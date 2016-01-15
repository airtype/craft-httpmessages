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

        $router = $this->addRoutes($router);

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
     * Add Routes
     *
     * @param RouteCollection $router Router
     */
    private function addRoutes(RouteCollection $router)
    {
        foreach ($this->routes as $pattern => $http_methods) {
            foreach ($http_methods as $method => $middleware) {
                $router = $this->addRoute($router, $method, $pattern, $middleware);
            }
        }

        return $router;
    }

    /**
     * Add Route
     *
     * @param RouteCollection $router     Router
     * @param string          $method     Method
     * @param string          $pattern    Pattern
     * @param string          $middleware Middleware
     */
    private function addRoute(RouteCollection $router, $method, $pattern, $middleware)
    {
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

        return $router;
    }

}
