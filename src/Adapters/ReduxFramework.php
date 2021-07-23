<?php
namespace Jankx\Option\Adapters;

use Jankx;
use Redux;
use Jankx\Option\Abstracts\Adapter;

class ReduxFramework extends Adapter
{
    protected $themeOptions;

    public function prepare()
    {
        $reduxOptionName = apply_filters(
            'jankx_option_redux_framework_option_name',
            get_stylesheet()
        );
        global $$reduxOptionName;

        $this->themeOptions = &$$reduxOptionName;
    }

    public function getOption($name, $defaultValue = null)
    {

        if (isset($this->themeOptions[$name])) {
            return $this->themeOptions[$name];
        }

        return $defaultValue;
    }

    public function register_admin_menu($menu_title, $display_name)
    {
        $opt_name = Jankx::templateStylesheet();
        $theme = wp_get_theme(); // For use with some settings. Not necessary.

        $args = array(
            'display_name'         => $display_name,
            'menu_title'           => $menu_title,
            'customizer'           => true,
            'display_version'      => $theme->get('Version'),
            'page_priority'        => 60
        );

        Redux::setArgs($opt_name, apply_filters('jankx/opion/adapter/redux/args', $args));

        Redux::setSection($opt_name, array(
            'title'  => esc_html__('Basic Field', 'redux-framework-demo'),
            'id'     => 'basic',
            'desc'   => esc_html__('Basic field with no subsections.', 'redux-framework-demo'),
            'icon'   => 'el el-home',
            'fields' => array(
                array(
                    'id'       => 'opt-text',
                    'type'     => 'text',
                    'title'    => esc_html__('Example Text', 'redux-framework-demo'),
                    'desc'     => esc_html__('Example description.', 'redux-framework-demo'),
                    'subtitle' => esc_html__('Example subtitle.', 'redux-framework-demo'),
                    'hint'     => array(
                        'content' => 'This is a <b>hint</b> tool-tip for the text field.<br/><br/>Add any HTML based text you like here.',
                    )
                )
            )
        ));
    }
}
