<?php

namespace Craft;

use Zend\Diactoros\ServerRequest;

class HttpMessages_CraftRequestFactory
{
    /**
     * Create
     *
     * @return Request Request
     */
    public static function fromServerRequest(ServerRequest $request)
    {
        $serverParams    = $request->getServerParams();
        $uploadedFiles   = $request->getUploadedFiles();
        $uri             = $request->getUri();
        $method          = $request->getMethod();
        $body            = $request->getBody();
        $headers         = $request->getHeaders();
        $cookieParams    = $request->getCookieParams();
        $queryParams     = $request->getQueryParams();
        $parsedBody      = $request->getParsedBody();
        $protocolVersion = $request->getProtocolVersion();

        return new HttpMessages_CraftRequest(
            $serverParams,
            $uploadedFiles,
            $uri,
            $method,
            $body,
            $headers,
            $cookieParams,
            $queryParams,
            $parsedBody,
            $protocolVersion
        );
    }

}

