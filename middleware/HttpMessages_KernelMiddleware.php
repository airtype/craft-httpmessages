<?php

namespace Craft;

use Craft\HttpMessages_CraftRequest as Request;
use Craft\HttpMessages_CraftResponse as Response;

class HttpMessages_KernelMiddleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $controller = $request->getRoute()->getController();
        $method = $request->getRoute()->getMethod();

        $controller = new $controller();

        $response = $controller->$method($request, $response);

        return $response;
    }
}
