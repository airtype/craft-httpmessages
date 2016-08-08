<?php
namespace Craft;

class HttpMessages_RoutesService extends BaseApplicationComponent
{
    /**
     * Get Craft Routes
     *
     * @return array Craft Routes
     */
    public function getCraftRoutes()
    {
        $routes = [];

        foreach (craft()->plugins->call('registerHttpMessagesRoutes', $routes) as $plugin => $plugin_routes) {
            $routes = \CMap::mergeArray($routes, $plugin_routes);
        }

        $routes = array_merge($routes, craft()->httpMessages_config->getRoutes());

        $craft_routes = [];

        foreach ($routes as $pattern => $methods) {
            $pattern = ltrim($pattern, '/');

            $craft_routes[$pattern] = [
                'action' => 'httpMessages/handle',
                'params' => [
                    'variables' => [
                        'HttpMessages_methods' => $methods,
                        'HttpMessages_pattern' => $pattern,
                    ],
                ]
            ];
        }

        return $craft_routes;
    }

    /**
     * Get Route
     *
     * @param array $routeVariables Variables
     *
     * @return HttpMessages_Route HttpMessages Route
     */
    public function getRoute(array $routeVariables)
    {
        unset($routeVariables['matches']);

        $methods = $routeVariables['HttpMessages_methods'];
        unset($routeVariables['HttpMessages_methods']);

        $pattern = $routeVariables['HttpMessages_pattern'];
        unset($routeVariables['HttpMessages_pattern']);

        $requestMethod = craft()->request->getRequestType();

        $this->validateRouteRequestMethod($methods, $requestMethod);

        $middleware = $methods[$requestMethod];

        return new HttpMessages_Route($requestMethod, $pattern, $middleware, $routeVariables);
    }

    /**
     * Validate Route Request Method
     *
     * @param array  $methods Methods
     * @param string $method  Method
     *
     * @return void|HttpMessages_Exception
     */
    private function validateRouteRequestMethod(array $methods, $method)
    {
        $methods = array_change_key_case($methods, CASE_UPPER);

        if (!in_array(strtoupper($method), array_keys($methods))) {
            $methods = implode(', ', array_keys($methods));

            throw new HttpMessages_Exception("`$method` is not a defined method for `$pattern`. Possible methods include: `$methods`");
        }
    }

}
