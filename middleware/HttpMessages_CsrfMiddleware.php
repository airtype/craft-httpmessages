<?php

namespace Craft;

use Craft\HttpMessages_CraftRequest as Request;
use Craft\HttpMessages_CraftResponse as Response;

class HttpMessages_CsrfMiddleware
{
    /**
     * Config
     *
     * @var array
     */
    protected $config;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->config = craft()->httpMessages_config->get('csrf', 'middleware');
    }

    /**
     * __invoke Magic Method
     *
     * @param Request  $request  Request
     * @param Response $response Response
     * @param callable $next     Next
     *
     * @return Response Response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $this->validateCsrfToken($request);

        if ($next) {
            $response = $next($request, $response);
        }

        return $response;
    }

    /**
     * Validate Csrf Token
     *
     * @param Request $request Request
     *
     * @return void|HttMessages_Exception
     */
    private function validateCsrfToken(Request $request)
    {
        $submittedToken = $this->getSubmittedToken($request);

        $validToken = craft()->request->getCsrfToken();

        if ($submittedToken !== $validToken) {
            throw new HttpMessages_Exception("Csrf token mismatch.");
        }
    }

    /**
     * Get Submitted Token
     *
     * @param Request $request Request
     *
     * @return void|HttMessages_Exception
     */
    private function getSubmittedToken(Request $request)
    {
        $csrfTokenName = $this->config->get('csrfTokenName');

        if ($request->hasHeader($csrfTokenName)) {
            return $request->getHeader($csrfTokenName)[0];
        }

        if ($token = $request->getParam($csrfTokenName)) {
            return $token;
        }

        throw new HttpMessages_Exception("A valid csrf token was not submitted with your request.");
    }

}
