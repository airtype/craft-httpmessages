<?php

namespace Craft;

use Craft\HttpMessages_CraftResponse as Response;
use Streamer\Stream as Streamer;
use Craft\HttpMessages_Stream as Stream;

class HttpMessages_ResponseFactory
{
    /**
     * Create
     *
     * @return Response Response
     */
    public static function create()
    {
        $response = new Response;

        // Message
        $response = self::withProtocolVersion($response);
        $response = self::withBody($response);

        // Response
        $response = self::withStatus($response);

        return $response;
    }

    /**
     * With Protocol Version
     *
     * @param CraftResponse Response
     *
     * @return CraftResponse Response
     */
    private static function withProtocolVersion(Response $response)
    {
        return $response->withProtocolVersion(craft()->request->getHttpVersion());
    }

    /**
     * With Body
     *
     * @param CraftResponse Response
     *
     * @return CraftResponse Response
     */
    private static function withBody(Response $response)
    {
        $streamer = new Streamer(fopen('php://temp', 'w+'));

        return $response->withBody(new Stream($streamer));
    }

    /**
     * With Status Code
     *
     * @param CraftResponse Response
     *
     * @return CraftResponse Response
     */
    private static function withStatus(Response $response)
    {
        return $response->withStatus(200);
    }

}
