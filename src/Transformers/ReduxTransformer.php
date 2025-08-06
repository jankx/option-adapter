<?php

namespace Jankx\Adapter\Options\Transformers;

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

class ReduxTransformer
{
    /**
     * Transform standard configuration to Redux format
     *
     * @param array $config Standard configuration
     * @return array Redux configuration
     */
    public static function transformConfig($config)
    {
        $reduxConfig = [];

        // Transform pages to Redux sections
        if (isset($config['pages'])) {
            foreach ($config['pages'] as $page) {
                $reduxConfig['sections'][] = self::transformPage($page);
            }
        }

        return $reduxConfig;
    }

    /**
     * Transform page configuration to Redux section
     *
     * @param \Jankx\Adapter\Options\Specs\Page $page Page object
     * @return array Redux section
     */
    public static function transformPage($page, $adapter = null)
    {
        $icon = $page->getIcon();

        if ($adapter && method_exists($adapter, 'transformIcon')) {
            $icon = $adapter->transformIcon($icon);
        } else {
            $icon = self::mapIcon($icon);
        }

        $section = [
            'id' => $page->getId(),
            'title' => $page->getTitle(),
            'desc' => $page->getDescription() ?? '',
            'fields' => [],
        ];

        // Add icon if exists and is valid
        if ($icon) {
            $section['icon'] = $icon;
        }

        // Add priority if exists
        if ($page->getPriority()) {
            $section['priority'] = $page->getPriority();
        }

        return $section;
    }

    /**
     * Transform section configuration to Redux fields
     *
     * @param \Jankx\Adapter\Options\Specs\Section $section Section object
     * @return array Redux fields
     */
    public static function transformSection($section)
    {
        $fields = [];

        foreach ($section->getFields() as $field) {
            $fields[] = self::transformField($field);
        }

        return $fields;
    }

    /**
     * Transform field configuration to Redux field
     *
     * @param \Jankx\Adapter\Options\Specs\Field $field Field object
     * @return array Redux field
     */
    public static function transformField($field)
    {


        $reduxField = [
            'id' => $field->getId(),
            'type' => self::mapFieldType($field->getType()),
            'title' => $field->getTitle(),
        ];

        // Add subtitle
        if ($field->getSubtitle()) {
            $reduxField['subtitle'] = $field->getSubtitle();
        }

        // Add description
        if ($field->getDescription()) {
            $reduxField['desc'] = $field->getDescription();
        }

        // Add default value
        if ($field->getDefault()) {
            $reduxField['default'] = $field->getDefault();
        }

        // Add WordPress native support
        if ($field->isWordPressNative()) {
            $reduxField['wordpress_native'] = true;
            if ($field->getOptionName()) {
                $reduxField['option_name'] = $field->getOptionName();
            }
        }

        // Add field-specific options
        $reduxField = self::addFieldOptions($reduxField, $field);

        return $reduxField;
    }

    /**
     * Map WordPress dashicons to Redux icons
     *
     * @param string $dashicon WordPress dashicon
     * @return string Redux icon
     */
    public static function mapIcon($dashicon)
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
     * Map standard field type to Redux field type
     *
     * @param string $type Standard field type
     * @return string Redux field type
     */
    public static function mapFieldType($type)
    {
        $typeMap = [
            'text' => 'text',
            'textarea' => 'textarea',
            'image' => 'media',
            'icon' => 'icon',
            'color' => 'color',
            'select' => 'select',
            'radio' => 'radio',
            'checkbox' => 'checkbox',
            'switch' => 'switch',
            'slider' => 'slider',
            'typography' => 'typography',
            'image_select' => 'image_select',
            'gallery' => 'gallery',
            'repeater' => 'repeater',
            'sorter' => 'sorter',
        ];

        return isset($typeMap[$type]) ? $typeMap[$type] : $type;
    }

    /**
     * Add field-specific options for Redux
     *
     * @param array $reduxField Redux field configuration
     * @param \Jankx\Adapter\Options\Specs\Field $field Original field object
     * @return array Updated Redux field
     */
    public static function addFieldOptions($reduxField, $field)
    {
        switch ($field->getType()) {
            case 'select':
                if ($field->hasOptions()) {
                    $reduxField['options'] = $field->getOptions();
                }
                break;

            case 'radio':
                if ($field->hasOptions()) {
                    $reduxField['options'] = $field->getOptions();
                }
                break;

            case 'slider':
                if ($field->hasMin()) {
                    $reduxField['min'] = $field->getMin();
                }
                if ($field->hasMax()) {
                    $reduxField['max'] = $field->getMax();
                }
                if ($field->hasStep()) {
                    $reduxField['step'] = $field->getStep();
                }
                break;

            case 'image_select':
                if ($field->hasOptions()) {
                    $reduxField['options'] = $field->getOptions();
                }
                break;

            case 'typography':
                if ($field->hasOptions()) {
                    $options = $field->getOptions();
                    $reduxField['google'] = isset($options['google']) ? $options['google'] : true;
                    $reduxField['font-family'] = isset($options['font-family']) ? $options['font-family'] : true;
                    $reduxField['font-size'] = isset($options['font-size']) ? $options['font-size'] : true;
                    $reduxField['font-weight'] = isset($options['font-weight']) ? $options['font-weight'] : true;
                    $reduxField['line-height'] = isset($options['line-height']) ? $options['line-height'] : true;
                    $reduxField['color'] = isset($options['color']) ? $options['color'] : true;
                }
                break;

            case 'repeater':
                if ($field->hasSubFields()) {
                    $reduxField['fields'] = [];
                    foreach ($field->getSubFields() as $subField) {
                        $reduxField['fields'][] = self::transformField($subField);
                    }
                }
                break;

            case 'sorter':
                if ($field->hasOptions()) {
                    $reduxField['options'] = $field->getOptions();
                }
                break;

            case 'media':
                if ($field->hasOptions()) {
                    $options = $field->getOptions();
                    $reduxField['preview_size'] = isset($options['preview_size']) ? $options['preview_size'] : 'medium';
                    $reduxField['library_filter'] = isset($options['library_filter']) ? $options['library_filter'] : [];
                }
                break;

            case 'gallery':
                if ($field->hasOptions()) {
                    $options = $field->getOptions();
                    $reduxField['preview_size'] = isset($options['preview_size']) ? $options['preview_size'] : 'medium';
                }
                break;
        }

        return $reduxField;
    }

    /**
     * Transform complete configuration structure
     *
     * @param array $config Complete configuration
     * @return array Redux configuration
     */
    public static function transformCompleteConfig($config)
    {
        $reduxConfig = [
            'opt_name' => self::generateOptionName(),
            'display_name' => isset($config['display_name']) ? $config['display_name'] : 'Theme Options',
            'display_version' => isset($config['display_version']) ? $config['display_version'] : '1.0.0',
            'menu_type' => 'submenu',
            'allow_sub_menu' => true,
            'menu_title' => isset($config['menu_title']) ? $config['menu_title'] : 'Theme Options',
            'page_title' => isset($config['page_title']) ? $config['page_title'] : 'Theme Options',
            'admin_bar' => false,
            'dev_mode' => isset($config['dev_mode']) ? $config['dev_mode'] : false,
            'customizer' => isset($config['customizer']) ? $config['customizer'] : true,
            'page_priority' => isset($config['menu_position']) ? $config['menu_position'] : 60,
            'page_parent' => 'themes.php',
            'page_permissions' => 'manage_options',
            'menu_icon' => isset($config['menu_icon']) ? $config['menu_icon'] : 'dashicons-admin-customizer',
            'last_tab' => '',
            'page_icon' => 'icon-themes',
            'page_slug' => 'theme-options',
            'save_defaults' => true,
            'default_show' => false,
            'default_mark' => '',
            'show_import_export' => isset($config['import_export']) ? $config['import_export'] : true,
            'transient_time' => 60 * MINUTE_IN_SECONDS,
            'output' => true,
            'output_tag' => true,
            'database' => '',
            'use_cdn' => true,
            'hints' => [
                'icon' => 'el el-question-sign',
                'icon_position' => 'right',
                'icon_color' => 'lightgray',
                'icon_size' => 'normal',
                'tip_style' => [
                    'color' => 'red',
                    'shadow' => true,
                    'rounded' => false,
                    'style' => '',
                ],
                'tip_position' => [
                    'my' => 'top left',
                    'at' => 'bottom right',
                ],
                'tip_effect' => [
                    'show' => [
                        'effect' => 'slide',
                        'duration' => '500',
                        'event' => 'mouseover',
                    ],
                    'hide' => [
                        'effect' => 'slide',
                        'duration' => '500',
                        'event' => 'click mouseleave',
                    ],
                ],
            ],
            'show_options_object' => false,
        ];

        // Add sections
        if (isset($config['pages'])) {
            $reduxConfig['sections'] = [];
            foreach ($config['pages'] as $page) {
                $section = self::transformPage($page);

                // Add fields from sections
                if (isset($page['sections'])) {
                    foreach ($page['sections'] as $sectionConfig) {
                        $section['fields'] = array_merge(
                            $section['fields'],
                            self::transformSection($sectionConfig)
                        );
                    }
                }

                $reduxConfig['sections'][] = $section;
            }
        }

        return $reduxConfig;
    }

    /**
     * Generate option name for Redux
     *
     * @return string Option name
     */
    public static function generateOptionName()
    {
        $theme = wp_get_theme();
        $themeName = $theme->get('Name');

        // Clean theme name for option name
        $optionName = preg_replace('/[^a-zA-Z0-9]/', '_', $themeName);
        $optionName = strtolower($optionName);
        $optionName = preg_replace('/_+/', '_', $optionName);
        $optionName = trim($optionName, '_');

        return $optionName . '_theme_options';
    }

    /**
     * Transform OptionsReader data to Redux format
     *
     * @param \Jankx\Adapter\Options\OptionsReader $optionsReader
     * @return array
     */
    public static function transformOptionsReader($optionsReader, $adapter = null)
    {
        $reduxData = [
            'sections' => [],
        ];

        // Get pages from options reader
        $pages = $optionsReader->getPages();

        foreach ($pages as $page) {
            // Transform page to Redux section (pages become sections in Redux)
            $reduxSection = self::transformPage($page, $adapter);
            $reduxSection['fields'] = []; // Initialize fields array

            // Get sections for this page
            $sections = $optionsReader->getSections($page->getTitle());

            // Merge all fields from all sections into one section
            foreach ($sections as $section) {
                // Get fields for this section
                $fields = $optionsReader->getFields($section->getTitle());

                // Transform each field and add to the section
                foreach ($fields as $field) {
                    $transformedField = self::transformField($field);
                    $reduxSection['fields'][] = $transformedField;
                }
            }

            $reduxData['sections'][] = $reduxSection;
        }

        return $reduxData;
    }

    /**
     * Transform field value for Redux
     *
     * @param mixed $value Field value
     * @param string $type Field type
     * @return mixed Transformed value
     */
    public static function transformFieldValue($value, $type)
    {
        switch ($type) {
            case 'typography':
                if (is_array($value)) {
                    return $value;
                }
                return [
                    'font-family' => 'Arial, sans-serif',
                    'font-size' => '16px',
                    'font-weight' => '400',
                    'line-height' => '1.6',
                    'color' => '#333333',
                ];

            case 'color':
                if (is_array($value)) {
                    return $value;
                }
                return $value ?: '#007cba';

            case 'media':
                if (is_array($value)) {
                    return $value;
                }
                return $value ?: '';

            case 'gallery':
                if (is_array($value)) {
                    return $value;
                }
                return $value ?: [];

            case 'repeater':
                if (is_array($value)) {
                    return $value;
                }
                return $value ?: [];

            case 'sorter':
                if (is_array($value)) {
                    return $value;
                }
                return $value ?: [];

            default:
                return $value;
        }
    }

    /**
     * Transform Redux field back to standard format
     *
     * @param array $reduxField Redux field configuration
     * @return array Standard field configuration
     */
    public static function transformBackToStandard($reduxField)
    {
        $field = [
            'id' => $reduxField['id'],
            'name' => $reduxField['title'],
            'type' => self::mapFieldTypeBack($reduxField['type']),
        ];

        // Add subtitle
        if (isset($reduxField['subtitle'])) {
            $field['sub_title'] = $reduxField['subtitle'];
        }

        // Add description
        if (isset($reduxField['desc'])) {
            $field['description'] = $reduxField['desc'];
        }

        // Add default value
        if (isset($reduxField['default'])) {
            $field['default_value'] = $reduxField['default'];
        }

        // Add WordPress native support
        if (isset($reduxField['wordpress_native'])) {
            $field['wordpress_native'] = $reduxField['wordpress_native'];
        }
        if (isset($reduxField['option_name'])) {
            $field['option_name'] = $reduxField['option_name'];
        }

        // Add field-specific options
        $field = self::addFieldOptionsBack($field, $reduxField);

        return $field;
    }

    /**
     * Map Redux field type back to standard type
     *
     * @param string $type Redux field type
     * @return string Standard field type
     */
    public static function mapFieldTypeBack($type)
    {
        $typeMap = [
            'text' => 'text',
            'textarea' => 'textarea',
            'media' => 'image',
            'icon' => 'icon',
            'color' => 'color',
            'select' => 'select',
            'radio' => 'radio',
            'checkbox' => 'checkbox',
            'switch' => 'switch',
            'slider' => 'slider',
            'typography' => 'typography',
            'image_select' => 'image_select',
            'gallery' => 'gallery',
            'repeater' => 'repeater',
            'sorter' => 'sorter',
        ];

        return isset($typeMap[$type]) ? $typeMap[$type] : $type;
    }

    /**
     * Add field-specific options back to standard format
     *
     * @param array $field Standard field configuration
     * @param array $reduxField Redux field configuration
     * @return array Updated standard field
     */
    public static function addFieldOptionsBack($field, $reduxField)
    {
        switch ($reduxField['type']) {
            case 'select':
            case 'radio':
                if (isset($reduxField['options'])) {
                    $field['options'] = $reduxField['options'];
                }
                break;

            case 'slider':
                if (isset($reduxField['min'])) {
                    $field['min'] = $reduxField['min'];
                }
                if (isset($reduxField['max'])) {
                    $field['max'] = $reduxField['max'];
                }
                if (isset($reduxField['step'])) {
                    $field['step'] = $reduxField['step'];
                }
                break;

            case 'image_select':
                if (isset($reduxField['options'])) {
                    $field['options'] = $reduxField['options'];
                }
                break;

            case 'typography':
                $field['options'] = [];
                if (isset($reduxField['google'])) {
                    $field['options']['google'] = $reduxField['google'];
                }
                if (isset($reduxField['font-family'])) {
                    $field['options']['font-family'] = $reduxField['font-family'];
                }
                if (isset($reduxField['font-size'])) {
                    $field['options']['font-size'] = $reduxField['font-size'];
                }
                if (isset($reduxField['font-weight'])) {
                    $field['options']['font-weight'] = $reduxField['font-weight'];
                }
                if (isset($reduxField['line-height'])) {
                    $field['options']['line-height'] = $reduxField['line-height'];
                }
                if (isset($reduxField['color'])) {
                    $field['options']['color'] = $reduxField['color'];
                }
                break;

            case 'repeater':
                if (isset($reduxField['fields'])) {
                    $field['fields'] = [];
                    foreach ($reduxField['fields'] as $subField) {
                        $field['fields'][] = self::transformBackToStandard($subField);
                    }
                }
                break;

            case 'sorter':
                if (isset($reduxField['options'])) {
                    $field['options'] = $reduxField['options'];
                }
                break;

            case 'media':
                $field['options'] = [];
                if (isset($reduxField['preview_size'])) {
                    $field['options']['preview_size'] = $reduxField['preview_size'];
                }
                if (isset($reduxField['library_filter'])) {
                    $field['options']['library_filter'] = $reduxField['library_filter'];
                }
                break;

            case 'gallery':
                $field['options'] = [];
                if (isset($reduxField['preview_size'])) {
                    $field['options']['preview_size'] = $reduxField['preview_size'];
                }
                break;
        }

        return $field;
    }
}