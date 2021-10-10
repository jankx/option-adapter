<?php
namespace Jankx\Option\Adapters;

use Redux;
use Jankx\Option\Abstracts\Adapter;
use Jankx\Option\Specs\Options;
use Jankx\Option\Specs\Section;
use Jankx\Option\OptionsReader;
use Jankx\Option\Framework;

class ReduxFramework extends Adapter
{
    protected $optionName;
    protected $themeOptions;

    protected static $mapSectionFields = array(
        'requiredSection' => 'require',
    );
    protected static $mapFieldProperties = array(
        'requiredField' => 'require'
    );


    protected function createOptionName()
    {
        $this->optionName = apply_filters(
            'jankx_option_redux_framework_option_name',
            get_template()
        );
        return $this->optionName;
    }

    public function prepare()
    {
        $this->createOptionName();

        $optionName = $this->optionName;

        global $$optionName;

        $this->themeOptions = $$optionName;
    }

    public function getOption($name, $defaultValue = null)
    {

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
                $this->convertObjectToArgs($section, static::$mapSectionFields)
            );
        }

        // Support old Redux version
        return Redux::setSection(
            $this->optionName,
            $this->convertObjectToArgs($section, static::$mapSectionFields)
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
