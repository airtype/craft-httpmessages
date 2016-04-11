<?php

namespace Craft;

use Zend\Diactoros\Response;

class HttpMessages_CraftResponseFactory
{
    /**
     * Create
     *
     * @return Response Response
     */
    public static function fromResponse(Response $response)
    {
        $body    = $response->getBody();
        $status  = $response->getStatusCode();
        $headers = $response->getHeaders();

        return new HttpMessages_CraftResponse($body, $status, $headers);
    }

}
