<?php

namespace Craft;

require_once('functions.php');
require_once(dirname(__FILE__).'/../../../vendor/autoload.php');

use HttpMessages\Factories\RequestFactory;
use HttpMessages\Factories\ResponseFactory;
use HttpMessages\Services\ConfigService as HttpMessageConfigService;
use HttpMessages\Services\RouterService;
use HttpMessages\Http\CraftRouter as Router;
use RestfulApi\Exceptions\RestfulApiException;

class HttpMessagesPlugin extends BasePlugin
{
    /**
     * Get Name
     *
     * @return string Name
     */
    public function getName()
    {
         return Craft::t('Http Messages');
    }

    /**
     * Get Version
     *
     * @return string Version
     */
    public function getVersion()
    {
        return '0.0.0';
    }

    /**
     * Get Developer
     *
     * @return string Developer
     */
    public function getDeveloper()
    {
        return 'Airtype';
    }

    /**
     * Get Developer Url
     *
     * @return string Developer Url
     */
    public function getDeveloperUrl()
    {
        return 'http://airtype.com';
    }

    /**
     * Register Http Messages Routes
     *
     * @return array Routes
     */
    public function registerHttpMessagesRoutes()
    {
        return craft()->config->get('routes', 'httpMessages');
    }

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        $request = RequestFactory::create();
        $response = ResponseFactory::create();

        $config_service = new HttpMessageConfigService();
        $routes = $config_service->getRoutes();

        $router_service = new RouterService($routes);
        $router_service->handle($request, $response);
    }

}


