<?php

use Jankx\Option\Framework;
use Jankx\Option\OptionFrameworkAdapter;

if (!function_exists('jankx_get_option')) {
    function jankx_get_option($optionName, $defaultValue = null)
    {
        $pre = apply_filters("jankx_get_option_{$optionName}", null, $defaultValue);
        if (!is_null($pre)) {
            return $pre;
        }

        $framework = Framework::getFramework();
        if (is_null($framework)) {
            return $defaultValue;
        }

        if (!is_a($framework, OptionFrameworkAdapter::class)) {
            throw new \Exception(sprintf(
                'The option framework must be an instance of %s',
                OptionFrameworkAdapter::class
            ));
        }

        return $framework->getOption(
            $defaultValue,
            $defaultValue
        );
    }
}
