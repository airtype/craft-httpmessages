<?php

namespace Craft;

use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Stream;

class HttpMessages_RequestService extends BaseApplicationComponent
{
    /**
     * Craft Request
     *
     * @var HttpMessages_CraftRequest
     */
    protected $craftRequest;

    /**
     * Constructor
     */
    public function __construct()
    {
        $serverRequest = ServerRequestFactory::fromGlobals();

        $this->craftRequest = HttpMessages_CraftRequestFactory::fromServerRequest($serverRequest);
    }

    /**
     * Get Craft Request
     *
     * @param array  $routeMethods   Route Methods
     * @param string $routePattern   Route Pattern
     * @param array  $routeVariables Route Variables
     *
     * @return HttpMessages_CraftRequest Craft Request
     */
    public function getCraftRequest(array $routeMethods, $routePattern, array $routeVariables = [])
    {
        $requestMethod = $this->craftRequest->getMethod();

        $this->validateRouteByType($routeMethods, $requestMethod, $routePattern);

        $routeConfig = $routeMethods[$requestMethod];

        $this->addAttributesToRequest($routeConfig, $routeVariables);

        $this->addRouteToRequest($requestMethod, $routePattern, $routeConfig);

        return $this->craftRequest;
    }

    /**
     * Add Attributes To Request
     *
     * @param array $routeConfig    Route Config
     * @param array $routeVariables Route Variables
     */
    private function addAttributesToRequest(array $routeConfig, array $routeVariables)
    {
        if (isset($routeConfig['variables']) && is_array($routeConfig['variables'])) {
            $routeVariables = array_merge($routeVariables, $routeConfig['variables']);
        }

        $this->craftRequest = $this->craftRequest->withAttributes($routeVariables);
    }

    /**
     * Add Route to Request
     *
     * @param string $requestMethod Request Method
     * @param string $routePattern  Route Pattern
     * @param array  $routeConfig   Route Config
     */
    private function addRouteToRequest($requestMethod, $routePattern, array $routeConfig)
    {
        $route = new HttpMessages_Route($requestMethod, $routePattern, $routeConfig);

        $this->craftRequest = $this->craftRequest->withRoute($route);
    }

    /**
     * Validate By Route Type
     *
     * @param array  $routeMethods  Route Methods
     * @param string $requestMethod Request Method
     * @param string $routePattern  Route Pattern
     *
     * @return void
     */
    private function validateRouteByType($routeMethods, $requestMethod, $routePattern)
    {
        $routeMethods = array_change_key_case($routeMethods, CASE_UPPER);

        if (!in_array(strtoupper($requestMethod), array_keys($routeMethods))) {
            $arrayKeysString = implode(', ', array_keys($routeMethods));

            throw new HttpMessages_Exception("`$requestMethod` is not a in the config options for `$routePattern`. Possible methods include: `$arrayKeysString`");
        }
    }

}
