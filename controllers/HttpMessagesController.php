<?php

namespace Craft;

class HttpMessagesController extends BaseController
{
    /**
     * Allow Anonymous
     *
     * @var boolean
     */
    protected $allowAnonymous = true;

    /**
     * Action Handle
     *
     * @param array $variables Variables
     *
     * @return void
     */
    public function actionHandle(array $variables = [])
    {
        unset($variables['matches']);

        $routeMethods = $variables['routeMethods'];
        unset($variables['routeMethods']);

        $routePattern = $variables['routePattern'];
        unset($variables['routePattern']);

        $request = craft()->httpMessages_request->getCraftRequest($routeMethods, $routePattern, $variables);

        $response = craft()->httpMessages_response->getCraftResponse();

        $response = craft()->httpMessages->handle($request, $response);

        $response->send();
    }
}
