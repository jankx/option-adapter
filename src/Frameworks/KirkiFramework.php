<?php

/**
 * Kirki Customizer Framework Adapter
 *
 * Kirki allows theme developers to build themes quicker & more easily.
 *
 * With over 30 custom controls ranging from simple sliders to complex typography controls
 * with Google-Fonts integration and features like automatic CSS & postMessage script generation,
 * Kirki makes theme development a breeze.
 *
 * @package Jankx
 * @subpackage Option
 * @author Puleeno Nguyen <puleeno@gmail.com>
 * @link https://kirki.org/
 * @since 1.0.0
 */

namespace Jankx\Adapter\Options\Frameworks;

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

use Jankx\Adapter\Options\Abstracts\Adapter;
use Jankx\Adapter\Options\Transformers\KirkiTransformer;

class KirkiFramework extends Adapter
{
    protected $configId = 'buocchandisan_theme_options';
    protected $panels = [];
    protected $sections = [];
    protected $fields = [];
    protected $themeOptions;

    protected static $mapSectionFields = array();
    protected static $mapFieldProperties = array();

    public function __construct()
    {
        // Prepare Kirki (like ReduxFramework)
        $this->prepare();

        // Khởi tạo Kirki config
        $this->initKirkiConfig();

        // Add hooks to ensure proper initialization (like ReduxFramework)
        add_action('init', [$this, 'forceInit'], 20);
        add_action('admin_init', [$this, 'forceInit'], 20);
        add_action('customize_register', [$this, 'forceInit'], 20);

        // Force create menu immediately
        add_action('admin_menu', function() {
            if (method_exists('\Kirki', 'init')) {
                \Kirki::init($this->configId);
            }
        }, 5); // Higher priority

        // Force create menu in admin_init hook
        add_action('admin_init', function() {
            if (method_exists('\Kirki', 'init')) {
                \Kirki::init($this->configId);
            }
        });

        // Force create menu in init hook
        add_action('init', function() {
            if (method_exists('\Kirki', 'init')) {
                \Kirki::init($this->configId);
            }
        });
    }

    /**
     * Prepare Kirki (like ReduxFramework)
     */
    public function prepare()
    {
        $this->configId = apply_filters(
            'jankx_option_kirki_framework_config_id',
            preg_replace(array(
                '/[^\w|^_]/',
                '/_{2,}/'
            ), '_', get_template())
        );
    }

    /**
     * Force Kirki initialization
     */
    public function forceInit()
    {
        if (method_exists('\Kirki', 'init')) {
            \Kirki::init($this->configId);
        }
    }

    protected function initKirkiConfig()
    {
        // Tạo Kirki config
        \Kirki::add_config($this->configId, [
            'option_type' => 'theme_mod',
            'capability' => 'edit_theme_options',
        ]);
    }

    public function setArgs($args)
    {
        // Không cần thiết cho Kirki, nhưng giữ để tương thích với ReduxFramework
    }

    public function addSection($section)
    {
        if (!is_array($section) || !isset($section['id']) || !isset($section['title'])) {
            return;
        }

        $sectionId = $section['id'];

        // Tạo section args
        $sectionArgs = [
            'title' => $section['title'],
            'priority' => $section['priority'] ?? 30,
        ];

        if (isset($section['description'])) {
            $sectionArgs['description'] = $section['description'];
        }
        if (isset($section['icon'])) {
            $sectionArgs['icon'] = $section['icon'];
        }

        // Tạo section trong Kirki
        \Kirki::add_section($sectionId, $sectionArgs);

        // Lưu section để tham chiếu
        $this->sections[$sectionId] = $section;

        // Tạo fields cho section này
        if (isset($section['fields']) && is_array($section['fields'])) {
            foreach ($section['fields'] as $field) {
                $this->addField($sectionId, $field);
            }
        }
    }

    public function addField($sectionId, $field)
    {
        if (!is_array($field) || !isset($field['id']) || !isset($field['type'])) {
            return;
        }

        $fieldId = $field['id'];

        // Tạo field args
        $fieldArgs = [
            'settings' => $fieldId,
            'type' => $this->mapFieldType($field['type']),
            'section' => $sectionId,
            'label' => $field['title'] ?? $field['id'],
        ];

        // Thêm default value nếu có
        if (isset($field['default'])) {
            $fieldArgs['default'] = $field['default'];
        }

        // Thêm các thuộc tính tùy chọn
        if (isset($field['subtitle'])) {
            $fieldArgs['description'] = $field['subtitle'];
        }
        if (isset($field['description'])) {
            $fieldArgs['description'] = $field['description'];
        }
        if (isset($field['priority'])) {
            $fieldArgs['priority'] = $field['priority'];
        }

        // Thêm các thuộc tính đặc biệt cho từng loại field
        switch ($field['type']) {
            case 'select':
            case 'radio':
                if (isset($field['options'])) {
                    $fieldArgs['choices'] = $field['options'];
                }
                break;
            case 'slider':
                if (isset($field['min'])) {
                    $fieldArgs['input_attrs']['min'] = $field['min'];
                }
                if (isset($field['max'])) {
                    $fieldArgs['input_attrs']['max'] = $field['max'];
                }
                if (isset($field['step'])) {
                    $fieldArgs['input_attrs']['step'] = $field['step'];
                }
                break;
            case 'spacing':
                if (isset($field['default']) && is_array($field['default'])) {
                    $fieldArgs['default'] = [
                        'top' => $field['default']['top'] ?? '',
                        'right' => $field['default']['right'] ?? '',
                        'bottom' => $field['default']['bottom'] ?? '',
                        'left' => $field['default']['left'] ?? '',
                    ];
                }
                break;
        }

        // Tạo field trong Kirki
        \Kirki::add_field($this->configId, $fieldArgs);

        // Lưu field để tham chiếu
        $this->fields[$fieldId] = $field;
    }

    public static function mapSectionFields()
    {
        return static::$mapSectionFields;
    }

    public static function mapFieldProperties()
    {
        return static::$mapFieldProperties;
    }

    public function getOption($name, $defaultValue = null)
    {
        if (is_null($this->themeOptions)) {
            $this->themeOptions = get_theme_mods();
        }

        if (isset($this->themeOptions[$name])) {
            return $this->themeOptions[$name];
        }

        return $defaultValue;
    }

    /**
     * Get Kirki configuration
     */
    public function getKirkiConfig()
    {
        return [
            'config_id' => $this->configId,
            'sections' => $this->sections,
            'fields' => $this->fields,
        ];
    }

    /**
     * Set WordPress native field value
     */
    public function setWordPressNativeValue($fieldId, $value)
    {
        // Get current theme mods
        $themeMods = get_theme_mods();

        // Update field value
        $themeMods[$fieldId] = $value;

        // Save theme mods
        $result = set_theme_mods($themeMods);

        // Clear cache
        $this->themeOptions = null;

        return $result;
    }

    /**
     * Get WordPress native field value
     */
    public function getWordPressNativeValue($fieldId, $defaultValue = null)
    {
        $themeMods = get_theme_mods();
        return isset($themeMods[$fieldId]) ? $themeMods[$fieldId] : $defaultValue;
    }

    public function register_admin_menu($menu_title, $display_name)
    {
        // Kirki tự động tạo menu trong Customizer
        // Không cần làm gì thêm

        // Force Kirki to create the menu immediately (like ReduxFramework)
        if (method_exists('\Kirki', 'init')) {
            \Kirki::init($this->configId);
        }

        // Add hook to ensure Kirki menu is created
        add_action('admin_menu', function() {
            if (method_exists('\Kirki', 'init')) {
                \Kirki::init($this->configId);
            }
        }, 5); // Higher priority

        // Force create menu immediately
        add_action('admin_init', function() {
            if (method_exists('\Kirki', 'init')) {
                \Kirki::init($this->configId);
            }
        });

        // Force create menu in init hook
        add_action('init', function() {
            if (method_exists('\Kirki', 'init')) {
                \Kirki::init($this->configId);
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
        if (!class_exists('\Kirki')) {
            return;
        }

        // Transform OptionsReader data to Kirki format
        $kirkiData = KirkiTransformer::transformOptionsReader($optionsReader, $this);

        // Add sections to Kirki
        foreach ($kirkiData['sections'] as $section) {
            $this->addSection($section);
        }

        // Force Kirki to create the menu after adding sections (like ReduxFramework)
        if (method_exists('\Kirki', 'init')) {
            \Kirki::init($this->configId);
        }
    }

    /**
     * Transform WordPress dashicons to Kirki icons
     *
     * @param string $dashicon WordPress dashicon
     * @return string Kirki icon
     */
    public function transformIcon($dashicon)
    {
        // Kirki có thể sử dụng dashicons trực tiếp
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

    /**
     * Map field types từ Jankx sang Kirki
     */
    protected function mapFieldType($jankxType)
    {
        $typeMap = [
            'text' => 'text',
            'textarea' => 'textarea',
            'select' => 'select',
            'radio' => 'radio',
            'checkbox' => 'checkbox',
            'switch' => 'switch',
            'color' => 'color',
            'image' => 'image',
            'slider' => 'slider',
            'spacing' => 'spacing',
            'typography' => 'typography',
            'icon' => 'text', // Kirki không có icon field, dùng text
        ];

        return $typeMap[$jankxType] ?? 'text';
    }
}
