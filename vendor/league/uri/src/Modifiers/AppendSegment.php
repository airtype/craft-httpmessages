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
namespace League\Uri\Modifiers;

use League\Uri\Modifiers\Filters\Segment;

/**
 * Append a segment to the URI path
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class AppendSegment extends AbstractPathModifier
{
    use Segment;

    /**
     * New instance
     *
     * @param string $segment
     */
    public function __construct($segment)
    {
        $this->segment = $this->filterSegment($segment);
    }

    /**
     * @inheritdoc
     */
    protected function modify($str)
    {
        return (string) $this->segment->modify($str)->append($this->segment);
    }
}
