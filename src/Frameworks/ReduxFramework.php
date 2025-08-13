<?php

namespace Jankx\Adapter\Options\Frameworks;

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

use Redux;
use Jankx\Adapter\Options\Abstracts\Adapter;
use Jankx\Adapter\Options\Specs\Options;
use Jankx\Option\Specs\Section;
use Jankx\Adapter\Options\Transformers\ReduxTransformer;

class ReduxFramework extends Adapter
{
    protected $optionName;
    protected $themeOptions;
    protected $reduxConfig;
    protected $reduxInstance;

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
        if (!class_exists(Redux::class)) {
            return;
        }

        // Transform args to Redux format
        $reduxArgs = ReduxTransformer::transformCompleteConfig($args);
        $this->reduxConfig = $reduxArgs;

        if (method_exists(Redux::class, 'set_args')) {
            return Redux::set_args($this->optionName, $reduxArgs);
        }
        return Redux::setArgs($this->optionName, $reduxArgs);
    }

    public function addSection($section)
    {
        if (!class_exists(Redux::class) || !is_a($section, Section::class)) {
            return;
        }

        // Transform section to Redux format
        $reduxSection = ReduxTransformer::transformSection($section);

        if (method_exists(Redux::class, 'set_section')) {
            return Redux::set_section(
                $this->optionName,
                $reduxSection
            );
        }

        // Support old Redux version
        return Redux::setSection(
            $this->optionName,
            $reduxSection
        );
    }

        public function register_admin_menu($menu_title, $display_name)
    {
        if (!class_exists(Redux::class)) {
            return;
        }

        $theme = wp_get_theme(); // For use with some settings. Not necessary.

        $args = array(
            'opt_name'             => $this->optionName,
            'display_name'         => $display_name,
            'menu_title'           => $menu_title,
            'customizer'           => false, // Set to false to create admin menu
            'display_version'      => $theme->get('Version'),
            'page_priority'        => 60,
            'dev_mode'             => defined('WP_DEBUG') && WP_DEBUG,
            'page_parent'          => 'themes.php',
            'page_permissions'     => 'manage_options',
            'save_defaults'        => true,
            'default_show'         => false,
            'default_mark'         => '',
            'show_import_export'   => true,
            'transient_time'       => 60 * MINUTE_IN_SECONDS,
            'output'               => true,
            'output_tag'           => true,
            'database'             => '',
            'use_cdn'              => true,
            'menu_type'            => 'submenu', // Force submenu type
            'allow_sub_menu'       => true, // Allow submenu
            'page_slug'            => $this->optionName, // Set page slug
        );



                // Initialize Redux using constructor
        $this->reduxInstance = new Redux($this->optionName, $args);

        // Force Redux to create the menu
        if ($this->reduxInstance && method_exists($this->reduxInstance, 'init')) {
            $this->reduxInstance->init();
        }

        // Store args for later use
        $this->setArgs(apply_filters('jankx/opion/adapter/redux/args', $args));



        // Force Redux to create the menu immediately
        if (method_exists(Redux::class, 'init')) {
            Redux::init($this->optionName);
        }

                // Add hook to ensure Redux menu is created
        add_action('admin_menu', function() {
            if (method_exists(Redux::class, 'init')) {
                Redux::init($this->optionName);
            }
        }, 5); // Higher priority

        // Force create menu immediately
        add_action('admin_init', function() {
            if (method_exists(Redux::class, 'init')) {
                Redux::init($this->optionName);

            }
        });

        // Force create menu in init hook
        add_action('init', function() {
            if (method_exists(Redux::class, 'init')) {
                Redux::init($this->optionName);
            }
        });
    }

    /**
     * Create sections from OptionsReader
     * @param \Jankx\Adapter\Options\OptionsReader $optionsReader
     *
     * @return void
     */
    public function createSections($optionsReader)
    {
        if (!class_exists(Redux::class)) {
            return;
        }

        // Transform OptionsReader data to Redux format


        // Transform OptionsReader data to Redux format
        $reduxData = ReduxTransformer::transformOptionsReader($optionsReader, $this);
        // Add sections to Redux
        foreach ($reduxData['sections'] as $section) {

            if ($this->reduxInstance) {
                $this->reduxInstance->setSection($section);
            } else {
                if (method_exists(Redux::class, 'set_section')) {
                    Redux::set_section($this->optionName, $section);
                } else {
                    // Support old Redux version
                    Redux::setSection($this->optionName, $section);
                }
            }
        }



        // Force Redux to create the menu after adding sections
        if (method_exists(Redux::class, 'init')) {
            Redux::init($this->optionName);

        }
    }

    /**
     * Transform WordPress dashicons to Redux Elusive Icons
     *
     * @param string $dashicon WordPress dashicon
     * @return string Redux Elusive Icon
     */
    public function transformIcon($dashicon)
    {
        $iconMap = [
            'dashicons-admin-generic' => 'el el-cog',
            'dashicons-editor-textcolor' => 'el el-font',
            'dashicons-art' => 'el el-picture',
            'dashicons-layout' => 'el el-th-large',
            'dashicons-align-wide' => 'el el-align-left',
            'dashicons-align-full-width' => 'el el-align-justify',
            'dashicons-admin-post' => 'el el-file',
            'dashicons-admin-tools' => 'el el-wrench',
            'dashicons-admin-settings' => 'el el-cog',
            'dashicons-admin-appearance' => 'el el-picture',
            'dashicons-admin-plugins' => 'el el-puzzle-piece',
            'dashicons-admin-users' => 'el el-user',
            'dashicons-admin-comments' => 'el el-comment',
            'dashicons-admin-media' => 'el el-picture',
            'dashicons-admin-links' => 'el el-link',
            'dashicons-admin-page' => 'el el-file-alt',
            'dashicons-admin-tools' => 'el el-wrench',
        ];

        $mappedIcon = isset($iconMap[$dashicon]) ? $iconMap[$dashicon] : 'el el-cog';

        return $mappedIcon;
    }

    /**
     * Get Redux configuration
     *
     * @return array
     */
    public function getReduxConfig()
    {
        return $this->reduxConfig;
    }

    /**
     * Set WordPress native field value
     *
     * @param string $fieldId Field ID
     * @param mixed $value Field value
     * @return bool
     */
    public function setWordPressNativeValue($fieldId, $value)
    {
        // Get current options
        $options = get_option($this->optionName, []);

        // Update field value
        $options[$fieldId] = $value;

        // Save options
        $result = update_option($this->optionName, $options);

        // Clear cache
        $this->themeOptions = null;

        return $result;
    }

    /**
     * Get WordPress native field value
     *
     * @param string $fieldId Field ID
     * @param mixed $defaultValue Default value
     * @return mixed
     */
    public function getWordPressNativeValue($fieldId, $defaultValue = null)
    {
        $options = get_option($this->optionName, []);
        return isset($options[$fieldId]) ? $options[$fieldId] : $defaultValue;
    }

    /**
     * Transform field value for Redux
     *
     * @param mixed $value Field value
     * @param string $type Field type
     * @return mixed
     */
    public function transformFieldValue($value, $type)
    {
        return ReduxTransformer::transformFieldValue($value, $type);
    }

    /**
     * Check if field is WordPress native
     *
     * @param string $fieldId Field ID
     * @return bool
     */
    public function isWordPressNativeField($fieldId)
    {
        if (!$this->reduxConfig || !isset($this->reduxConfig['sections'])) {
            return false;
        }

        foreach ($this->reduxConfig['sections'] as $section) {
            if (isset($section['fields'])) {
                foreach ($section['fields'] as $field) {
                    if ($field['id'] === $fieldId) {
                        return isset($field['wordpress_native']) && $field['wordpress_native'];
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get WordPress option name for field
     *
     * @param string $fieldId Field ID
     * @return string|null
     */
    public function getWordPressOptionName($fieldId)
    {
        if (!$this->reduxConfig || !isset($this->reduxConfig['sections'])) {
            return null;
        }

        foreach ($this->reduxConfig['sections'] as $section) {
            if (isset($section['fields'])) {
                foreach ($section['fields'] as $field) {
                    if ($field['id'] === $fieldId) {
                        return isset($field['option_name']) ? $field['option_name'] : null;
                    }
                }
            }
        }

        return null;
    }
}
