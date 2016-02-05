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
     * @param string $key Key
     *
     * @return mixed Query Param
     */
    public function getQueryParam($key)
    {
        return isset($this->query_params[$key]) ? $this->query_params[$key] : null;
    }

    /**
     * Get Param
     *
     * @param string $key Key
     *
     * @return mixed Param
     */
    public function getParam($key)
    {
        return isset($this->parsed_body[$key]) ? $this->parsed_body[$key] : null;
    }

    /**
     * With Headers
     *
     * @param array $headers Headers
     *
     * @return Request Request
     */
    public function withHeaders(array $headers)
    {
        $new = clone $this;

        $new->headers = $headers;

        return $new;
    }

    /**
     * Get Criteria
     *
     * @return ElementCriteriaModel Criteria
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * With Criteria
     *
     * @param ElementCriteriaModel|null $criteria Criteria
     *
     * @return Request Request
     */
    public function withCriteria(ElementCriteriaModel $criteria = null)
    {
        $new = clone $this;

        $new->criteria = $criteria;

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
