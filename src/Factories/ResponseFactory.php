<?php

namespace HttpMessages\Factories;

use HttpMessages\Http\CraftResponse;
use Streamer\Stream as Streamer;
use HttpMessages\Http\Stream;

class ResponseFactory
{
    /**
     * Create
     *
     * @return CraftResponse Response
     */
    public static function create()
    {
        $response = new CraftResponse;

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
    private function withProtocolVersion(CraftResponse $response)
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
    private function withBody(CraftResponse $response)
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
    private function withStatus(CraftResponse $response)
    {
        return $response->withStatus(200);
    }

}
