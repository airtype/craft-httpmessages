<?php

namespace Craft;

class HttpMessages_ConfigCollection
{
    /**
     * Data
     *
     * @var array
     */
    private $data = [];

    /**
     * Constructor
     *
     * @param array $data Data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get
     *
     * @param string $key     Key
     * @param mixed  $default Default
     *
     * @return mixed Value
     */
    public function get($key, $default = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    /**
     * Has
     *
     * @param string $key Key
     *
     * @return boolean
     */
    public function has($key)
    {
        return isset($this->data[$key]);
    }

}
