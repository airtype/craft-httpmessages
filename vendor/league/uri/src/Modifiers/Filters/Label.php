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
namespace League\Uri\Modifiers\Filters;

use League\Uri\Components\Host;
use League\Uri\Types\ValidatorTrait;

/**
 * Host label trait
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
trait Label
{
    use ValidatorTrait;

    /**
     * A Host object
     *
     * @var Host
     */
    protected $label;

    /**
     * Return a instance with the specified host
     *
     * @param string $label the data to be used
     *
     * @return $this
     */
    public function withLabel($label)
    {
        $label = $this->filterLabel($label);
        $clone = clone $this;
        $clone->label = $label;

        return $clone;
    }

    /**
     * Filter and validate the host string
     *
     * @param string $label the data to validate
     *
     * @return Host
     */
    protected function filterLabel($label)
    {
        return new Host($this->validateString($label));
    }
}
