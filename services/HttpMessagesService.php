<?php

namespace Craft;

use Psr7Middlewares\Middleware;

use Relay\RelayBuilder;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Stream;

use Relay\Runner;

class HttpMessagesService extends BaseApplicationComponent
{
    /**
     * Get Routes
     *
     * @return array Routes
     */
    public function handle($request, $response)
    {
        $relay = new RelayBuilder();

        $globalMiddleware = \Craft\craft()->config->get('globalMiddleware', 'httpmessages');
        $routeMiddleware = $request->getRoute()->getMiddleware();

        $dispatcher = $relay->newInstance(array_merge($globalMiddleware, $routeMiddleware));

        return $dispatcher($request, $response);
    }

}
