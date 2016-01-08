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
namespace League\Uri\Components;

use InvalidArgumentException;

/**
 * A Trait to validate a Hostname
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
trait HostnameTrait
{
    /**
     * Tells whether we have a IDN or not
     *
     * @var bool
     */
    protected $isIdn = false;

    /**
     * @inheritdoc
     */
    public function isIdn()
    {
        return $this->isIdn;
    }

    /**
     * Format an label collection for string representation of the Host
     *
     * @param array $labels  host labels
     * @param bool  $convert should we transcode the labels into their ascii equivalent
     *
     * @return array
     */
    protected function convertToAscii(array $labels, $convert)
    {
        return $convert ? array_map('idn_to_ascii', $labels) : $labels;
    }

    /**
     * Validate a string only host
     *
     * @param string $str
     *
     * @return array
     */
    protected function validateStringHost($str)
    {
        if (empty($str)) {
            return [];
        }
        $host = $this->lower($this->setIsAbsolute($str));
        $raw_labels = explode('.', $host);
        $labels = array_map(function ($value) {
            return idn_to_ascii($value);
        }, $raw_labels);

        $this->assertValidHost($labels);
        $this->isIdn = $raw_labels !== $labels;

        return array_reverse(array_map(function ($label) {
            return idn_to_utf8($label);
        }, $labels));
    }

    /**
     * set the FQDN property
     *
     * @param string $str
     *
     * @return string
     */
    abstract protected function setIsAbsolute($str);

    /**
     * Convert to lowercase a string without modifying unicode characters
     *
     * @param string $str
     *
     * @return string
     */
    protected function lower($str)
    {
        $res = [];
        for ($i = 0, $length = mb_strlen($str, 'UTF-8'); $i < $length; $i++) {
            $char = mb_substr($str, $i, 1, 'UTF-8');
            if (ord($char) < 128) {
                $char = strtolower($char);
            }
            $res[] = $char;
        }

        return implode('', $res);
    }

    /**
     * Validate a String Label
     *
     * @param array $labels found host labels
     *
     * @throws InvalidArgumentException If the validation fails
     */
    protected function assertValidHost(array $labels)
    {
        $verifs = array_filter($labels, function ($value) {
            return '' !== trim($value);
        });

        if ($verifs !== $labels) {
            throw new InvalidArgumentException('Invalid Hostname, empty labels are not allowed');
        }

        $this->assertLabelsCount($labels);
        $this->isValidContent($labels);
    }

    /**
     * Validated the Host Label Count
     *
     * @param array $labels host labels
     *
     * @throws InvalidArgumentException If the validation fails
     */
    abstract protected function assertLabelsCount(array $labels);

    /**
     * Validated the Host Label Pattern
     *
     * @param array $data host labels
     *
     * @throws InvalidArgumentException If the validation fails
     */
    protected function isValidContent(array $data)
    {
        if (count(preg_grep('/^[0-9a-z]([0-9a-z-]{0,61}[0-9a-z])?$/i', $data, PREG_GREP_INVERT))) {
            throw new InvalidArgumentException('Invalid Hostname, some labels contain invalid characters');
        }
    }
}
