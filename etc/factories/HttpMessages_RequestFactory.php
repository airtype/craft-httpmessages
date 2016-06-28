<?php

namespace Craft;

use Zend\Diactoros\ServerRequest;

class HttpMessages_RequestFactory
{
    /**
     * Create
     *
     * @return Request Request
     */
    public static function fromRequest(ServerRequest $request) {
        return new HttpMessages_CraftRequest(
            $request->getServerParams(),
            $request->getUploadedFiles(),
            $request->getUri(),
            $request->getMethod(),
            $request->getBody(),
            $request->getHeaders(),
            $request->getCookieParams(),
            $request->getQueryParams(),
            $request->getParsedBody(),
            $request->getProtocolVersion()
        );
    }

}

