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
        $theme = wp_get_theme(); // For use with some settings. Not necessary.

        $args = array(
            'display_name'         => $display_name,
            'menu_title'           => $menu_title,
            'customizer'           => true,
            'display_version'      => $theme->get('Version'),
            'page_priority'        => 60,
            'dev_mode'             => defined('WP_DEBUG') && WP_DEBUG,
        );
        $this->setArgs(apply_filters('jankx/opion/adapter/redux/args', $args));
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

        // Get pages from options reader
        $pages = $optionsReader->getPages();

        foreach ($pages as $page) {
            // Transform page to Redux section
            $reduxSection = ReduxTransformer::transformPage($page);

            // Get sections for this page
            $sections = $optionsReader->getSections($page->getTitle());

            foreach ($sections as $section) {
                // Transform section fields to Redux fields
                $reduxFields = ReduxTransformer::transformSection($section);
                $reduxSection['fields'] = array_merge($reduxSection['fields'], $reduxFields);
            }

            // Add section to Redux
            if (method_exists(Redux::class, 'set_section')) {
                Redux::set_section($this->optionName, $reduxSection);
            } else {
                // Support old Redux version
                Redux::setSection($this->optionName, $reduxSection);
            }
        }
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
