<?php

use Jankx\Option\Framework;
use Jankx\Option\OptionFrameworkAdapter;

if (!function_exists('jankx_option')) {
    function jankx_option($optionName, $defaultValue = null)
    {
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
