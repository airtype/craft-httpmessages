<?php

namespace Craft;

use Craft\HttpMessages_CraftRequest as Request;
use Craft\HttpMessages_CraftResponse as Response;

class HttpMessages_CacheMiddleware
{
    /**
     * Config
     *
     * @var HttpMessages_ConfigCollection
     */
    protected $config;

    /**
     * Duration
     *
     * @var integer
     */
    protected $duration;

    /**
     * Enabled
     *
     * @var bool
     */
    protected $enabled;

    /**
     * Constructor
     */
    public function __construct()
    {
        $config = craft()->httpMessages_config->get('cache', 'middleware');

        if ($duration = $config->get('defaultCacheDuration')) {
            $this->setDuration($duration);
        }

        if ($enabled = $config->get('enabled')) {
            $this->setEnabled($enabled);
        }
    }

    /**
     * Set Duration
     *
     * @param integer $duration Duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Set Enabled
     *
     * @param bool $enabled Enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
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
        if (!$this->enabled) {
            $response = $next($request, $response);

            return $response;
        }

        $cache_duration = $this->duration;
        $cache_key = $this->getCacheKey($request, $cache_duration);

        if ($cached = craft()->cache->get($cache_key)) {
            return $this->buildResponseFromCache($response, $cached);
        }

        $response = $next($request, $response);

        $this->cacheResponse($response, $cache_key, $cache_duration);

        return $response;
    }

    /**
     * Get Cache Key
     *
     * @param Request $request Request
     *
     * @return string Cache Key
     */
    private function getCacheKey(Request $request, $cache_duration)
    {
        $keys = array_merge($request->getQueryParams(), $request->getAttributes());

        $cache_key = $request->getUri();
        $cache_key .= $request->getMethod();
        $cache_key .= $cache_duration;
        $cache_key .= serialize($keys);

        return md5($cache_key);
    }

    /**
     * Cache Response
     *
     * @param Response $response       Response
     * @param string   $cache_key      Cache Key
     * @param int      $cache_duration Cache Duration
     *
     * @return void
     */
    private function cacheResponse(Response $response, $cache_key, $cache_duration)
    {
        craft()->cache->set($cache_key, [
            'response' => serialize($response),
            'body' => serialize($response->getBody()->getContents())
        ], $cache_duration);

        $response->getBody()->rewind();
    }

    /**
     * Build Response From Cache
     *
     * @param Response $response Response
     * @param array    $cache    Cache
     *
     * @return Response Response
     */
    private function buildResponseFromCache(Response $response, array $cache)
    {
        $body = $response->getBody();

        $response = unserialize($cache['response']);
        $response = $response->withBody($body);

        $response->getBody()->write(unserialize($cache['body']));
        $response->getBody()->rewind();

        return $response;
    }

}
