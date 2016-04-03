<?php

namespace Craft;

use League\Fractal\Pagination\PaginatorInterface;

class HttpMessages_Paginator implements PaginatorInterface
{
    /**
     * Config
     *
     * @var HttpMessages_ConfigCollection
     */
    protected $config;

    /**
     * Criteria
     *
     * @var ElementCriteriaModel
     */
    protected $criteria;

    /**
     * Limit
     *
     * @var int
     */
    protected $limit;

    /**
     * Current Page
     *
     * @var int
     */
    protected $current_page;

    /**
     * Last Page
     *
     * @var int
     */
    protected $last_page;

    /**
     * Total
     *
     * @var int
     */
    protected $total;

    /**
     * Count
     *
     * @var int
     */
    protected $count;

    /**
     * Per Page
     *
     * @var int
     */
    protected $per_page;

    /**
     * Constructor
     *
     * @param Criteria $criteria
     *
     * @return void
     */
    public function __construct(ElementCriteriaModel $criteria)
    {
        $this->config = craft()->httpMessages_config->get('fractal', 'middleware');

        $this->limit = $criteria->limit;

        $this->count = $criteria->count();

        $this->total = $criteria->total();

        $this->current_page = isset($criteria->page) ? (int) $criteria->page : 1;

        if ($this->current_page > $this->total) {
            $this->current_page = $this->total;
        }

        $this->last_page = ceil($criteria->total() / $this->limit);
    }

    /**
     * Get the current page.
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->current_page;
    }

    /**
     * Get the last page.
     *
     * @return int
     */
    public function getLastPage()
    {
        return $this->last_page;
    }

    /**
     * Get the total.
     *
     * @return int
     */
    public function getTotal()
    {
        // return (int) $this->criteria->total() - (int) $this->criteria->offset;
        return $this->total;
    }

    /**
     * Get the count.
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Get the number per page.
     *
     * @return int
     */
    public function getPerPage()
    {
        return $this->limit;
    }

    /**
     * Get the url for the given page.
     *
     * @param int $page
     *
     * @return string
     */
    public function getUrl($page)
    {
        $pagination_base_url = $this->config->get('paginationBaseUrl');
        $page_trigger = $this->config->get('paginationParameter');

        return $pagination_base_url . '?' . http_build_query(array_merge(craft()->request->getQuery(), [
            $page_trigger => $page
        ]));
    }
}
