<?php

namespace Craft;

use Zend\Diactoros\Response;

class HttpMessages_ResponseService extends BaseApplicationComponent
{
    /**
     * Craft Response
     *
     * @var HttpMessages_CraftResponse
     */
    protected $craftResponse;

    /**
     * Constructor
     */
    public function __construct()
    {
        $response = new Response;

        $this->craftResponse = HttpMessages_CraftResponseFactory::fromResponse($response);
    }

    /**
     * Get Craft Response
     *
     * @return CraftResponse Craft Response
     */
    public function getCraftResponse()
    {
        return $this->craftResponse;
    }

}
