<?php

namespace HttpMessages\Services;

use HttpMessages\Services\RequestService;
use HttpMessages\Services\ResponseService;
use FastRoute\RouteCollector as Router;
use FastRoute\Dispatcher as Dispatcher;
use Relay\Runner;
use HttpMessages\Exceptions\HttpMessagesException;

class DispatcherService
{
    /**
     * Request Service
     *
     * @var HttpMessages\Services\RequestService
     */
    protected $request_service;

    /**
     * Response Service
     *
     * @var HttpMessages\Services\ResponseService
     */
    protected $response_service;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->request_service  = new RequestService;
        $this->response_service = new ResponseService;
    }

    /**
     * Dispatch
     *
     * @param array $variables Variables
     *
     * @return void
     */
    public function dispatch()
    {
        $request = $this->request_service->getRequest();
        $response = $this->response_service->getResponse();

        try {

            $runner = new Runner(craft()->config->get('middleware', 'httpMessages'), function ($class) {
                return new $class();
            });

            $response = $runner($request, $response);

            $response->send();

        } catch (HttpMessagesException $exception) {

            $response
                ->writeToBody($exception->getMessage())
                ->withStatus($exception->getStatusCode(), $exception->getStatusPhrase())
                ->send();

        } catch (\Craft\Exception $craftException) {

            $response
                ->writeToBody($craftException->getMessage())
                ->withStatus(404)
                ->send();

        }

    }

}
