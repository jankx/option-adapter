<?php

namespace Jankx\Adapter\Options;

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

use Jankx\Adapter\Options\Interfaces\Adapter as InterfacesAdapter;

final class Helper
{
    public static function getOption($optionName, $defaultValue = null)
    {
        $pre = apply_filters("jankx/option/{$optionName}/pre", null, $defaultValue);
        if (!is_null($pre)) {
            return $pre;
        }

        $framework = Framework::getActiveFramework();
        if (is_null($framework)) {
            return $defaultValue;
        }

        if (!is_a($framework, InterfacesAdapter::class)) {
            throw new \Exception(sprintf(
                'The option framework must be an instance of %s',
                InterfacesAdapter::class
            ));
        }

        return $framework->getOption(
            $optionName,
            $defaultValue
        );
    }

    public static function setOption($optionName, $value)
    {
        $framework = Framework::getActiveFramework();
        if (is_null($framework)) {
            return false;
        }

        if (method_exists($framework, 'setOption')) {
            return $framework->setOption($optionName, $value);
        }

        return false;
    }

    public static function hasOption($optionName)
    {
        $value = self::getOption($optionName);
        return !is_null($value);
    }

    public static function getOptionsReader()
    {
        return OptionsReader::getInstance();
    }

    public static function getFramework()
    {
        return Framework::getInstance();
    }
}

// Global helper functions
if (!function_exists('jankx_get_option')) {
    function jankx_get_option($optionName, $defaultValue = null)
    {
        return Helper::getOption($optionName, $defaultValue);
    }
}

if (!function_exists('jankx_set_option')) {
    function jankx_set_option($optionName, $value)
    {
        return Helper::setOption($optionName, $value);
    }
}

if (!function_exists('jankx_has_option')) {
    function jankx_has_option($optionName)
    {
        return Helper::hasOption($optionName);
    }
}

if (!function_exists('jankx_get_options_reader')) {
    function jankx_get_options_reader()
    {
        return Helper::getOptionsReader();
    }
}

if (!function_exists('jankx_get_framework')) {
    function jankx_get_framework()
    {
        return Helper::getFramework();
    }
}
