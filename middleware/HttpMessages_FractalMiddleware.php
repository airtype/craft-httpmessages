<?php

namespace Craft;

use Craft\HttpMessages_CraftRequest as Request;
use Craft\HttpMessages_CraftResponse as Response;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class HttpMessages_FractalMiddleware
{
    /**
     * Config
     *
     * @var HttpMessages_ConfigCollection
     */
    public $config;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config = craft()->httpMessages_config->get('fractal', 'middleware');
    }

    /**
     * Invoke
     *
     * @return void
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        try {
            $response = HttpMessages_FractalResponseFactory::create($response);

            $response = $next($request, $response);

            $response = $this->applyFractal($request, $response);
        } catch (HttpMessages_Exception $exception) {
            $body = [
                'message' => $exception->getMessage(),
            ];

            if ($exception->getErrors()) {
                $body['errors'] = $exception->getErrors();
            }

            if ($exception->getInput()) {
                $body['input'] = $exception->getInput();
            }

            if ($this->config->get('devMode')) {
                $body['trace'] = $exception->getTrace();
            }

            $json = JsonHelper::encode($body);

            $response = $response
                ->withStatus($exception->getStatusCode(), $exception->getStatusPhrase())
                ->withJson($json);
        } catch(\CException $exception) {
            $body = [
                'message' => $exception->getMessage(),
            ];

            if ($this->config->get('devMode')) {
                $body['trace'] = $exception->getTrace();
            }

            $json = JsonHelper::encode($body);

            $response = $response
                ->withStatus(500)
                ->withJson($json);
        }

        return $response;
    }

    /**
     * Apply Fractal
     *
     * @param Request  $request  Request
     * @param Response $response Response
     *
     * @return Response Response
     */
    private function applyFractal(Request $request, Response $response)
    {
        $fractal = new Manager;
        $transformer = $this->getTransformer($request);

        // if (isset($this->includes)) {
        //     $this->manager->parseIncludes($this->includes);
        // }

        if ($response->getItem()) {
            if ($transformer) {
                $resource = new Item($response->getItem(), $transformer);
            } else {
                $resource = new Item($response->getItem());
            }
        }

        if (is_array($response->getCollection())) {
            if ($transformer) {
                $resource = new Collection($response->getCollection(), $transformer);
            } else {
                $resource = new Collection($response->getCollection());
            }

            if ($paginator = $this->getPaginator($response)) {
                $resource->setPaginator($paginator);
            }
        }

        if (isset($resource)) {
            $serializer = $this->getSerializer($request);
            $fractal->setSerializer($serializer);

            $resource = $fractal->createData($resource)->toArray();

            $json = JsonHelper::encode($resource);

            $response = $response->withJson($json);
        }

        return $response;
    }

    /**
     * Get Transformer
     *
     * @param Request $request Request
     *
     * @return Transformer Transformer
     */
    private function getTransformer(Request $request)
    {
        $transformer = $request->getRoute()->getMiddlewareVariable('transformer', 'fractal');

        return ($transformer) ? new $transformer : null;
    }

    /**
     * Get Serializer
     *
     * @param Request $request Request
     *
     * @return Serializer Serializer
     */
    private function getSerializer(Request $request)
    {
        $serializers = $this->config->get('serializers');

        $default_serializer = $this->config->get('defaultSerializer');

        if (!isset($serializers[$default_serializer])) {
            $exception = new HttpMessages_Exception();

            $exception->setMessage(sprintf('The default serializer `%s` does not exist.', $default_serializer));

            throw $exception;
        }

        $serializer = $serializers[$default_serializer];

        return new $serializer;
    }

    /**
     * Get Paginator
     *
     * @param Response $response Response
     *
     * @return Paginator Paginator
     */
    private function getPaginator(Response $response)
    {
        if(!$criteria = $response->getCriteria()) {
            return null;
        }

        if (!$criteria->limit) {
            return null;
        }

        if (!$paginator = $this->config->get('paginator')) {
            return null;
        }

        return new $paginator($criteria);
    }

}
