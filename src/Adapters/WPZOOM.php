<?php
namespace Jankx\Option\Adapters;

use Jankx\Option\OptionFrameworkAdapter;

class WPZOOM extends OptionFrameworkAdapter
{
    public function getOption($name, $defaultValue = null)
    {
        /**
         * Argument #2 is echo flag.
         * Jankx option don't support it so its value is setted `false`
         */
        $value = option::get($name, false);
        if (is_null($value)) {
            return $defaultValue;
        }
        return $value;
    }
}
