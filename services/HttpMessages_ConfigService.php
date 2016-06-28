<?php

namespace Craft;

class HttpMessages_ConfigService extends BaseApplicationComponent
{
    /**
     * Routes
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Middleware
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * Load Config Files
     *
     * @return void
     */
    public function loadConfigFiles()
    {
        $this->middleware = $this->loadMiddlewareConfigs();

        $this->routes = $this->loadRouteConfigs();
    }

    /**
     * Load Route Configs
     *
     * @return array Routes
     */
    private function loadRouteConfigs()
    {
        // Load routes found within the `craft/plugins/etc/config/routes`
        $defaultRouteConfigs = $this->loadConfigsFromDirectory(dirname(__FILE__) . '/../etc/config/routes');

        // Load routes found within the `craft/config/httpmessages/routes`
        $userRouteConfigs = $this->loadConfigsFromDirectory(CRAFT_CONFIG_PATH . 'httpmessages/routes');

        $mergedConfigs = \CMap::mergeArray($defaultRouteConfigs, $userRouteConfigs);

        // Plugins can register routes. Load any routes from plugins `craft/plugins/*/routes`
        foreach (craft()->plugins->call('registerHttpMessagesApplicationHandle') as $plugin => $application) {
            $applicationRoutes = $this->loadConfigsFromDirectory(CRAFT_CONFIG_PATH . $application . '/routes');

            $mergedConfigs = \CMap::mergeArray($mergedConfigs, $applicationRoutes);
        }

        return $this->normalizeMergedConfigs($mergedConfigs);
    }

    /**
     * Normalize Merged Route Configs
     *
     * @param array $mergedConfigs Merged Configs
     *
     * @return array Routes
     */
    private function normalizeMergedConfigs($mergedConfigs)
    {
        $routes = [];

        foreach ($mergedConfigs as $route) {
            foreach($route as $key => $value) {
                $routes[$key] = $value;
            }
        }

        return $routes;
    }

    /**
     * Load Middleware Configs
     *
     * @return arrat Middleware Configs
     */
    private function loadMiddlewareConfigs()
    {
        $defaultMiddleWareConfigs = $this->loadConfigsFromDirectory(dirname(__FILE__) . '/../etc/config/middleware');

        $userMiddlewareConfigs = $this->loadConfigsFromDirectory(CRAFT_CONFIG_PATH . 'httpmessages/middleware');

        $mergedConfig = $this->mergeConfigs($defaultMiddleWareConfigs, $userMiddlewareConfigs);

        return $mergedConfig;
    }

    /**
     * Load Configs From Directory
     *
     * @param string $directory Directory
     *
     * @return array Configs
     */
    private function loadConfigsFromDirectory($directory)
    {
        $configs = [];

        if (!$files = IOHelper::getFiles($directory)) {
            return $configs;
        }

        foreach ($files as $file) {
            $fileName = IOHelper::getFileName($file, false);

            $configs[$fileName] = require($file);
        }

        return $configs;
    }

    /**
     * Merge Configs
     *
     * @param array $defaultConfigs Default Configs
     * @param array $userConfigs    User Configs
     *
     * @return array Merged Configs
     */
    private function mergeConfigs($defaultConfigs, $userConfigs)
    {
        $mergedConfig = \CMap::mergeArray($defaultConfigs, $userConfigs);

        return $this->convertToConfigCollections($mergedConfig);
    }

    /**
     * Convert to Config Collections
     *
     * @param array $config Config
     *
     * @return array Configs
     */
    private function convertToConfigCollections(array $config)
    {
        foreach ($config as $key => $value) {
            $attributeCollection = new HttpMessages_ConfigCollection($value, true);

            $config[$key] = $attributeCollection;
        }

        return $config;
    }

    /**
     * Get
     *
     * @param string $key     Key
     * @param string $context Context
     *
     * @return mixed Value
     */
    public function get($key, $context, $default = null)
    {
        return isset($this->{$context}[$key]) ? $this->{$context}[$key] : $default;
    }

    /**
     * Get Routes
     *
     * @return array Routes
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Get Middleware
     *
     * @return array Middleware
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

}
