<?php

namespace Jankx\Adapter\Options\Frameworks;

use Jankx\Adapter\Options\Abstracts\Adapter;
use Jankx\Adapter\Options\Specs\Options;

use Jankx\Dashboard\Elements\Field;
use Jankx\Dashboard\Elements\Section;
use Jankx\Dashboard\Elements\Page;
use Jankx\Dashboard\OptionFramework;

use Jankx\GlobalConfigs;

class JankxOptionFramework extends Adapter
{
    public function setArgs($args) {}

    public function addSection($section) {}

    public static function mapSectionFields()
    {
        return [];
    }

    public static function mapFieldProperties()
    {
        return [];
    }

    public function getOption($name, $defaultValue = null) {}

    public function register_admin_menu($menu_title, $display_name)
    {
        $themeInfo = wp_get_theme(get_template());
        $optionName = class_exists(GlobalConfigs::class) ?  GlobalConfigs::get(
            'theme.short_name',
            $themeInfo->get('Name')
        ) : $themeInfo->get('Name');

        // Tạo instance của OptionFramework
        $optionsFramework = new OptionFramework(
            'jankx_options',
            $display_name,
            $menu_title,
        );

        // Tạo page Cài Đặt Chung
        $generalSettingsPage = new Page('Cài Đặt Chung');
        $generalSettingsSection = new Section('Cài Đặt Chung');
        $generalSettingsSection->addField(new Field('site_logo', 'Logo của Trang', 'input'));
        $generalSettingsSection->addField(new Field('site_description', 'Mô Tả Trang', 'textarea'));
        $generalSettingsPage->addSection($generalSettingsSection);
        $optionsFramework->addPage($generalSettingsPage);

        // Tạo page Cài Đặt Màu Sắc
        $colorSettingsPage = new Page('Cài Đặt Màu Sắc');
        $colorSettingsSection = new Section('Cài Đặt Màu Sắc');
        $colorSettingsSection->addField(new Field('color_scheme', 'Màu Sắc', 'select', [
            'options' => [
                'light' => 'Sáng',
                'dark' => 'Tối'
            ]
        ]));
        $colorSettingsPage->addSection($colorSettingsSection);
        $optionsFramework->addPage($colorSettingsPage);

        // Tạo page Cài Đặt Tính Năng
        $featureSettingsPage = new Page('Cài Đặt Tính Năng');
        $featureSettingsSection = new Section('Cài Đặt Tính Năng');
        $featureSettingsSection->addField(new Field('enable_feature_x', 'Kích Hoạt Tính Năng X', 'select', [
            'options' => [
                'yes' => 'Có',
                'no' => 'Không'
            ]
        ]));
        $featureSettingsPage->addSection($featureSettingsSection);
        $optionsFramework->addPage($featureSettingsPage);
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
