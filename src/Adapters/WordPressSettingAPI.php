<?php
namespace Jankx\Option\Adapters;

use Jankx\Option\Abstracts\Adapter;

class WordPressSettingAPI extends Adapter
{
    public function getOption($name, $defaultValue = null)
    {
        return get_option($name, $defaultValue);
    }
}
