<?php

namespace Jankx\Adapter\Options\Frameworks;

use Jankx\Adapter\Options\Abstracts\Adapter;
use Jankx\Adapter\Options\Specs\Options;
use Jankx\Dashboard\OptionFramework;
use Jankx\GlobalConfigs;

class JankxOptionFramework extends Adapter
{
    public function setArgs($args)
    {
    }

    public function addSection($section)
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

    public function getOption($name, $defaultValue = null)
    {
    }

    public function register_admin_menu($menu_title, $display_name)
    {
        $themeInfo = wp_get_theme(get_template());
        $optionName = class_exists(GlobalConfigs::class) ?  GlobalConfigs::get(
            'theme.short_name',
            $themeInfo->get('Name')
        ) : $themeInfo->get('Name');
        new OptionFramework(
            'jankx_options',
            $display_name,
            $menu_title,
        );
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
