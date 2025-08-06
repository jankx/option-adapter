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

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

use Jankx\Adapter\Options\Frameworks\JankxOptionFramework;
use Jankx\Adapter\Options\Frameworks\KirkiFramework;
use Jankx\Adapter\Options\Frameworks\ReduxFramework;
use Jankx\Adapter\Options\Frameworks\WordPressSettingAPI;
use Jankx\Adapter\Options\Frameworks\CustomizeFramework;

class Framework
{
    protected static $instance;
    protected static $framework;
    protected static $mode = 'auto';
    protected static $configFramework = null;

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

    /**
     * Set framework mode from external (Jankx Framework)
     *
     * @param string $framework
     * @return void
     */
    public static function setFrameworkFromExternal($framework)
    {
        static::$configFramework = $framework;
    }

    public function setMode($mode)
    {
        static::$mode = $mode;
    }

    /**
     * Get framework from external config or WordPress option
     *
     * @return string|null
     */
    protected function getFrameworkFromConfig()
    {
        // Priority 1: External config (set by Jankx Framework)
        if (static::$configFramework !== null) {
            return static::$configFramework;
        }

        // Priority 2: WordPress option (backward compatibility)
        $framework = get_option('jankx_option_framework');
        if (!empty($framework)) {
            return $framework;
        }

        return null;
    }

    protected function detectFramework()
    {
        error_log('[JANKX DEBUG] Framework detection: Starting detection');

        // Priority 1: Redux Framework (highest priority)
        if (class_exists('Redux')) {
            error_log('[JANKX DEBUG] Framework detection: Redux class found');
            return 'redux';
        }
        error_log('[JANKX DEBUG] Framework detection: Redux class not found');

        // Priority 2: Jankx Dashboard Framework
        if (class_exists('\Jankx\Dashboard\OptionFramework')) {
            error_log('[JANKX DEBUG] Framework detection: Jankx OptionFramework class found');
            return 'jankx';
        }
        error_log('[JANKX DEBUG] Framework detection: Jankx OptionFramework class not found');

        // Priority 3: WPZOOM Framework
        if ((defined('WPZOOM_INC') && class_exists('option'))) {
            error_log('[JANKX DEBUG] Framework detection: WPZOOM class found');
            return 'zoom';
        }
        error_log('[JANKX DEBUG] Framework detection: WPZOOM class not found');

        // Priority 4: WordPress Customizer (always available)
        error_log('[JANKX DEBUG] Framework detection: Using WordPress Customizer');
        return 'customize';
    }

    public function loadFramework()
    {
        error_log('[JANKX DEBUG] Framework loading: Starting with mode: ' . static::$mode);

        // First, try to get framework from config
        if (static::$mode === 'auto') {
            $configFramework = $this->getFrameworkFromConfig();
            error_log('[JANKX DEBUG] Framework loading: Config framework: ' . ($configFramework ?: 'null'));

            if ($configFramework) {
                static::$mode = $configFramework;
                error_log('[JANKX DEBUG] Framework loading: Using config framework: ' . static::$mode);
            } else {
                // Fallback to auto-detection if no config found
                $mode = $this->detectFramework();
                error_log('[JANKX DEBUG] Framework loading: Detected framework: ' . $mode);

                if ($mode && !in_array($mode, ['auto', 'wordpress'])) {
                    update_option('jankx_option_framework', $mode);
                    static::$mode = $mode;
                    error_log('[JANKX DEBUG] Framework loading: Set mode to: ' . static::$mode);
                }
            }
        }

        $frameworks = apply_filters('jankx_option_framework_modes', array(
            'jankx'     => JankxOptionFramework::class,
            'kirki'     => KirkiFramework::class,
            'redux'     => ReduxFramework::class,
            'wordpress' => WordPressSettingAPI::class,
            'customize' => CustomizeFramework::class,
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

    /**
     * Get current framework mode
     *
     * @return string
     */
    public static function getCurrentMode()
    {
        return static::$mode;
    }
}
