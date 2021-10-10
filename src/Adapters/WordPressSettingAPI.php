<?php
namespace Jankx\Option\Adapters;

use Jankx\Option\Abstracts\Adapter;

class WordPressSettingAPI extends Adapter
{
    public function getOption($name, $defaultValue = null)
    {
        return get_option($name, $defaultValue);
    }

    public function setArgs($args)
    {
    }

    public function addSection($sectionId, $sectionArgs)
    {
    }

    public function register_admin_menu($menu_title, $display_name)
    {
        // add_menu_page($menu_title, $display_name, 'manage_options', 'jankx', null, null, 65);
    }
}
