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
    public static function create(Response $response)
    {
        $body    = $response->getBody();
        $status  = $response->getStatusCode();
        $headers = $response->getHeaders();

        return new FractalResponse($body, $status, $headers);
    }
}
