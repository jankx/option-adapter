<?php
namespace Jankx\Option;

use Jankx\Option\Framework;
use Jankx\Option\OptionFrameworkAdapter;

class Option
{
    public static function get($optionName, $defaultValue = null)
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
            $optionName,
            $defaultValue
        );
    }
}
