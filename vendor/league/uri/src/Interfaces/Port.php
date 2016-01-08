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
namespace League\Uri\Interfaces;

/**
 * Value object representing a URI Port component.
 *
 * Instances of this interface are considered immutable; all methods that
 * might change state MUST be implemented such that they retain the internal
 * state of the current instance and return an instance that contains the
 * changed state.
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 * @see     https://tools.ietf.org/html/rfc3986#section-3.2.3
 */
interface Port extends Component
{
    /**
     * URI component delimiter.
     *
     * @var string
     */
    const DELIMITER = ':';

    /**
     * Maximum port number.
     *
     * @var int
     */
    const MAXIMUM = 65535;

    /**
     * Minimum port number.
     *
     * @var int
     */
    const MINIMUM = 1;

    /**
     * Return an integer representation of the Port component
     *
     * @return null|int
     */
    public function toInt();
}
