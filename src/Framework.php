<?php
/**
 * Jankx Option Adapter
 *
 * Jankx theme framework can working with many option frameworks
 * You can choose your fall in love framework.
 *
 * @package jankx
 * @subpackage option
 */

namespace Jankx\Option;

use Jankx\Option\Adapters\Kirki;
use Jankx\Option\Adapters\ReduxFramework;
use Jankx\Option\Adapters\WordPressSettingAPI;
use Jankx\Option\Adapters\WPZOOM;

class Framework
{
    protected static $instance;
    protected static $framework;
    protected static $mode = 'auto';

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function detect()
    {
        if (static::$mode === 'auto') {
            static::$mode = $this->findOptionFramework();
        }
        $supportedFrameworks = array(
            'redux' => ReduxFramework::class
        );
    }

    private function define($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    public function setMode($mode)
    {
        static::$mode = $mode;
    }

    protected function detectFramework()
    {
        if (class_exists('ReduxFramework')) {
            return 'redux';
        }
        if ((defined('WPZOOM_INC') && class_exists('option'))) {
            return 'zoom';
        }
        return 'wordpress';
    }

    public function loadFramework()
    {
        if (static::$mode === 'auto') {
            static::$mode = $this->detectFramework();
        }

        $frameworks = apply_filters('jankx_option_framework_modes', array(
            'Kirki'     => Kirki::class,
            'redux'     => ReduxFramework::class,
            'wordpress' => WordPressSettingAPI::class,
            'zoom'      => WPZOOM::class,
        ));
        if (!isset($frameworks[static::$mode])) {
            throw new \Exception(sprintf(
                'The option framework mode "%s" is not supported',
                static::$mode
            ));
        }

        // Jankx option is not support override Framework to get good result
        if (is_null(static::$framework)) {
            static::$framework = new $frameworks[static::$mode];

            static::$framework->prepare();

            do_action_ref_array('jankx_option_setup_framework', array(
                &static::$framework,
                static::$mode
            ));
        }
    }

    public static function getFramework()
    {
        return static::$framework;
    }
}
