<?php

namespace Craft;

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
     * Register Site Routes
     *
     * @return array Site Routes
     */
    public function registerSiteRoutes()
    {
        $routes = craft()->httpMessages_routes->getRoutes();

        return $routes;
    }

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        Craft::import('plugins.httpmessages.middleware.*', true);
        Craft::import('plugins.httpmessages.etc.*.*', true);

        $autoload = dirname(__FILE__) . '/../../../vendor/autoload.php';

        if (!is_file($autoload)) {
            throw new HttpMessages_Exception("`/vendor/autoload.php` could not be loaded by `craft/plugins/httpmessages/HttpMessagesPlugin.php`. Try running `composer install` in the root of the project.");
        }

        require_once($autoload);

        craft()->httpMessages_config->loadConfigFiles();
    }

}
