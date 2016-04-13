<?php

namespace Craft;

class HttpMessages_Route
{
    /**
     * Method
     *
     * @var string
     */
    protected $method;

    /**
     * Pattern
     *
     * @var string
     */
    protected $pattern;

    /**
     * Config
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * Middleware Classes
     *
     * @var array
     */
    protected $middlewareClasses = [];

    /**
     * Constructor
     *
     * @param string $method  Method
     * @param string $pattern Pattern
     * @param array  $config  Config
     */
    public function __construct($method, $pattern, array $config)
    {
        $this->method     = $method;
        $this->pattern    = $pattern;

        $this->middleware = isset($config['middleware']) ? $config['middleware'] : null;

        if (!isset($config['controller'])) {
            throw new HttpMessages_Exception("A controller must be defined in the `$method $pattern` route definition.");
        }

        $this->controller = $config['controller'];

        if (!isset($config['method'])) {
            throw new HttpMessages_Exception("A method must be defined in the `$method $pattern` route definition.");
        }

        $this->method = $config['method'];
    }

    /**
     * Get Middleware
     *
     * @return null|array Middleware
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * Set Middleware
     *
     * @param array $middleware Middleware
     */
    public function setMiddleware(array $middleware)
    {
        $this->middleware = $middleware;
    }

    /**
     * Get Controller
     *
     * @return string Controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Get Method
     *
     * @return string Method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get Middleware Classes
     *
     * @return array Middleware Classes
     */
    public function getMiddlewareClasses()
    {
        $middlewareHandles = [];

        foreach ($this->middleware as $key => $value) {
            if (is_string($value)) {
                $middlewareHandles[] = $value;
            }

            if (is_array($value)) {
                $middlewareHandles[] = $key;
            }
        }

        return craft()->httpMessages_middleware->getMiddlewareClassesByHandles($middlewareHandles);
    }

    /**
     * Get Middleware Variable
     *
     * @param string $key        Key
     * @param string $middleware Middleware
     *
     * @return mixed Middleware Variable
     */
    public function getMiddlewareVariable($key, $middleware)
    {
        if (!array_key_exists($middleware, $this->middleware)) {
            return null;
        }

        return array_key_exists($key, $this->middleware[$middleware]) ? $this->middleware[$middleware][$key] : null;
    }

    /**
     * Get Middleware Config
     *
     * @param string $middleware Middleware
     *
     * @return array Middleware Config
     */
    public function getMiddlewareConfig($middleware)
    {
        return $this->middleware[$middleware];
    }

    /**
     * Get Pattern
     *
     * @return string Pattern
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Is Pattern
     *
     * @param string $pattern String
     *
     * @return boolean
     */
    public function is($method, $pattern)
    {
        return $this->method === $method && $this->pattern === $pattern;
    }
}
