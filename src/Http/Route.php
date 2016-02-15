<?php

namespace HttpMessages\Http;

class Route
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
     * Constructor
     *
     * @param array $config Config
     */
    public function __construct($method, $pattern, array $middleware)
    {
        $this->method     = $method;
        $this->pattern    = $pattern;
        $this->middleware = $middleware;
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
     * Get Middleware Classes
     *
     * @return array Middleware Classes
     */
    public function getMiddlewareClasses()
    {
        return $this->middleware['middleware'];
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
