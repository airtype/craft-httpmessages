<?php

return [

    /**
     * Dev Mode (boolean)
     *
     * While in dev mode, the stack trace of errors are printed in the
     * json response body. By default, this `devMode` observes the default
     * setting of the `devMode` in Craft's general configuration.
     */
    'devMode' => \Craft\craft()->config->get('devMode'),

    /**
     * Page Trigger
     *
     * The query string parametere for pagination. If the value is the same as
     * Craft's `pageTrigger`, an exception will be thrown.
     */
    'paginationParameter' => 'page',

    /**
     * Pagination Base Url
     *
     * The url that is prepended to all pagination links.
     */
    'paginationBaseUrl' => \Craft\craft()->request->getPath(),

    /**
     * Paginator
     *
     * "Paginators offer more information about your result-set including total, and have
     * next/previous links which will only show if there is more data available. This
     * intelligence comes at the cost of having to count the number of entries in a database
     * on each call." - http://fractal.thephpleague.com/pagination/#using-paginators
     */
    'paginator' => 'Craft\\HttpMessages_Paginator',

    /**
     * Default Serializer
     *
     * "A Serializer structures your Transformed data in certain ways. There are many output
     * structures for APIs, two popular ones being HAL and JSON-API. Twitter and Facebook output
     * data differently to each other, and Google does it differently too. Most of the differences
     * between these serializers are how data is namespaced.
     *
     * Serializer classes let you switch between various output formats with minimal effect on
     * your Transformers." - http://fractal.thephpleague.com/serializers/
     */
    'defaultSerializer' => 'ArraySerializer',

    /**
     * Serializers
     *
     * Available serializers that can be specified as default serializer.
     */
    'serializers' => [
        'ArraySerializer'     => 'League\\Fractal\\Serializer\\ArraySerializer',
        'DataArraySerializer' => 'League\\Fractal\\Serializer\\DataArraySerializer',
        'JsonApiSerializer'   => 'League\\Fractal\\Serializer\\JsonApiSerializer',
    ],

];
