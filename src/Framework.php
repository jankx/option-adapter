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
        if (class_exists('Redux')) {
            return 'redux';
        }
        if ((defined('WPZOOM_INC') && class_exists('option'))) {
            return 'zoom';
        }
        return 'wordpress';
    }

    protected function getActiveMode()
    {
        if (static::$mode === 'auto') {
            $mode = $this->detectFramework();

            if ($mode && $mode !== 'auto') {
                update_option('jankx_option_framework', $mode);
                static::$mode = $mode;
            }
        }
        return apply_filters('jankx/option/framework/active', static::$mode);
    }

    /**
     * @return \Jankx\Option\Abstracts\Adapter
     */
    protected function setupFramework($frameworks)
    {
        $mode      = $this->getActiveMode();
        if (!isset($frameworks[$mode])) {
            return;
        }

        $framework = new $frameworks[$mode];

        $framework->prepare();

        do_action_ref_array('jankx/option/framework/setup', array(
            &$framework,
            static::$mode
        ));

        return $framework;
    }

    public function loadFramework()
    {
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
            static::$framework = $this->setupFramework($frameworks);
        }
    }

    public static function getActiveFramework()
    {
        return static::$framework;
    }
}
