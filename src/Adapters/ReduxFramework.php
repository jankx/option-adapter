<?php
namespace Jankx\Option\Adapters;

use Redux;
use Jankx\Option\Abstracts\Adapter;
use Jankx\Option\Specs\Options;
use Jankx\Option\Specs\Section;

class ReduxFramework extends Adapter
{
    protected $optionName;
    protected $themeOptions;

    protected static $mapSectionFields = array(
        'requiredSection' => 'require',
    );
    protected static $mapFieldProperties = array(
        'requiredField' => 'require',
        'defaultValue' => 'default',
    );

    public static function mapSectionFields()
    {
        return static::$mapSectionFields;
    }

    public static function mapFieldProperties()
    {
        return static::$mapFieldProperties;
    }

    public function prepare()
    {
        $this->optionName = apply_filters(
            'jankx_option_redux_framework_option_name',
            preg_replace(array(
                '/[^\w|^_]/',
                '/_{2,}/'
            ), '_', get_template())
        );
    }

    public function getOption($name, $defaultValue = null)
    {
        if (is_null($this->themeOptions)) {
            $this->themeOptions = get_option($this->optionName);
        }

        if (isset($this->themeOptions[$name])) {
            return $this->themeOptions[$name];
        }

        return $defaultValue;
    }

    public function setArgs($args)
    {
        if (method_exists(Redux::class, 'set_args')) {
            return Redux::set_args($this->optionName, $args);
        }
        return Redux::setArgs($this->optionName, $args);
    }

    public function addSection($section)
    {
        if (!is_a($section, Section::class)) {
            return;
        }

        if (method_exists(Redux::class, 'set_section')) {
            return Redux::set_section(
                $this->optionName,
                $this->convertSectionToArgs($section)
            );
        }

        // Support old Redux version
        return Redux::setSection(
            $this->optionName,
            $this->convertSectionToArgs($section)
        );
    }

    public function register_admin_menu($menu_title, $display_name)
    {
        $theme = wp_get_theme(); // For use with some settings. Not necessary.

        $args = array(
            'display_name'         => $display_name,
            'menu_title'           => $menu_title,
            'customizer'           => true,
            'display_version'      => $theme->get('Version'),
            'page_priority'        => 60
        );
        $this->setArgs(apply_filters('jankx/opion/adapter/redux/args', $args));
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
