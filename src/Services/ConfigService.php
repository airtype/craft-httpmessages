<?php

namespace HttpMessages\Services;

use HttpMessages\Http\CraftRequest as Request;
use HttpMessages\Exceptions\HttpMessagesException;

class ConfigService
{
    /**
     * Request
     *
     * @var HttpMessages\Http\CraftRequest
     */
    protected $request;

    /**
     * Registered Middleware
     *
     * @var array
     */
    protected $registered_middleware = [];

    protected $registered_middleware_routes;

    /**
     * Routes
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Construct
     *
     * @param Request $request Request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->registerMiddleware();
        $this->mergeMiddlewareConfigs();
    }

    /**
     * Register Middleware
     *
     * @return void
     */
    private function registerMiddleWare()
    {
        $middleware = [];

        foreach (\Craft\craft()->plugins->call('registerHttpMessagesMiddlewareHandle', $middleware) as $plugin => $handle) {
            $middleware = \CMap::mergeArray($middleware, [$plugin => ['handle' => $handle]]);
        }

        foreach (\Craft\craft()->plugins->call('registerHttpMessagesMiddlewareClass', $middleware) as $plugin => $class) {
            $middleware = \CMap::mergeArray($middleware, [$plugin => ['class' => $class]]);
        }

        foreach ($middleware as $plugin => $values) {
            $transformed_middleware[$values['handle']]['class'] = $values['class'];
            $transformed_middleware[$values['handle']]['routes'] = \Craft\craft()->config->get('routes', $plugin);
        }

        $this->registered_middleware = $transformed_middleware;
    }

    /**
     * Merge Middleware Configs
     *
     * @return void
     */
    private function mergeMiddlewareConfigs()
    {
        $routes = [];

        $config_routes = \Craft\craft()->config->get('routes', 'httpMessages');

        $transformed_middleware_routes = [];

        foreach ($this->registered_middleware as $handle => $registered_middleware) {

            if (!$registered_middleware['routes']) {
                continue;
            }

            $transformed_routes = $this->transformRoutes($registered_middleware['routes'], $handle);

            $transformed_middleware_routes = \CMap::mergeArray($transformed_middleware_routes, $transformed_routes);
        }

        $this->registered_middleware_routes = $transformed_middleware_routes;

        $config_routes = $this->transformRoutes($config_routes);

        foreach ($config_routes as $pattern => $methods) {
            foreach ($methods as $method => $middleware) {
                foreach ($middleware as $key => $handle) {
                    $config_routes[$pattern][$method][$handle]['class'] = $this->registered_middleware[$handle]['class'];

                    if ($middleware_variables = $this->getRegisteredMiddlewareVariables($handle, $pattern, $method)) {
                        $config_routes[$pattern][$method][$handle]['variables'] = $middleware_variables;
                    }

                    unset($config_routes[$pattern][$method][$key]);
                }
            }
        }

        $this->routes = $config_routes;
    }

    private function transformRoutes(array $config_routes, $handle = null)
    {
        $http_methods = [
            'GET',
            'POST',
            'PUT',
            'PATCH',
            'DELETE',
            'COPY',
            'HEAD',
            'OPTIONS',
            'LINK',
            'UNLINK',
            'PURGE',
            'LOCK',
            'UNLOCK',
            'PROPFIND',
            'VIEW',
        ];

        foreach ($config_routes as $pattern => $config_http_methods) {
            if (isset($config_http_methods['default'])) {
                $default_middleware = $config_http_methods['default'];
                unset($config_http_methods['default']);
            }

            $config_http_methods = array_change_key_case($config_http_methods, CASE_UPPER);

            if (isset($default_middleware)) {
                foreach ($http_methods as $http_method) {
                    if (!isset($config_http_methods[$http_method])) {
                        $config_http_methods[$http_method] = $default_middleware;
                    }
                }
            }

            if ($handle) {
                $config_routes[$pattern] = [$handle => $config_http_methods];
            } else {
                $config_routes[$pattern] = $config_http_methods;
            }

        }

        return $config_routes;
    }

    private function getRegisteredMiddlewareVariables($handle, $pattern, $method)
    {
        if (!array_key_exists($handle, $this->registered_middleware)) {
            $exception = new HttpMessagesException();
            $exception->setMessage(sprintf('Middleware with the handle `%s` is not defined.', $handle));
            throw $exception;
        }

        if (!isset($this->registered_middleware_routes[$pattern][$handle][$method])) {
            return null;
        }

        return $this->registered_middleware_routes[$pattern][$handle][$method];
    }

    public function getRoutes()
    {
        return $this->routes;
    }
}
