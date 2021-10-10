<?php
namespace Jankx\Option\Adapters;

use Redux;
use Jankx\Option\Abstracts\Adapter;

class ReduxFramework extends Adapter
{
    protected $optionName;
    protected $themeOptions;

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

        $reduxOptions = $GLOBALS[$this->optionName];
        $this->themeOptions = &$reduxOptions;
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

    public function addSection($sectionId, $sectionArgs)
    {
        if (method_exists(Redux::class, 'set_section')) {
            return Redux::set_section($sectionId, $sectionArgs);
        }

        // Support old Redux version
        return Redux::setSection($sectionId, $sectionArgs);
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
}
