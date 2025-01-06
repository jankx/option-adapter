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

namespace Jankx\Adapter\Options;

use Jankx\Adapter\Options\Frameworks\JankxOptionFramework;
use Jankx\Adapter\Options\Frameworks\KirkiFramework;
use Jankx\Adapter\Options\Frameworks\ReduxFramework;
use Jankx\Adapter\Options\Frameworks\WordPressSettingAPI;

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
        if (class_exists('\Jankx\Dashboard\OptionFramework')) {
            return 'jankx';
        }
        if (class_exists('Redux')) {
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
            $mode = $this->detectFramework();

            if ($mode && !in_array($mode, ['auto', 'wordpress'])) {
                update_option('jankx_option_framework', $mode);
                static::$mode = $mode;
            }
        }

        $frameworks = apply_filters('jankx_option_framework_modes', array(
            'jankx'     => JankxOptionFramework::class,
            'kirki'     => KirkiFramework::class,
            'redux'     => ReduxFramework::class,
            'wordpress' => WordPressSettingAPI::class,
        ));

        if (!isset($frameworks[static::$mode])) {
            throw new \Exception(sprintf(
                'The option framework mode "%s" is not supported',
                static::$mode
            ));
        }

        // Jankx option is not support override Framework to get good result
        if (is_null(static::$framework)) {
            static::$framework = new $frameworks[static::$mode]();

            static::$framework->prepare();

            do_action_ref_array('jankx_option_setup_framework', array(
                &static::$framework,
                static::$mode
            ));
        }
    }

    /**
     * Return active option framework instance
     *
     * @return \Jankx\Adapter\Options\Interfaces\Adapter
     */
    public static function getActiveFramework()
    {
        return static::$framework;
    }
}
