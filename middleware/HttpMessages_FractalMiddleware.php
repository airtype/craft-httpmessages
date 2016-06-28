<?php

namespace Craft;

use Craft\HttpMessages_CraftRequest as Request;
use Craft\HttpMessages_CraftResponse as Response;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use League\Fractal\Serializer\SerializerAbstract;
use League\Fractal\Pagination\PaginatorInterface;

class HttpMessages_FractalMiddleware
{
    /**
     * Config
     *
     * @var HttpMessages_ConfigCollection
     */
    public $config;

    /**
     * Transformer
     *
     * @var League\Fractal\TransformerAbstract
     */
    public $transformer;

    /**
     * Serializer
     *
     * @var League\Fractal\Serializer\SerializerAbstract
     */
    public $serializer;

    /**
     * Paginator
     *
     * @var League\Fractal\Pagination\PaginatorInterface
     */
    public $paginator;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config = craft()->httpMessages_config->get('fractal', 'middleware');

        $defaultSerializer = $this->config->get('defaultSerializer');
        $serializers = $this->config->get('serializers');

        if ($defaultSerializer && $serializers && isset($serializers[$defaultSerializer])) {
            $serializer = $serializers[$defaultSerializer];

            $this->setSerializer($serializer);
        }

        if ($paginator = $this->config->get('paginator')) {
            $this->setPaginator($paginator);
        }
    }

    /**
     * With Transformer
     *
     * @param mixed $transformer Transformer
     *
     * @return HttpMessages_FractalMiddleware
     */
    public function setTransformer($transformer)
    {
        if (is_string($transformer)) {
            $transformer = new $transformer;
        }

        if (!$transformer instanceof TransformerAbstract) {
            throw new HttpMessages_Exception('Transformer is not an instance of `League\Fractal\TransformerAbstract`.');
        }

        $this->transformer = $transformer;

        return $this;
    }

    /**
     * Get Transformer
     *
     * @return TransformerAbstract Transformer
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * With Serializer
     *
     * @param mixed $serializer Serializer
     *
     * @return HttpMessages_FractalMiddleware
     */
    public function setSerializer($serializer)
    {
        if (is_string($serializer)) {
            $serializer = new $serializer;
        }

        if (!$serializer instanceof SerializerAbstract) {
            throw new HttpMessages_Exception('Serializer is not an instance of `League\Fractal\Serializer\SerializerAbstract`.');
        }

        $this->serializer = $serializer;

        return $this;
    }

    /**
     * Get Serializer
     *
     * @return SerializerAbstract Serializer
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * With Paginator
     *
     * @param mixed $paginator Paginator
     *
     * @return HttpMessages_FractalMiddleware
     */
    public function setPaginator($paginator)
    {
        if (is_string($paginator)) {
            $paginator = new $paginator;
        }

        if (!$paginator instanceof PaginatorInterface) {
            throw new HttpMessages_Exception('Paginator is not an instance of `League\Fractal\Pagination\PaginatorInterface`.');
        }

        $this->paginator = $paginator;

        return $this;
    }

    /**
     * Get Paginator
     *
     * @return PaginatorInterface Paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * __invoke Magic Method
     *
     * @param Request  $request  Request
     * @param Response $response Response
     * @param callable $next     Next
     *
     * @return Response Response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        try {
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

        // if (isset($this->includes)) {
        //     $this->manager->parseIncludes($this->includes);
        // }

        $transformer = $this->transformer;
        $paginator   = $this->paginator;
        $serializer  = $this->serializer;

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

            if ($paginator && $criteria = $response->getCriteria()) {
                $paginator = $this->setCriteriaOnPaginator($paginator, $criteria);

                $resource->setPaginator($paginator);
            }
        }

        if (isset($resource)) {
            $fractal->setSerializer($serializer);

            $resource = $fractal->createData($resource)->toArray();

            $json = JsonHelper::encode($resource);

            $response = $response->withJson($json);
        }

        return $response;
    }

    /**
     * Set Criteria On Paginator
     *
     * @param PaginatorInterface   $paginator Paginator
     * @param ElementCriteriaModel $criteria  Criteria
     */
    private function setCriteriaOnPaginator(PaginatorInterface $paginator, ElementCriteriaModel $criteria)
    {
        if (!$criteria->limit) {
            return $paginator;
        }

        return $paginator->setCriteria($criteria);
    }

}
