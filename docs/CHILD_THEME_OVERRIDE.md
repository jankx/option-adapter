# Child Theme Override Support

Jankx Option Adapter hỗ trợ child theme override để bạn có thể tùy chỉnh theme options mà không cần modify parent theme.

## 🏗️ Directory Priority System

```
1. Child Theme: get_stylesheet_directory() . '/includes/options/'
2. Parent Theme: get_template_directory() . '/includes/options/'
3. Framework: JANKX_ABSPATH . '/includes/options/'
4. Fallback: option-adapter/tests/configs/
```

## 📁 Cấu trúc Child Theme Override

### **Child Theme Structure:**
```
child-theme/
├── includes/
│   └── options/
│       ├── pages.php (Override parent pages)
│       ├── general/
│       │   ├── site_info.php (Override section)
│       │   └── logo_settings.php (Add new section)
│       └── colors/
│           └── primary_colors.php (Override colors)
```

### **Parent Theme Structure:**
```
parent-theme/
├── includes/
│   └── options/
│       ├── pages.php
│       ├── general/
│       │   ├── site_info.php
│       │   └── favicon_settings.php
│       └── colors/
│           ├── primary_colors.php
│           └── secondary_colors.php
```

## 🎯 Override Examples

### **1. Override Pages (pages.php)**

#### **Parent Theme:**
```php
<?php
return [
    [
        'id' => 'general',
        'name' => 'General Settings',
        'args' => [
            'description' => 'General theme settings',
        ],
    ],
    [
        'id' => 'colors',
        'name' => 'Color Settings',
        'args' => [
            'description' => 'Theme color customization',
        ],
    ],
];
```

#### **Child Theme Override:**
```php
<?php
return [
    [
        'id' => 'general',
        'name' => 'General Settings (Custom)',
        'args' => [
            'description' => 'Customized general settings',
        ],
    ],
    [
        'id' => 'colors',
        'name' => 'Color Settings',
        'args' => [
            'description' => 'Theme color customization',
        ],
    ],
    [
        'id' => 'custom',
        'name' => 'Custom Settings',
        'args' => [
            'description' => 'Additional custom settings',
        ],
    ],
];
```

### **2. Override Sections**

#### **Parent Theme (general/site_info.php):**
```php
<?php
return [
    'id' => 'site_info',
    'name' => 'Site Information',
    'description' => 'Basic site information settings',
    'fields' => [
        [
            'id' => 'site_title',
            'name' => 'Site Title',
            'type' => 'text',
            'default_value' => 'My Website',
        ],
        [
            'id' => 'site_description',
            'name' => 'Site Description',
            'type' => 'textarea',
            'default_value' => '',
        ],
    ],
];
```

#### **Child Theme Override (general/site_info.php):**
```php
<?php
return [
    'id' => 'site_info',
    'name' => 'Site Information (Custom)',
    'description' => 'Customized site information settings',
    'fields' => [
        [
            'id' => 'site_title',
            'name' => 'Site Title',
            'type' => 'text',
            'default_value' => 'My Custom Website',
        ],
        [
            'id' => 'site_description',
            'name' => 'Site Description',
            'type' => 'textarea',
            'default_value' => 'Custom description',
        ],
        [
            'id' => 'custom_field',
            'name' => 'Custom Field',
            'type' => 'text',
            'default_value' => 'Custom value',
        ],
    ],
];
```

### **3. Add New Sections**

#### **Child Theme (general/logo_settings.php):**
```php
<?php
return [
    'id' => 'logo_settings',
    'name' => 'Logo Settings',
    'description' => 'Custom logo settings for child theme',
    'fields' => [
        [
            'id' => 'custom_logo',
            'name' => 'Custom Logo',
            'type' => 'image',
            'default_value' => '',
        ],
        [
            'id' => 'logo_width',
            'name' => 'Logo Width',
            'type' => 'slider',
            'default_value' => 200,
        ],
    ],
];
```

## 🔧 Usage Examples

### **1. Enable/Disable Child Theme Override**

```php
// Disable child theme override
$optionsReader = OptionsReader::getInstance();
$optionsReader->setChildThemeOverrideEnabled(false);

// Enable child theme override (default)
$optionsReader->setChildThemeOverrideEnabled(true);
```

### **2. Get Options Directories**

```php
$optionsReader = OptionsReader::getInstance();
$directories = $optionsReader->getOptionsDirectories();

// Output:
// [
//     '/path/to/child-theme/includes/options',
//     '/path/to/parent-theme/includes/options',
//     '/path/to/framework/includes/options',
//     '/path/to/fallback/configs'
// ]
```

### **3. Load Configuration with Override**

```php
$optionsReader = OptionsReader::getInstance();

// Load pages.php with override support
$pages = $optionsReader->loadConfiguration('pages.php');

// Load specific section with override support
$section = $optionsReader->loadConfiguration('general/site_info.php');
```

### **4. Find File in Directories**

```php
$optionsReader = OptionsReader::getInstance();

// Find file with priority
$filePath = $optionsReader->findFileInDirectories('general/site_info.php');

// Returns first found file path or null
```

## 🎨 Advanced Override Techniques

### **1. Conditional Override**

```php
// Child theme: general/site_info.php
<?php
$parentConfig = include get_template_directory() . '/includes/options/general/site_info.php';

return array_merge($parentConfig, [
    'fields' => array_merge($parentConfig['fields'], [
        [
            'id' => 'additional_field',
            'name' => 'Additional Field',
            'type' => 'text',
            'default_value' => '',
        ],
    ]),
]);
```

### **2. Filter-Based Override**

```php
// Child theme: functions.php
add_filter('jankx/option/directories', function($directories) {
    // Add custom directory
    $directories[] = get_stylesheet_directory() . '/custom-options';
    return $directories;
});
```

### **3. Dynamic Override**

```php
// Child theme: functions.php
add_filter('jankx/option/directory/path', function($path) {
    if (is_admin()) {
        return get_stylesheet_directory() . '/admin-options';
    }
    return $path;
});
```

## 🚀 Best Practices

### **1. File Organization**
- Sử dụng cùng cấu trúc thư mục như parent theme
- Đặt tên file giống parent theme để override
- Tạo file mới để thêm sections/pages

### **2. Configuration Management**
- Backup parent theme configuration trước khi override
- Test override trên development environment
- Document các thay đổi override

### **3. Performance**
- Child theme override không ảnh hưởng performance
- File được load theo priority, dừng khi tìm thấy
- Cache được sử dụng cho configuration

### **4. Compatibility**
- Override tương thích với tất cả frameworks
- Fallback mechanism đảm bảo hoạt động
- Backward compatibility với parent theme

## 🔍 Debugging

### **1. Check Override Status**

```php
$optionsReader = OptionsReader::getInstance();
$directories = $optionsReader->getOptionsDirectories();

foreach ($directories as $directory) {
    echo "Directory: " . $directory . "\n";
    echo "Exists: " . (is_dir($directory) ? 'Yes' : 'No') . "\n";
}
```

### **2. Check File Priority**

```php
$optionsReader = OptionsReader::getInstance();
$filePath = $optionsReader->findFileInDirectories('pages.php');

if ($filePath) {
    echo "Found file: " . $filePath . "\n";
} else {
    echo "File not found in any directory\n";
}
```

### **3. Load Configuration Debug**

```php
$optionsReader = OptionsReader::getInstance();
$config = $optionsReader->loadConfiguration('pages.php');

if ($config) {
    echo "Configuration loaded successfully\n";
    print_r($config);
} else {
    echo "Configuration not found\n";
}
```

---

**Version**: 1.0.0
**Author**: Puleeno Nguyen
**License**: MIT