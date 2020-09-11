<?php
namespace Jankx\Option\Adapters;

use Jankx\Option\OptionFrameworkAdapter;

class WordPressSettingAPI extends OptionFrameworkAdapter
{
    public function getOption($name, $defaultValue = null)
    {
        return get_option($name, $defaultValue);
    }
}
