<?php

namespace Craft;

class HttpMessages_ResponseFactory
{
    /**
     * Create
     *
     * @return Response Response
     */
    public static function fromResponse()
    {
        $response = new HttpMessages_CraftResponse;

        $response = self::withProtocolVersion($response);

        return $response;
    }

    /**
     * With Protocol Version
     *
     * @param HttpMessages_CraftResponse Response
     *
     * @return HttpMessages_CraftResponse Response
     */
    private static function withProtocolVersion(HttpMessages_CraftResponse $response)
    {
        return $response->withProtocolVersion(craft()->request->getHttpVersion());
    }

}
