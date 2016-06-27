<?php

namespace Craft;

use Zend\Diactoros\ServerRequest;

class HttpMessages_CraftRequest extends ServerRequest
{
    /**
     * Criteria
     *
     * @var ElementCriteriaModel
     */
    protected $criteria;

    /**
     * Route
     *
     * @var HttpMessages_Route
     */
    protected $route;

    /**
     * Get Query Param
     *
     * @param string $key     Key
     * @param mixed  $default Default
     *
     * @return mixed Query Param
     */
    public function getQueryParam($key, $default = null)
    {
        $query_params = $this->getQueryParams();

        return isset($query_params[$key]) ? $query_params[$key] : $default;
    }

    /**
     * Get Params
     *
     * @return array Params
     */
    public function getParams()
    {
        return $this->getParsedBody();
    }

    /**
     * Get Param
     *
     * @param string $key     Key
     * @param mixed  $default Default
     *
     * @return mixed Param
     */
    public function getParam($key, $default = null)
    {
        $parsed_body = $this->getParsedBody();

        return isset($parsed_body[$key]) ? $parsed_body[$key] : $default;
    }

    /**
     * With Headers
     *
     * @param array $headers Headers
     *
     * @return CraftRequest Request
     */
    public function withHeaders(array $headers)
    {
        $new = clone $this;

        foreach ($headers as $header => $value) {
            $new = $new->withHeader($header, $value);
        }

        return $new;
    }

    /**
     * With Attributes
     *
     * @param array $attributes Attributes
     *
     * @return HttpMessages_CraftRequest Craft Request
     */
    public function withAttributes(array $attributes)
    {
        $new = clone $this;

        foreach ($attributes as $attribute => $value) {
            $new = $new->withAttribute($attribute, $value);
        }

        return $new;
    }

    /**
     * With Server Params
     *
     * @param array $server_params Server Params
     *
     * @return CraftRequest Request
     */
    public function withServerParams(array $server_params)
    {
        $new = clone $this;

        $new->server_params = $server_params;

        return $new;
    }

    /**
     * Get Route
     *
     * @return string Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * With Route
     *
     * @param string $route Route
     *
     * @return Request Request
     */
    public function withRoute(HttpMessages_Route $route)
    {
        $new = clone $this;

        $new->route = $route;

        return $new;
    }

}
