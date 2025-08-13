<?php

namespace Jankx\Adapter\Options\Frameworks;

use Jankx\Adapter\Options\Interfaces\Adapter;
use Jankx\Adapter\Options\OptionsReader;
use Jankx\Adapter\Options\Transformers\CustomizeTransformer;

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

class CustomizeFramework implements Adapter
{
    protected $sections = [];
    protected $args = [];
    protected $optionName = 'bookix_theme_options';

    public function prepare()
    {
        // WordPress Customizer không cần prepare đặc biệt
    }

    public function register_admin_menu($menu_title, $display_name)
    {
        // Customizer không cần register menu riêng, nó có sẵn trong Appearance > Customize
        add_action('customize_register', [$this, 'register_customizer_sections']);
    }

    public function register_customizer_sections($wp_customize)
    {
        foreach ($this->sections as $section) {
            $this->add_customizer_section($wp_customize, $section);
        }
    }

    protected function add_customizer_section($wp_customize, $section)
    {
        $section_id = $section['id'] ?? 'bookix_' . sanitize_title($section['title']);

        // Add section
        $wp_customize->add_section($section_id, [
            'title' => $section['title'] ?? 'Section',
            'priority' => $section['priority'] ?? 30,
        ]);

        // Add fields
        if (isset($section['fields']) && is_array($section['fields'])) {
            foreach ($section['fields'] as $field) {
                $this->add_customizer_field($wp_customize, $section_id, $field);
            }
        }
    }

    protected function add_customizer_field($wp_customize, $section_id, $field)
    {
        $field_id = $field['id'] ?? 'bookix_' . sanitize_title($field['title']);
        $setting_id = $this->optionName . '[' . $field_id . ']';

        // Add setting
        $wp_customize->add_setting($setting_id, [
            'default' => $field['default'] ?? '',
            'sanitize_callback' => $this->get_sanitize_callback($field),
        ]);

        // Add control
        $control_args = [
            'label' => $field['title'] ?? '',
            'description' => $field['subtitle'] ?? '',
            'section' => $section_id,
            'settings' => $setting_id,
        ];

        // Map field type to Customizer control
        $control_type = $this->map_field_type_to_customizer($field);
        if ($control_type) {
            $wp_customize->add_control($setting_id, array_merge($control_args, $control_type));
        }
    }

    protected function map_field_type_to_customizer($field)
    {
        $type = $field['type'] ?? 'text';

        switch ($type) {
            case 'text':
                return ['type' => 'text'];
            case 'textarea':
                return ['type' => 'textarea'];
            case 'checkbox':
                return ['type' => 'checkbox'];
            case 'radio':
                return [
                    'type' => 'radio',
                    'choices' => $field['options'] ?? []
                ];
            case 'select':
                return [
                    'type' => 'select',
                    'choices' => $field['options'] ?? []
                ];
            case 'color':
                return ['type' => 'color'];
            case 'image':
                return ['type' => 'image'];
            case 'number':
                return ['type' => 'number'];
            case 'url':
                return ['type' => 'url'];
            case 'email':
                return ['type' => 'email'];
            default:
                return ['type' => 'text'];
        }
    }

    protected function get_sanitize_callback($field)
    {
        $type = $field['type'] ?? 'text';

        switch ($type) {
            case 'text':
                return 'sanitize_text_field';
            case 'textarea':
                return 'sanitize_textarea_field';
            case 'checkbox':
                return 'rest_sanitize_boolean';
            case 'color':
                return 'sanitize_hex_color';
            case 'url':
                return 'esc_url_raw';
            case 'email':
                return 'sanitize_email';
            case 'number':
                return 'intval';
            default:
                return 'sanitize_text_field';
        }
    }

    public function getOption($name, $defaultValue = null)
    {
        $options = get_option($this->optionName, []);
        return isset($options[$name]) ? $options[$name] : $defaultValue;
    }

        public function createSections($optionsReader)
    {
        // Transform OptionsReader data to Customizer format

        // Transform OptionsReader data to Customizer format
        $customizerData = CustomizeTransformer::transformOptionsReader($optionsReader);

        // Add sections to Customizer
        foreach ($customizerData['sections'] as $section) {
            $this->addSection($section);
        }
    }

    public function addSection($section)
    {
        $this->sections[] = $section;
    }

    public function setArgs($args)
    {
        $this->args = $args;
        if (isset($args['opt_name'])) {
            $this->optionName = $args['opt_name'];
        }
    }

    public function convertSectionToArgs($section)
    {
        return $section;
    }

    public static function mapSectionFields()
    {
        return [
            'title' => 'title',
            'subtitle' => 'subtitle',
            'priority' => 'priority',
        ];
    }

    public static function mapFieldProperties()
    {
        return [
            'title' => 'label',
            'subtitle' => 'description',
            'default' => 'default',
            'type' => 'type',
            'options' => 'choices',
        ];
    }

    /**
     * Transform WordPress dashicons to WordPress Customizer icons
     *
     * @param string $dashicon WordPress dashicon
     * @return string WordPress Customizer icon
     */
    public function transformIcon($dashicon)
    {
        // WordPress Customizer có thể sử dụng dashicons trực tiếp
        $iconMap = [
            'dashicons-admin-generic' => 'dashicons-admin-generic',
            'dashicons-editor-textcolor' => 'dashicons-editor-textcolor',
            'dashicons-art' => 'dashicons-art',
            'dashicons-layout' => 'dashicons-layout',
            'dashicons-align-wide' => 'dashicons-align-wide',
            'dashicons-align-full-width' => 'dashicons-align-full-width',
            'dashicons-admin-post' => 'dashicons-admin-post',
            'dashicons-admin-tools' => 'dashicons-admin-tools',
        ];

        $mappedIcon = isset($iconMap[$dashicon]) ? $iconMap[$dashicon] : 'dashicons-admin-generic';

        return $mappedIcon;
    }
}