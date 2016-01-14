<?php

namespace HttpMessages\Services;

use HttpMessages\Http\CraftRequest as Request;
use Streamer\Stream as Streamer;
use HttpMessages\Http\Stream;
use League\Uri\Schemes\Http as HttpUri;

class RequestService
{
    /**
     * Request
     *
     * @var HttpMessages\Http\Request
     */
    protected $request;

    /**
     * Constructor
     */
    public function __construct()
    {
        $request  = new Request();

        // Message
        $request = $this->withProtocolVersion($request);
        $request = $this->withHeaders($request);
        $request = $this->withBody($request);

        // Request
        $request = $this->withRequestTarget($request);
        $request = $this->withMethod($request);
        $request = $this->withUri($request);

        // Server Request
        $request = $this->withServerParams($request);
        $request = $this->withCookieParams($request);
        $request = $this->withQueryParams($request);
        $request = $this->withUploadedFiles($request);
        $request = $this->withParsedBody($request);

        // Craft Request
        // $request = $this->withAttributes($request);
        // $request = $this->withCriteria($request);

        $this->request = $request;
    }

    /**
     * Set Default Protocol Version
     *
     * @return void
     */
    private function withProtocolVersion(Request $request)
    {
        return $request->withProtocolVersion(\Craft\craft()->request->getHttpVersion());
    }

    /**
     * Set Default Headers
     *
     * @return void
     */
    private function withHeaders(Request $request)
    {
        $headers = [];

        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = array_map('trim', explode(',', $value));
            }
        }

        return $request->withHeaders($headers);
    }

    /**
     * Set Body
     *
     * @return void
     */
    private function withBody(Request $request)
    {
        $streamer = new Streamer(fopen('php://input', 'r'));

        return $request->withBody(new Stream($streamer));
    }

    /**
     * Set Default Request Target
     *
     * @return void
     */
    private function withRequestTarget(Request $request)
    {
        return $request->withRequestTarget(\Craft\craft()->request->getUrl());
    }

    /**
     * Set Default Method
     *
     * @return void
     */
    private function withMethod(Request $request)
    {
        return $request->withMethod(\Craft\craft()->request->getRequestType());
    }

    /**
     * Set Default Uri
     *
     * @return void
     */
    private function withUri(Request $request)
    {
        $uri = HttpUri::createFromServer($_SERVER);

        return $request->withUri($uri);
    }

    /**
     * Set Default Server Params
     *
     * @return void
     */
    private function withServerParams(Request $request)
    {
        return $request->withServerParams($_SERVER);
    }

    /**
     * Set Default Cookie Params
     *
     * @return void
     */
    private function withCookieParams(Request $request)
    {
        return $request->withCookieParams($_COOKIE);
    }

    /**
     * Set Default Query Params
     *
     * @return void
     */
    private function withQueryParams(Request $request)
    {
        return $request->withQueryParams(\Craft\craft()->request->getQuery());
    }

    /**
     * Set Default Uploaded Files
     *
     * @return void
     */
    private function withUploadedFiles(Request $request)
    {
        $files = [];

        foreach ($_FILES as $file) {
            $files[] = new UploadedFile($file);
        }

        return $request->withUploadedFiles($files);
    }

    /**
     * Set Default Parsed Body
     *
     * @return void
     */
    private function withParsedBody(Request $request)
    {
        if (in_array($request->getHeaderLine('Content-Type'), ['application/x-www-form-urlencoded', 'multipart/form-data']) && $request->getMethod() === 'POST') {
            $parsed_body = $_POST;
        } else {
            mb_parse_str($request->getBody()->getContents(), $parsed_body);

            $parsed_body = $parsed_body;
        }

        return $request->withParsedBody($parsed_body);
    }

    /**
     * Set Default Attributes
     *
     * @return void
     */
    private function withAttributes(Request $request)
    {
        $attributes = \Craft\craft()->urlManager->getRouteParams()['variables'];
        unset($attributes['matches']);

        return $request->withAttributes($attributes);
    }

    /**
     * With Criteria
     *
     * @param Request $request Request
     *
     * @return Request Request
     */
    public function withCriteria($element_type, $element_id = null, array $attributes)
    {
        $pagination_parameter = \Craft\craft()->config->get('paginationParameter', 'restfulApi');

        $criteria = \Craft\craft()->elements->getCriteria($element_type, $attributes);

        if (isset($criteria->$pagination_parameter)) {
            $criteria->offset = ($criteria->$pagination_parameter - 1) * $criteria->limit;
            unset($criteria->$pagination_parameter);
        }

        if ($element_id) {
            $criteria->archived = null;
            $criteria->fixedOrder = null;
            $criteria->limit = 1;
            $criteria->localeEnabled = false;
            $criteria->offset = 0;
            $criteria->order = null;
            $criteria->status = null;
            $criteria->editable = null;

            if (is_numeric($element_id)) {
                $criteria->id = $element_id;
            } else {
                $criteria->slug = $element_id;
            }
        }

        $request = $request->withCriteria($criteria);

        return $request;
    }

    /**
     * Get Request
     *
     * @return object Response
     */
    public function getRequest()
    {
        return $this->request;
    }

}

