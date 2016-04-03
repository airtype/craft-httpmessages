<?php

namespace Craft;

use Relay\Runner;

class HttpMessagesService extends BaseApplicationComponent
{
    /**
     * Get Routes
     *
     * @return array Routes
     */
    public function handle($request, $response)
    {
        $runner = new Runner($request->getRoute()->getMiddlewareClasses(), function ($class) {
            return new $class();
        });

        return $runner($request, $response);
    }

}
