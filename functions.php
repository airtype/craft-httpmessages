<?php

if (!function_exists('dd')) {
    function dd($mixed)
    {
        \Craft\Craft::dd($mixed);
    }
}

if (!function_exists('craft')) {
    function craft()
    {
        return \Craft\craft();
    }
}
