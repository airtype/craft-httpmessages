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
        $this->method  = $method;
        $this->pattern = $pattern;
        $this->middleware  = $middleware;
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
        $classes = [];

        foreach ($this->middleware as $middleware) {
            $classes[] = $middleware['class'];
        }

        return $classes;
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
        return $this->middleware[$middleware]['variables'][$key];
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
