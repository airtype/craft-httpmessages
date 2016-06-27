<?php

namespace Craft;

use Craft\HttpMessages_CraftRequest as Request;
use Craft\HttpMessages_CraftResponse as Response;

class HttpMessages_KernelMiddleware
{
    /**
     * Controller
     *
     * @var mixed
     */
    protected $controller;

    /**
     * Method
     *
     * @var mixed
     */
    protected $method;

    /**
     * Attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Set Controller
     *
     * @param mixed $controller Controller
     *
     * @return HttpMessages_KernelMiddleware
     */
    public function setController($controller)
    {
        $this->controller = (is_string($controller)) ? new $controller : $controller;

        return $this;
    }

    /**
     * Set Method
     *
     * @param string $method Method
     *
     * @return HttpMessages_KernelMiddleware
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Set Attributes
     *
     * @param array $attributes Attributes
     *
     * @return HttpMessages_KernelMiddleware
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * __invoke Magic Method
     *
     * @param Request  $request  Request
     * @param Response $response Response
     * @param callable $next     Next
     *
     * @return Response Response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $request = $request->withAttributes($this->attributes);

        $response = $this->controller->{$this->method}($request, $response);

        return $response;
    }
}
