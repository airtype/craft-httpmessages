<?php

namespace Craft;

use Craft\HttpMessages_CraftResponse as Response;
use Craft\HttpMessages_FractalResponse as FractalResponse;

class HttpMessages_FractalResponseFactory
{
    /**
     * Create
     *
     * @param Response $response Response
     *
     * @return FractalResponse Fractal Response
     */
    public function create(Response $response)
    {
        return new FractalResponse($response);
    }
}
