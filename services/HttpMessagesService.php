<?php

namespace Craft;

use Relay\RelayBuilder;

class HttpMessagesService extends BaseApplicationComponent
{
    /**
     * Get Request
     *
     * @param array $routeVariables Route Variables
     *
     * @return HttpMessages_CraftRequest Request
     */
    public function getRequest(array $routeVariables = [])
    {
        $route = craft()->httpMessages_routes->getRoute($routeVariables);

        $serverRequest = \Zend\Diactoros\ServerRequestFactory::fromGlobals();
        $request = HttpMessages_RequestFactory::fromRequest($serverRequest);

        return $request->withRoute($route);
    }

    /**
     * Get Response
     *
     * @return HttpMessages_CraftResponse Response
     */
    public function getResponse()
    {
        $response = new \Zend\Diactoros\Response;

        return HttpMessages_ResponseFactory::fromResponse($response);
    }

    /**
     * Handle
     *
     * @param Request $request Request
     * @param Response $response Response
     *
     * @return Response Response
     */
    public function handle(HttpMessages_CraftRequest $request, HttpMessages_CraftResponse $response)
    {
        $relay = new RelayBuilder();

        $globalMiddleware = \Craft\craft()->config->get('globalMiddleware', 'httpmessages');
        $routeMiddleware = $request->getRoute()->getMiddleware();

        $dispatcher = $relay->newInstance(array_merge($globalMiddleware, $routeMiddleware));

        return $dispatcher($request, $response);
    }

}
