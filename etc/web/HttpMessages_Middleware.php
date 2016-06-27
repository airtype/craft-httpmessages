<?php

namespace Craft;

use RuntimeException;

class HttpMessages_Middleware extends \Psr7Middlewares\Middleware
{
    /**
     * Namespace
     *
     * @var string
     */
    private static $namespace = 'Craft\\HttpMessages_%sMiddleware';

    /**
     * __callStatic Magic Method
     *
     * @param string $name Name
     * @param array  $args Args
     *
     * @return mixed|RuntimeException
     */
    public static function __callStatic($name, $args)
    {
        $class = sprintf(self::$namespace, ucfirst($name));

        if (class_exists($class)) {
            switch (count($args)) {
                case 0:
                    return new $class();

                case 1:
                    return new $class($args[0]);

                default:
                    return (new \ReflectionClass($class))->newInstanceArgs($args);
            }
        }

        throw new RuntimeException("The middleware {$class} does not exits");
    }
}
