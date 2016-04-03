<?php

namespace Craft;

use Craft\HttpMessages_CraftRequest as Request;
use Streamer\Stream as Streamer;
use Craft\HttpMessages_Stream as Stream;
use League\Uri\Schemes\Http as HttpUri;

class HttpMessages_RequestFactory
{
    /**
     * Create
     *
     * @return Request Request
     */
    public static function create()
    {
        $request  = new Request();

        // Message
        $request = self::withProtocolVersion($request);
        $request = self::withHeaders($request);
        $request = self::withBody($request);

        // Request
        $request = self::withRequestTarget($request);
        $request = self::withMethod($request);
        $request = self::withUri($request);

        // Server Request
        $request = self::withServerParams($request);
        $request = self::withCookieParams($request);
        $request = self::withQueryParams($request);
        $request = self::withUploadedFiles($request);
        $request = self::withParsedBody($request);

        // Craft Request
        $request = self::withAttributes($request);

        return $request;
    }

    /**
     * With Protocol Version
     *
     * @return void
     */
    private function withProtocolVersion(Request $request)
    {
        return $request->withProtocolVersion(craft()->request->getHttpVersion());
    }

    /**
     * With Headers
     *
     * @return void
     */
    private function withHeaders(Request $request)
    {
        $irregular_headers = [
            'CONTENT_TYPE',
            'CONTENT_LENGTH',
            'PHP_AUTH_USER',
            'PHP_AUTH_PW',
            'PHP_AUTH_DIGEST',
            'AUTH_TYPE',
        ];

        $headers = [];

        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = array_map('trim', explode(',', $value));
            }

            if (in_array($name, $irregular_headers)) {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $name))))] = array_map('trim', explode(',', $value));
            }
        }

        return $request->withHeaders($headers);
    }

    /**
     * With Body
     *
     * @param CraftRequest $request Request
     *
     * @return CraftRequest Request
     */
    private function withBody(Request $request)
    {
        $streamer = new Streamer(fopen('php://input', 'r'));

        return $request->withBody(new HttpMessages_Stream($streamer));
    }

    /**
     * With Request Target
     *
     * @param CraftRequest $request Request
     *
     * @return CraftRequest Request
     */
    private function withRequestTarget(Request $request)
    {
        return $request->withRequestTarget(craft()->request->getUrl());
    }

    /**
     * With Method
     *
     * @param CraftRequest $request Request
     *
     * @return CraftRequest Request
     */
    private function withMethod(Request $request)
    {
        return $request->withMethod(craft()->request->getRequestType());
    }

    /**
     * With Uri
     *
     * @param CraftRequest $request Request
     *
     * @return CraftRequest Request
     */
    private function withUri(Request $request)
    {
        $uri = HttpUri::createFromServer($_SERVER);

        return $request->withUri($uri);
    }

    /**
     * With Server Params
     *
     * @param CraftRequest $request Request
     *
     * @return CraftRequest Request
     */
    private function withServerParams(Request $request)
    {
        return $request->withServerParams($_SERVER);
    }

    /**
     * With Cookie Params
     *
     * @param CraftRequest $request Request
     *
     * @return CraftRequest Request
     */
    private function withCookieParams(Request $request)
    {
        return $request->withCookieParams($_COOKIE);
    }

    /**
     * With Query Params
     *
     * @param CraftRequest $request Request
     *
     * @return CraftRequest Request
     */
    private function withQueryParams(Request $request)
    {
        return $request->withQueryParams(craft()->request->getQuery());
    }

    /**
     * With Uploaded Files
     *
     * @param CraftRequest $request Request
     *
     * @return CraftRequest Request
     */
    private function withUploadedFiles(Request $request)
    {
        $files = [];

        foreach ($_FILES as $file) {
            // $files[] = new UploadedFile($file);
        }

        return $request->withUploadedFiles($files);
    }

    /**
     * With Parsed Body
     *
     * @param CraftRequest $request Request
     *
     * @return CraftRequest Request
     */
    private function withParsedBody(Request $request)
    {
        return $request->withParsedBody(craft()->request->getRestParams());
    }

    /**
     * With Attributes
     *
     * @param CraftRequest $request Request
     *
     * @return CraftRequest Request
     */
    private function withAttributes(Request $request)
    {
        $attributes = craft()->urlManager->getRouteParams()['variables'];
        unset($attributes['matches']);

        if (!$attributes) {
            $attributes = [];
        }

        return $request->withAttributes($attributes);
    }

}

