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
        // Normalize variables
        unset($variables['matches']);

        $config = $variables['httpMessagesConfig'];
        unset($variables['httpMessagesConfig']);

        $pattern = $variables['pattern'];
        unset($variables['pattern']);

        // Validate the request type
        $requestType = craft()->request->getRequestType();

        $config = array_change_key_case($config, CASE_UPPER);

        if (!in_array(strtoupper($requestType), array_keys($config))) {
            $arrayKeysString = implode(', ', array_keys($config));

            throw new HttpMessages_Exception("`$requestType` is not a in the config options for `$pattern`. Possible methods include: `$arrayKeysString`");
        }

        $config = $config[$requestType];

        if (isset($config['variables']) && is_array($config['variables'])) {
            $variables = array_merge($variables, $config['variables']);
        }

        $request = HttpMessages_RequestFactory::create();

        $route = new HttpMessages_Route($requestType, $pattern, $config);

        $request = $request->withRoute($route);
        $request = $request->withAttributes($variables);

        $response = HttpMessages_ResponseFactory::create();

        $response = craft()->httpMessages->handle($request, $response);

        $response->send();
    }
}
