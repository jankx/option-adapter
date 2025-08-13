# Jankx OptionsReader Filters Usage Guide

## Tổng Quan

`OptionsReader` class trong Jankx framework cung cấp nhiều filters để plugins và child themes có thể nhúng options của họ vào theme một cách linh hoạt.

## Các Filters Có Sẵn

### 1. Directory Filters

#### `jankx/option/directory/path`
**Mục đích**: Sửa đổi đường dẫn thư mục options mặc định
```php
add_filter('jankx/option/directory/path', function($path) {
    return get_stylesheet_directory() . '/custom-options';
});
```

#### `jankx/option/directories`
**Mục đích**: Sửa đổi tất cả thư mục options
```php
add_filter('jankx/option/directories', function($directories) {
    // Sửa đổi danh sách thư mục
    return $directories;
});
```

#### `jankx/option/custom_directories`
**Mục đích**: Thêm thư mục options tùy chỉnh
```php
add_filter('jankx/option/custom_directories', function($customDirs) {
    $customDirs[] = get_stylesheet_directory() . '/plugin-options';
    $customDirs[] = WP_PLUGIN_DIR . '/my-plugin/options';
    return $customDirs;
});
```

### 2. Pages Configuration Filters

#### `jankx/option/pages_config`
**Mục đích**: Sửa đổi cấu hình pages
```php
add_filter('jankx/option/pages_config', function($pagesConfig) {
    // Sửa đổi cấu hình pages
    return $pagesConfig;
});
```

#### `jankx/option/custom_pages`
**Mục đích**: Thêm pages tùy chỉnh
```php
add_filter('jankx/option/custom_pages', function($customPages) {
    $customPages['my_custom_page'] = [
        'title' => 'My Custom Page',
        'menu_title' => 'Custom Page',
        'capability' => 'manage_options',
        'menu_slug' => 'my-custom-page',
        'icon' => 'dashicons-admin-generic',
        'position' => 30,
    ];
    return $customPages;
});
```

### 3. Sections Filters

#### `jankx/option/sections_for_page`
**Mục đích**: Sửa đổi sections cho page cụ thể
```php
add_filter('jankx/option/sections_for_page', function($sections, $pageId) {
    if ($pageId === 'my_page') {
        // Sửa đổi sections
    }
    return $sections;
}, 10, 2);
```

#### `jankx/option/custom_sections_for_page`
**Mục đích**: Thêm sections tùy chỉnh cho page cụ thể
```php
add_filter('jankx/option/custom_sections_for_page', function($customSections, $pageId) {
    if ($pageId === 'my_page') {
        $customSections['my_custom_section'] = [
            'title' => 'My Custom Section',
            'description' => 'This is a custom section',
            'fields' => [
                // Field definitions
            ],
        ];
    }
    return $customSections;
}, 10, 2);
```

### 4. Fields Filters

#### `jankx/option/fields`
**Mục đích**: Sửa đổi fields cho section cụ thể
```php
add_filter('jankx/option/fields', function($fields, $sectionTitle) {
    if ($sectionTitle === 'my_section') {
        // Sửa đổi fields
    }
    return $fields;
}, 10, 2);
```

#### `jankx/option/custom_fields_data`
**Mục đích**: Thêm fields tùy chỉnh cho section cụ thể
```php
add_filter('jankx/option/custom_fields_data', function($customFields, $sectionTitle) {
    if ($sectionTitle === 'my_section') {
        $customFields[] = [
            'id' => 'my_custom_field',
            'type' => 'text',
            'title' => 'My Custom Field',
            'description' => 'This is a custom field',
        ];
    }
    return $customFields;
}, 10, 2);
```

### 5. Configuration Filters

#### `jankx/option/all_configurations`
**Mục đích**: Sửa đổi tất cả configurations
```php
add_filter('jankx/option/all_configurations', function($configurations) {
    // Sửa đổi tất cả configurations
    return $configurations;
});
```

#### `jankx/option/custom_configurations`
**Mục đích**: Thêm configurations tùy chỉnh
```php
add_filter('jankx/option/custom_configurations', function($customConfigs) {
    $customConfigs['my_page'] = [
        'my_section' => [
            'title' => 'My Section',
            'fields' => [
                // Field definitions
            ],
        ],
    ];
    return $customConfigs;
});
```

## Sử Dụng Trong Child Theme

### Ví dụ 1: Thêm Page Options Tùy Chỉnh

```php
// Trong functions.php của child theme
add_action('init', function() {
    // Thêm custom page
    add_filter('jankx/option/custom_pages', function($customPages) {
        $customPages['child_theme_options'] = [
            'title' => 'Child Theme Options',
            'menu_title' => 'Child Options',
            'capability' => 'manage_options',
            'menu_slug' => 'child-theme-options',
            'icon' => 'dashicons-admin-customizer',
            'position' => 60,
        ];
        return $customPages;
    });

    // Thêm custom sections
    add_filter('jankx/option/custom_sections_for_page', function($customSections, $pageId) {
        if ($pageId === 'child_theme_options') {
            $customSections['general_settings'] = [
                'title' => 'General Settings',
                'description' => 'Configure general settings for child theme',
                'fields' => [
                    [
                        'id' => 'custom_logo',
                        'type' => 'image',
                        'title' => 'Custom Logo',
                        'description' => 'Upload custom logo for child theme',
                    ],
                    [
                        'id' => 'custom_color',
                        'type' => 'color',
                        'title' => 'Custom Color',
                        'description' => 'Choose custom color scheme',
                        'default' => '#007cba',
                    ],
                ],
            ];
        }
        return $customSections;
    });
});
```

### Ví dụ 2: Sửa Đổi Options Có Sẵn

```php
// Sửa đổi page có sẵn
add_filter('jankx/option/pages_config', function($pagesConfig) {
    if (isset($pagesConfig['theme_options'])) {
        $pagesConfig['theme_options']['menu_title'] = 'Theme Settings (Modified)';
    }
    return $pagesConfig;
});

// Sửa đổi fields có sẵn
add_filter('jankx/option/fields', function($fields, $sectionTitle) {
    if ($sectionTitle === 'header_settings') {
        foreach ($fields as &$field) {
            if ($field['id'] === 'site_logo') {
                $field['description'] = 'Modified description for site logo';
            }
        }
    }
    return $fields;
}, 10, 2);
```

## Sử Dụng Trong Plugin

### Ví dụ: Plugin Options

```php
// Trong plugin file
class MyPluginOptions {
    public function __construct() {
        add_action('init', [$this, 'registerOptions']);
    }

    public function registerOptions() {
        // Thêm custom page
        add_filter('jankx/option/custom_pages', function($customPages) {
            $customPages['my_plugin_options'] = [
                'title' => 'My Plugin Options',
                'menu_title' => 'Plugin Options',
                'capability' => 'manage_options',
                'menu_slug' => 'my-plugin-options',
                'icon' => 'dashicons-admin-plugins',
                'position' => 70,
            ];
            return $customPages;
        });

        // Thêm custom sections
        add_filter('jankx/option/custom_sections_for_page', function($customSections, $pageId) {
            if ($pageId === 'my_plugin_options') {
                $customSections['plugin_settings'] = [
                    'title' => 'Plugin Settings',
                    'description' => 'Configure plugin behavior',
                    'fields' => [
                        [
                            'id' => 'enable_feature',
                            'type' => 'checkbox',
                            'title' => 'Enable Feature',
                            'description' => 'Enable or disable main feature',
                            'default' => true,
                        ],
                        [
                            'id' => 'api_key',
                            'type' => 'text',
                            'title' => 'API Key',
                            'description' => 'Enter your API key',
                        ],
                    ],
                ];
            }
            return $customSections;
        });
    }
}

new MyPluginOptions();
```

## Hook Priority

Các filters được thực thi theo thứ tự sau:

1. **Child Theme** (Priority: Highest)
2. **Custom Directories** (Priority: High)
3. **Parent Theme** (Priority: Medium)
4. **Fallback** (Priority: Low)

## Lưu Ý Quan Trọng

1. **Hook Timing**: Sử dụng `init` action để đảm bảo WordPress đã sẵn sàng
2. **Capability Check**: Luôn kiểm tra quyền truy cập phù hợp
3. **Data Validation**: Validate dữ liệu trước khi trả về
4. **Performance**: Tránh thực hiện các operation nặng trong filters
5. **Compatibility**: Kiểm tra compatibility với các plugins khác

## Debug và Troubleshooting

### Kiểm tra filters có sẵn
```php
$optionsReader = Jankx\Adapter\Options\OptionsReader::getInstance();
$availableFilters = $optionsReader->getAvailableFilters();
error_log('Available filters: ' . print_r($availableFilters, true));
```

### Debug custom options
```php
add_action('jankx/option/register_custom_options', function($pageId, $pageConfig, $sectionsConfig) {
    error_log("Custom options registered for page: $pageId");
    error_log("Page config: " . print_r($pageConfig, true));
    error_log("Sections config: " . print_r($sectionsConfig, true));
}, 10, 3);
```

## Kết Luận

Với hệ thống filters mạnh mẽ này, plugins và child themes có thể dễ dàng mở rộng và tùy chỉnh options của Jankx theme một cách linh hoạt và an toàn.
