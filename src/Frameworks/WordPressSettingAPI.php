<?php

namespace Jankx\Adapter\Options\Frameworks;

use Jankx\Adapter\Options\Abstracts\Adapter;

class WordPressSettingAPI extends Adapter
{
    public function getOption($name, $defaultValue = null)
    {
        return get_option($name, $defaultValue);
    }

    public function setArgs($args)
    {
    }

    public static function mapSectionFields()
    {
        return [];
    }

    public static function mapFieldProperties()
    {
        return [];
    }

    public function addSection($section)
    {
    }

    public function register_admin_menu($menu_title, $display_name)
    {
        // add_menu_page($menu_title, $display_name, 'manage_options', 'jankx', null, null, 65);
    }

    public function createSections($options)
    {
        if (is_a($options, Options::class)) {
            foreach ($options->getSections() as $section) {
                $this->addSection($section);
            }
        }
    }
}
