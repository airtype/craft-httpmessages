<?php
/**
 * League.Url (http://url.thephpleague.com)
 *
 * @package   League.url
 * @author    Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @copyright 2013-2015 Ignace Nyamagana Butera
 * @license   https://github.com/thephpleague/uri/blob/master/LICENSE (MIT License)
 * @version   4.0.0
 * @link      https://github.com/thephpleague/uri/
 */
namespace League\Uri;

use InvalidArgumentException;
use League\Uri\Components\Host;
use League\Uri\Components\Query;
use League\Uri\Interfaces\Host as HostInterface;
use League\Uri\Interfaces\Query as QueryInterface;
use League\Uri\Interfaces\Uri;
use League\Uri\Interfaces\UriPart;
use League\Uri\Schemes\Generic\PathFormatterTrait;
use Psr\Http\Message\UriInterface;

/**
 * A class to manipulate URI and URI components output
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class Formatter
{
    use PathFormatterTrait;

    /**
     * Constants for host formatting
     */
    const HOST_AS_UNICODE = 1;
    const HOST_AS_ASCII   = 2;

    /**
     * host encoding property
     *
     * @var int
     */
    protected $hostEncoding = self::HOST_AS_UNICODE;

    /**
     * query encoding property
     *
     * @var int
     */
    protected $queryEncoding = PHP_QUERY_RFC3986;

    /**
     * URI Parser object
     *
     * @var UriParser
     */
    protected $uriParser;

    /**
     * Query Parser object
     *
     * @var QueryParser
     */
    protected $queryParser;

    /**
     * query separator property
     *
     * @var string
     */
    protected $querySeparator = '&';

    public function __construct()
    {
        $this->uriParser = new UriParser();
        $this->queryParser = new QueryParser();
    }

    /**
     * Host encoding setter
     *
     * @param int $encode a predefined constant value
     */
    public function setHostEncoding($encode)
    {
        if (!in_array($encode, [self::HOST_AS_UNICODE, self::HOST_AS_ASCII])) {
            throw new InvalidArgumentException('Unknown Host encoding rule');
        }
        $this->hostEncoding = $encode;
    }

    /**
     * Host encoding getter
     *
     * @return int
     */
    public function getHostEncoding()
    {
        return $this->hostEncoding;
    }

    /**
     * Query encoding setter
     *
     * @param int $encode a predefined constant value
     */
    public function setQueryEncoding($encode)
    {
        if (!in_array($encode, [PHP_QUERY_RFC3986, PHP_QUERY_RFC1738])) {
            throw new InvalidArgumentException('Unknown Query encoding rule');
        }
        $this->queryEncoding = $encode;
    }

    /**
     * Query encoding getter
     *
     * @return int
     */
    public function getQueryEncoding()
    {
        return $this->queryEncoding;
    }

    /**
     * Query separator setter
     *
     * @param string $separator
     */
    public function setQuerySeparator($separator)
    {
        $separator = filter_var($separator, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

        $this->querySeparator = trim($separator);
    }

    /**
     * Query separator getter
     *
     * @return string
     */
    public function getQuerySeparator()
    {
        return $this->querySeparator;
    }

    /**
     * Format an object according to the formatter properties
     *
     * @param UriInterface|Interfaces\Uri|Interfaces\Components\UriPart $input
     *
     * @return string
     */
    public function format($input)
    {
        if ($input instanceof UriPart) {
            return $this->formatUriPart($input);
        }

        if ($input instanceof Uri || $input instanceof UriInterface) {
            return $this->formatUri($input);
        }

        throw new InvalidArgumentException(
            'input must be an URI object or a League UriPart implemented object'
        );
    }

    /**
     * Format a UriPart implemented object according to the Formatter properties
     *
     * @param UriPart $part
     *
     * @return string
     */
    protected function formatUriPart(UriPart $part)
    {
        if ($part instanceof QueryInterface) {
            return $this->queryParser->build($part->toArray(), $this->querySeparator, $this->queryEncoding);
        }

        if ($part instanceof HostInterface) {
            return $this->formatHost($part);
        }

        return $part->__toString();
    }

    /**
     * Format a HostInterface according to the Formatter properties
     *
     * @param HostInterface $host
     *
     * @return string
     */
    protected function formatHost(HostInterface $host)
    {
        if (self::HOST_AS_ASCII == $this->hostEncoding) {
            return $host->toAscii()->__toString();
        }

        return $host->toUnicode()->__toString();
    }

    /**
     * Format a Interfaces\Schemes\Uri according to the Formatter properties
     *
     * @param Uri|UriInterface $uri
     *
     * @return string
     */
    protected function formatUri($uri)
    {
        $scheme = $uri->getScheme();
        if ('' !== $scheme) {
            $scheme .= ':';
        }

        $query = $this->formatUriPart(new Query($uri->getQuery()));
        if ('' !== $query) {
            $query = '?'.$query;
        }

        $fragment = $uri->getFragment();
        if ('' !== $fragment) {
            $fragment = '#'.$fragment;
        }

        $auth = $this->formatAuthority($uri);

        return $scheme.$auth.$this->formatPath($uri->getPath(), $auth).$query.$fragment;
    }

    /**
     * Format a URI authority according to the Formatter properties
     *
     * @param UriInterface|Uri $uri
     *
     * @return string
     */
    protected function formatAuthority($uri)
    {
        if ('' == $uri->getHost()) {
            return '';
        }

        $components = $this->uriParser->parse((string) $uri);
        $port = $components['port'];
        if (!empty($port)) {
            $port = ':'.$port;
        }

        return '//'
            .$this->uriParser->buildUserInfo($components['user'], $components['pass'])
            .$this->formatHost(new Host($components['host']))
            .$port;
    }
}
