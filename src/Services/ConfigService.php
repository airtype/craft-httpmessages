<?php

namespace HttpMessages\Services;

use HttpMessages\Exceptions\HttpMessagesException;

class ConfigService
{
    /**
     * Http Methods
     *
     * @var [type]
     */
    protected $http_methods = [
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

    /**
     * Get Routes
     *
     * @return array Routes
     */
    public function getRoutes()
    {
        $registered_middleware = $this->getRegisteredMiddleWare();
        $registered_middleware_routes = $this->transformMiddlewares($registered_middleware);

        $routes = \Craft\craft()->config->get('routes', 'httpMessages');
        $routes = $this->transformRoutes($routes);

        $routes = $this->addMiddlewareVariablesToRoutes($routes, $registered_middleware, $registered_middleware_routes);

        return $routes;
    }

    /**
     * Get Registered Middleware
     *
     * @return void
     */
    private function getRegisteredMiddleWare()
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

        return $transformed_middleware;
    }

    /**
     * Transform Middlewares
     *
     * @param array $middlewares Middlewares
     *
     * @return array Routes
     */
    private function transformMiddlewares(array $middlewares)
    {
        $middleware = [];

        foreach ($middlewares as $handle => $config) {

            if (!$config['routes']) {
                continue;
            }

            $transformed_middleware = $this->transformMiddleware($config['routes'], $handle);

            $middleware = \CMap::mergeArray($middleware, $transformed_middleware);
        }

        return $middleware;
    }

    /**
     * Transform Middleware
     *
     * @param array  $routes Routes
     * @param string $handle Handle
     *
     * @return array Routes
     */
    private function transformMiddleware(array $routes, $handle)
    {
        $middleware = [];

        foreach ($routes as $pattern => $http_methods) {
            if (isset($http_methods['default'])) {
                $default = $http_methods['default'];
                unset($http_methods['default']);
            }

            $http_methods = array_change_key_case($http_methods, CASE_UPPER);

            if (isset($default)) {
                foreach ($this->http_methods as $http_method) {
                    if (!isset($http_methods[$http_method])) {
                        $http_methods[$http_method] = $default;
                    }
                }
            }

            $middleware[$pattern] = [
                $handle => $http_methods
            ];
        }

        return $middleware;
    }

    /**
     * Transform Routes
     *
     * @param array $routes Routes
     *
     * @return array Routes
     */
    private function transformRoutes(array $routes)
    {
        foreach ($routes as $pattern => $http_methods) {
            if (isset($http_methods['default'])) {
                $default = $http_methods['default'];
                unset($http_methods['default']);
            }

            $http_methods = array_change_key_case($http_methods, CASE_UPPER);

            if (isset($default)) {
                foreach ($this->http_methods as $http_method) {
                    if (!isset($http_methods[$http_method])) {
                        $http_methods[$http_method] = $default;
                    }
                }
            }

            $routes[$pattern] = $http_methods;
        }

        return $routes;
    }

    /**
     * Add Middleware Variables To Routes
     *
     * @param array $routes                       Routes
     * @param array $registered_middleware        Registered Middleware
     * @param array $registered_middleware_routes Registered Middleware Routes
     */
    private function addMiddlewareVariablesToRoutes(array $routes, array $registered_middleware, array $registered_middleware_routes)
    {
        foreach ($routes as $pattern => $methods) {
            foreach ($methods as $method => $middleware) {
                foreach ($middleware as $key => $handle) {
                    $routes[$pattern][$method][$handle]['class'] = $registered_middleware[$handle]['class'];

                    if ($middleware_variables = $this->getRegisteredMiddlewareVariables($registered_middleware, $registered_middleware_routes, $handle, $pattern, $method)) {
                        $routes[$pattern][$method][$handle]['variables'] = $middleware_variables;
                    }

                    unset($routes[$pattern][$method][$key]);
                }
            }
        }

        return $routes;
    }

    /**
     * Get Registered Middleware Variables
     *
     * @param array $registered_middleware        Registered Middleware
     * @param array $registered_middleware_routes Registered Middleware Routes
     * @param string $handle                      Handle
     * @param string $pattern                     Pattern
     * @param string $method                      Method
     *
     * @return array Variables
     */
    private function getRegisteredMiddlewareVariables(array $registered_middleware, array $registered_middleware_routes, $handle, $pattern, $method)
    {
        if (!array_key_exists($handle, $registered_middleware)) {
            $exception = new HttpMessagesException();
            $exception->setMessage(sprintf('Middleware with the handle `%s` is not defined.', $handle));
            throw $exception;
        }

        if (!isset($registered_middleware_routes[$pattern][$handle][$method])) {
            return [];
        }

        return $registered_middleware_routes[$pattern][$handle][$method];
    }

}
