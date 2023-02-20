<?php
namespace Jankx\Option;

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

        if (!is_a($framework, Adapter::class)) {
            throw new \Exception(sprintf(
                'The option framework must be an instance of %s',
                Adapter::class
            ));
        }

        return $framework->getOption(
            $optionName,
            $defaultValue
        );
    }
}
