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
     * Constructor
     *
     * @param string $method  Method
     * @param string $pattern Pattern
     * @param array  $config  Config
     */
    public function __construct($method, $pattern, array $middleware = [])
    {
        $this->method     = $method;
        $this->pattern    = $pattern;
        $this->middleware = $middleware;
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
     * Get Pattern
     *
     * @return string Pattern
     */
    public function getPattern()
    {
        return $this->pattern;
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

}
