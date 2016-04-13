<?php

namespace Craft;

use Craft\HttpMessages_CraftResponse as Response;

class HttpMessages_FractalResponse extends Response
{
    /**
     * Item
     *
     * @var array
     */
    protected $item;

    /**
     * Collection
     *
     * @var array
     */
    protected $collection;

    /**
     * Criteria
     *
     * @var ElementCriteriaModel
     */
    protected $criteria;

    /**
     * Constructor
     *
     * @param Response $response Response
     */
    public function __construct(Response $response)
    {
        foreach (get_object_vars($response) as $property => $value) {
            $this->$property = $value;
        }
    }

    /**
     * Get Collection
     *
     * @return array Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * With Collection
     *
     * @param array $collection Collection
     *
     * @return RestResponse RestResponse
     */
    public function withCollection(array $collection)
    {
        $new = clone $this;

        $new->collection = $collection;

        return $new;
    }

    /**
     * Get Item
     *
     * @return Item Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * With Item
     *
     * @param mixed $item Item
     *
     * @return RestResponse RestResponse
     */
    public function withItem($item)
    {
        $new = clone $this;

        $new->item = $item;

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
     * @param ElementCriteriaModel $criteria Criteria
     *
     * @return RestResponse RestResponse
     */
    public function withCriteria(ElementCriteriaModel $criteria)
    {
        $new = clone $this;

        $new->criteria = $criteria;

        return $new;
    }

}
