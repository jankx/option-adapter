<?php

use Jankx\Option\Option;

if (!function_exists('jankx_get_option')) {
    function jankx_get_option($optionName, $defaultValue = null)
    {
        return Option::get($optionName, $defaultValue);
    }
}
