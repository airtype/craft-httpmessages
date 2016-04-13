<?php
namespace Craft;

class HttpMessages_RoutesService extends BaseApplicationComponent
{
    /**
     * Get Routes
     *
     * @return array Routes
     */
    public function getRoutes()
    {
        $routes = [];

        foreach (craft()->plugins->call('registerHttpMessagesRoutes', $routes) as $plugin => $plugin_routes) {
            $routes = \CMap::mergeArray($routes, $plugin_routes);
        }

        $routes = array_merge($routes, craft()->httpMessages_config->getRoutes());

        $craft_routes = [];

        foreach ($routes as $pattern => $config) {
            $pattern = ltrim($pattern, '/');

            $craft_routes[$pattern] = [
                'action' => 'httpMessages/handle',
                'params' => [
                    'variables' => [
                        'httpMessagesConfig' => $config,
                        'pattern' => $pattern,
                    ],
                ]
            ];
        }

        return $craft_routes;
    }

}
