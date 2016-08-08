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
        $request = craft()->httpMessages->getRequest($variables);
        $response = craft()->httpMessages->getResponse();

        $response = craft()->httpMessages->handle($request, $response);

        $response->send();
    }

}
