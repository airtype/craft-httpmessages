<?php

namespace HttpMessages\Http;

use Craft\ElementCriteriaModel;

class CraftRequest extends ServerRequest
{
    /**
     * Criteria
     *
     * @var Craft\ElementCriteriaModel
     */
    protected $criteria;

    /**
     * Route
     *
     * @var Route
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
        return isset($this->query_params[$key]) ? $this->query_params[$key] : $default;
    }

    /**
     * Get Params
     *
     * @return array Params
     */
    public function getParams()
    {
        return $this->parsed_body;
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
        return isset($this->parsed_body[$key]) ? $this->parsed_body[$key] : $default;
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

        $new->headers = $headers;

        return $new;
    }

    /**
     * With Attributes
     *
     * @param array $attributes Attributes
     *
     * @return CraftRequest Request
     */
    public function withAttributes(array $attributes)
    {
        $new = clone $this;

        $new->attributes = $attributes;

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
    public function withRoute(Route $route)
    {
        $new = clone $this;

        $new->route = $route;

        return $new;
    }

}
