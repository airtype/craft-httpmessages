# Http Messages

## Overview
Http Messages is a plugin for Craft CMS that creates PSR-7 request and response objects, passing them through a stack of middleware and returning a decorated response.

## Routing
Http Message utilizes a third-party router ([Route] (http://route.thephpleague.com/)) to match routes defined in the Http Messages config file.

## Middleware
PSR-7 request and response objects are then passed to a stack of middleware ([Relay] (http://relayphp.com)) that are defined for that specific route. Each middleware has the opportunity to decorate the request and response objects before passing the objects to the next middleware in the stack. Any middleware that is called as to return the response object. This allows any middleware to manipulate a response object after other middleware have ran. This is all explained in great detail at the [Relay package's website] (http://relayphp.com).

Each middleware is defined as a separate plugin for Craft. You'll need to download these and install them before utilizing them.

### Available Middleware
* [Http Messages - Fractal Middleware] (https://github.com/airtype/httpmessagesfractalmiddleware)
* [Http Messages - Rest Middleware] (https://github.com/airtype/httpmessagesrestmiddleware)
* [Http Messages - Commerce Middleware] (https://github.com/airtype/httpmessagescommercemiddleware)

## Configuration
Ensure that `craft/config/httpmessages.php` exists.

### Routes
Consider the following example:

```php
<?php

return [

    'routes' => [
        [
            'pattern' => '/some/path',
            'methods' => [
                'GET' => [
                    'middleware' => [],
                ],
                'POST' => [
                    'middleware' => [],
                ],
            ],
        ],
        
        [
            'pattern' => '/another/path',
            'methods' => [
                'GET' => [
                    'middleware' => [],
                ],
            ],
        ], 
    ],
    
];
```
The `routes` key of the config array should be an array of associative arrays. Each associative array will define the configuration of that route. Below is the configuration of a single route.

```php
[
    'pattern' => '/some/path',
    'methods' => [
        'GET' => [
            'middleware' => [],
        ],
        'POST' => [
            'middleware' => [
                '
            ],
        ],
    ],
],
```
The `pattern` attribute's value is used by the router to see if the requested path matches.

The `methods` attribute defines what methods are allowed for the defined pattern. Each method is defined as a key, with the value being an associative array that defines a configuration for that specific route.
