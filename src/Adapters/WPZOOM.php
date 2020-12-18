<?php
namespace Jankx\Option\Adapters;

use option;
use Jankx\Option\Abstracts\Adapter;

class WPZOOM extends Adapter
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
