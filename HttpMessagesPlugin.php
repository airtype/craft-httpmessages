<?php

namespace Craft;

Craft::import('plugins.httpmessages.vendor.autoload', true);

use HttpMessages\Services\RouterService;
use HttpMessages\Services\RequestService;
use HttpMessages\Services\ResponseService;
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
        $request_service  = new RequestService;
        $request = $request_service->getRequest();

        $response_service = new ResponseService;
        $response = $response_service->getResponse();

        $routes = craft()->config->get('routes', 'httpMessages');
        $router_service = new RouterService($routes);
        $router_service->handle($request, $response);
    }

}
