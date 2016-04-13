<?php

namespace Craft;

class HttpMessages_MiddlewareService extends BaseApplicationComponent
{
    /**
     * Registered Middleware
     *
     * @var array
     */
    protected $registeredMiddleware = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->registeredMiddleware = $this->getRegisteredMiddleware();
    }

    /**
     * Get Middleware Class By Handle
     *
     * @param array $middlewareHandles Middleware Handles
     *
     * @return array Middleware Classes
     */
    public function getMiddlewareClassesByHandles(array $middlewareHandles)
    {
        $middlewareClasses = [];

        foreach ($middlewareHandles as $middlewareHandle) {
            if (!$middlewareClass = $this->getMiddlewareClassByHandle($middlewareHandle)) {
                dd("`$middlewareHandle` middleware not found.");
            }

            $middlewareClasses[] = $middlewareClass;
        }

        array_push($middlewareClasses, $this->registeredMiddleware['kernel']);

        return $middlewareClasses;
    }

    /**
     * Get Middleware Class By Handle
     *
     * @param array $middlewareHandle Middleware Handle
     *
     * @return string|null Middleware Class
     */
    public function getMiddlewareClassByHandle($middlewareHandle)
    {
        return (isset($this->registeredMiddleware[$middlewareHandle])) ? $this->registeredMiddleware[$middlewareHandle] : null;
    }

    /**
     * Get Registered Middleware
     *
     * @return array Registered Middleware
     */
    private function getRegisteredMiddleware()
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

        if($default_middleware = craft()->config->get('registeredMiddleware', 'httpmessages')) {
            $middleware = array_merge($middleware, $default_middleware);
        }

        if (!array_key_exists('kernel', $middleware)) {
            $middleware['kernel'] = 'Craft\\HttpMessages_KernelMiddleware';
        }

        return $middleware;
    }

}
