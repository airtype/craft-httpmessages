<?php

namespace HttpMessages\Services;

use HttpMessages\Http\CraftRequest as Request;
use HttpMessages\Http\CraftResponse as Response;
use League\Route\RouteCollection;
use HttpMessages\Http\Route;
use Relay\Runner;
use League\Route\Http\Exception\NotFoundException;
use HttpMessages\Exceptions\HttpMessagesException;

class RouterService
{
    /**
     * Routes
     *
     * @var array
     */
    protected $routes;

    /**
     * Registered Middleware
     *
     * @var array
     */
    protected $registered_middleware;

    /**
     * Constructor
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Handle
     *
     * @param Request  $request  Request
     * @param Response $response Response
     *
     * @return void
     */
    public function handle(Request $request, Response $response)
    {
        $router = new RouteCollection();

        foreach ($this->routes as $pattern => $http_methods) {
            foreach ($http_methods as $method => $middleware) {
                $route = new Route($method, $pattern, $middleware);

                $router->addRoute($method, $route->getPattern(), function (Request $request, Response $response, $attributes) use ($route) {
                    $request = $request->withRoute($route);
                    $request = $request->withAttributes($attributes);

                    $runner = new Runner($route->getMiddlewareClasses(), function ($class) {
                        return new $class();
                    });

                    $response = $runner($request, $response);

                    return $response;
                });
            }
        }

        $dispatcher = $router->getDispatcher($request);

        try {
            $response = $dispatcher->handle($request, $response);

            $response->send();
        } catch (NotFoundException $exception) {

        } catch (HttpMessagesException $exception) {

            $response
                ->writeToBody($exception->getMessage(), 'json')
                ->withStatus($exception->getStatusCode(), $exception->getStatusPhrase())
                ->send();

        } catch (\Craft\Exception $craftException) {

            $response
                ->writeToBody($craftException->getMessage(), 'json')
                ->withStatus(404)
                ->send();

        }
    }

    /**
     * Get Middleware
     *
     * @param array $config Config
     *
     * @return array Middleware
     */
    public function getMiddlewareClasses(array $middleware)
    {
        if (empty($middleware)) {
            $exception = new HttpMessagesException();
            $exception->setMessage('No middleware is defined for this route.');
            throw $exception;
        }

        foreach (array_keys($middleware) as $key) {
            if (isset($this->registered_middleware[$key])) {
                $middleware[$key] = $this->registered_middleware[$key];
            } else {
                $exception = new HttpMessagesException();
                $exception->setMessage(sprintf('Middleware `%s` is not registered.', $key));
                throw $exception;
            }
        }

        return $middleware;
    }

}
