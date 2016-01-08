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
     * Middleware
     *
     * @var array
     */
    protected $middleware;

    /**
     * Config
     *
     * @var array
     */
    protected $config = [];

    /**
     * Constructor
     *
     * @param array $config Config
     */
    public function __construct($method, $pattern, array $config)
    {
        $this->method  = $method;
        $this->pattern = $pattern;
        $this->config  = $config;
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
     * Get Middleware Config
     *
     * @param string $key        Key
     * @param string $middleware Middleware
     *
     * @return mixed Middleware Config
     */
    public function getMiddlewareConfig($key, $middleware)
    {
        return $this->config['middleware'][$middleware][$key];
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
