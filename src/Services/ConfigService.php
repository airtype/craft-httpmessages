<?php

namespace HttpMessages\Services;

use HttpMessages\Exceptions\HttpMessagesException;

class ConfigService
{
    /**
     * Http Methods
     *
     * @var array
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

        $routes = [];

        foreach (craft()->plugins->call('registerHttpMessagesRoutes', $routes) as $plugin => $plugin_routes) {
            $routes = \CMap::mergeArray($routes, $plugin_routes);
        }

        $routes = array_merge($routes, craft()->config->get('routes', 'httpMessages'));

        foreach ($routes as $pattern => $http_methods) {
            foreach ($http_methods as $http_method => $middlewares) {
                foreach (array_keys($middlewares) as $handle) {
                    $routes[$pattern][$http_method]['middleware'][$handle] = $registered_middleware[$handle];
                }
            }
        }

        return $routes;
    }

    /**
     * Get Registered Middleware
     *
     * @return array Registered Middleware
     */
    private function getRegisteredMiddleWare()
    {
        $middleware = [];

        foreach (craft()->plugins->call('registerHttpMessagesMiddlewareHandle', $middleware) as $plugin => $handle) {
            $middleware = \CMap::mergeArray($middleware, [$plugin => ['handle' => $handle]]);
        }

        foreach (craft()->plugins->call('registerHttpMessagesMiddlewareClass', $middleware) as $plugin => $class) {
            $middleware = \CMap::mergeArray($middleware, [$plugin => ['class' => $class]]);
        }

        foreach ($middleware as $plugin => $values) {
            $middleware[$values['handle']] = $values['class'];
            unset($middleware[$plugin]);
        }

        $middleware = array_merge($middleware, craft()->config->get('registeredMiddleware', 'httpmessages'));

        return $middleware;
    }

}
