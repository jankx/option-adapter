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
     * @param array $page Page configuration
     * @return array Redux section
     */
    public static function transformPage($page)
    {
        $section = [
            'id' => $page['id'],
            'title' => $page['name'],
            'desc' => isset($page['args']['description']) ? $page['args']['description'] : '',
            'fields' => [],
        ];

        // Add icon if exists
        if (isset($page['args']['icon'])) {
            $section['icon'] = $page['args']['icon'];
        }

        // Add priority if exists
        if (isset($page['args']['priority'])) {
            $section['priority'] = $page['args']['priority'];
        }

        return $section;
    }

    /**
     * Transform section configuration to Redux fields
     *
     * @param array $section Section configuration
     * @return array Redux fields
     */
    public static function transformSection($section)
    {
        $fields = [];

        if (isset($section['fields'])) {
            foreach ($section['fields'] as $field) {
                $fields[] = self::transformField($field);
            }
        }

        return $fields;
    }

    /**
     * Transform field configuration to Redux field
     *
     * @param array $field Field configuration
     * @return array Redux field
     */
    public static function transformField($field)
    {
        $reduxField = [
            'id' => $field['id'],
            'type' => self::mapFieldType($field['type']),
            'title' => $field['name'],
        ];

        // Add subtitle
        if (isset($field['sub_title'])) {
            $reduxField['subtitle'] = $field['sub_title'];
        }

        // Add description
        if (isset($field['description'])) {
            $reduxField['desc'] = $field['description'];
        }

        // Add default value
        if (isset($field['default_value'])) {
            $reduxField['default'] = $field['default_value'];
        }

        // Add WordPress native support
        if (isset($field['wordpress_native']) && $field['wordpress_native']) {
            $reduxField['wordpress_native'] = true;
            if (isset($field['option_name'])) {
                $reduxField['option_name'] = $field['option_name'];
            }
        }

        // Add field-specific options
        $reduxField = self::addFieldOptions($reduxField, $field);

        return $reduxField;
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
     * @param array $field Original field configuration
     * @return array Updated Redux field
     */
    public static function addFieldOptions($reduxField, $field)
    {
        switch ($field['type']) {
            case 'select':
                if (isset($field['options'])) {
                    $reduxField['options'] = $field['options'];
                }
                break;

            case 'radio':
                if (isset($field['options'])) {
                    $reduxField['options'] = $field['options'];
                }
                break;

            case 'slider':
                if (isset($field['min'])) {
                    $reduxField['min'] = $field['min'];
                }
                if (isset($field['max'])) {
                    $reduxField['max'] = $field['max'];
                }
                if (isset($field['step'])) {
                    $reduxField['step'] = $field['step'];
                }
                break;

            case 'image_select':
                if (isset($field['options'])) {
                    $reduxField['options'] = $field['options'];
                }
                break;

            case 'typography':
                if (isset($field['options'])) {
                    $reduxField['google'] = isset($field['options']['google']) ? $field['options']['google'] : true;
                    $reduxField['font-family'] = isset($field['options']['font-family']) ? $field['options']['font-family'] : true;
                    $reduxField['font-size'] = isset($field['options']['font-size']) ? $field['options']['font-size'] : true;
                    $reduxField['font-weight'] = isset($field['options']['font-weight']) ? $field['options']['font-weight'] : true;
                    $reduxField['line-height'] = isset($field['options']['line-height']) ? $field['options']['line-height'] : true;
                    $reduxField['color'] = isset($field['options']['color']) ? $field['options']['color'] : true;
                }
                break;

            case 'repeater':
                if (isset($field['fields'])) {
                    $reduxField['fields'] = [];
                    foreach ($field['fields'] as $subField) {
                        $reduxField['fields'][] = self::transformField($subField);
                    }
                }
                break;

            case 'sorter':
                if (isset($field['options'])) {
                    $reduxField['options'] = $field['options'];
                }
                break;

            case 'media':
                if (isset($field['options'])) {
                    $reduxField['preview_size'] = isset($field['options']['preview_size']) ? $field['options']['preview_size'] : 'medium';
                    $reduxField['library_filter'] = isset($field['options']['library_filter']) ? $field['options']['library_filter'] : [];
                }
                break;

            case 'gallery':
                if (isset($field['options'])) {
                    $reduxField['preview_size'] = isset($field['options']['preview_size']) ? $field['options']['preview_size'] : 'medium';
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