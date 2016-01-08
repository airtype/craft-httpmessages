<?php

namespace HttpMessages\Services;

use HttpMessages\Http\CraftResponse as Response;
use Streamer\Stream as Streamer;
use HttpMessages\Http\Stream;

class ResponseService
{
    /**
     * Response
     *
     * @var HttpMessages\Http\Response
     */
    protected $response;

    /**
     * Constructor
     */
    public function __construct()
    {
        $response = new Response;

        // Message
        $response = $this->withProtocolVersion($response);
        $response = $this->withBody($response);

        // Response
        $response = $this->withStatus($response);

        $this->response = $response;
    }

    /**
     * With Protocol Version
     *
     * @param Response Response
     *
     * @return Response Response
     */
    private function withProtocolVersion(Response $response)
    {
        return $response->withProtocolVersion(\Craft\craft()->request->getHttpVersion());
    }

    /**
     * With Body
     *
     * @param Response Response
     *
     * @return Response Response
     */
    private function withBody(Response $response)
    {
        $streamer = new Streamer(fopen('php://temp', 'w+'));

        return $response->withBody(new Stream($streamer));
    }

    /**
     * With Status Code
     *
     * @param Response Response
     *
     * @return Response Response
     */
    private function withStatus(Response $response)
    {
        return $response->withStatus(200);
    }

    /**
     * Get Response
     *
     * @return Response Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
