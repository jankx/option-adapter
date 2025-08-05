# Options Directory Structure

Option Adapter sử dụng cấu trúc thư mục linh hoạt để quản lý theme options với hỗ trợ child theme override.

## 📁 **Cấu trúc thư mục**

```
theme/
├── includes/
│   └── options/
│       ├── pages.php (Pages configuration)
│       ├── page_id_1/
│       │   ├── section_1.php
│       │   ├── section_2.php
│       │   └── section_3.php
│       ├── page_id_2/
│       │   ├── section_1.php
│       │   └── section_2.php
│       └── page_id_3/
│           ├── section_1.php
│           └── section_2.php
```

## 🎯 **Priority System**

OptionsReader sử dụng hệ thống priority để load configurations:

### **Priority 1: Child Theme (Highest)**
```
child-theme/includes/options/
├── pages.php
├── page_id_1/
│   ├── section_1.php
│   └── section_2.php
```

### **Priority 2: Parent Theme**
```
parent-theme/includes/options/
├── pages.php
├── page_id_1/
│   ├── section_1.php
│   └── section_2.php
```

### **Priority 3: Jankx Framework**
```
jankx-framework/includes/options/
├── pages.php
├── page_id_1/
│   ├── section_1.php
│   └── section_2.php
```

### **Priority 4: Fallback (Tests)**
```
option-adapter/tests/configs/
├── pages.php
├── page_id_1/
│   ├── section_1.php
│   └── section_2.php
```

## 📋 **File Structure Examples**

### **1. pages.php**
```php
<?php
return [
    [
        'id' => 'page_id_1',
        'name' => 'General Settings',
        'icon' => 'dashicons-admin-generic'
    ],
    [
        'id' => 'page_id_2',
        'name' => 'Typography',
        'icon' => 'dashicons-editor-textcolor'
    ],
    [
        'id' => 'page_id_3',
        'name' => 'Colors',
        'icon' => 'dashicons-art'
    ]
];
```

### **2. Section Files**
```php
// page_id_1/section_1.php
<?php
return [
    'name' => 'Site Information',
    'fields' => [
        [
            'id' => 'site_title',
            'name' => 'Site Title',
            'type' => 'text',
            'value' => 'My Website',
            'description' => 'Enter your site title'
        ],
        [
            'id' => 'site_description',
            'name' => 'Site Description',
            'type' => 'textarea',
            'value' => '',
            'description' => 'Enter your site description'
        ]
    ]
];
```

```php
// page_id_1/section_2.php
<?php
return [
    'name' => 'Logo Settings',
    'fields' => [
        [
            'id' => 'site_logo',
            'name' => 'Site Logo',
            'type' => 'image',
            'value' => '',
            'description' => 'Upload your site logo'
        ],
        [
            'id' => 'logo_width',
            'name' => 'Logo Width',
            'type' => 'slider',
            'min' => 50,
            'max' => 300,
            'step' => 10,
            'value' => 150,
            'description' => 'Set logo width in pixels'
        ]
    ]
];
```

## 🔄 **Child Theme Override**

### **Override Rules**

1. **Complete Override**: Child theme có thể override toàn bộ file
2. **Partial Override**: Child theme có thể override từng section
3. **Add New**: Child theme có thể thêm sections mới

### **Example: Child Theme Override**

**Parent Theme:**
```
parent-theme/includes/options/
├── pages.php
└── general/
    ├── site_info.php
    └── logo_settings.php
```

**Child Theme Override:**
```
child-theme/includes/options/
├── pages.php (override parent)
└── general/
    ├── site_info.php (override parent)
    ├── logo_settings.php (override parent)
    └── social_media.php (new section)
```

## 🛠️ **OptionsReader Methods**

### **1. Directory Management**
```php
$optionsReader = OptionsReader::getInstance();

// Get all options directories with priority
$directories = $optionsReader->getOptionsDirectories();

// Get page directories
$pageDirectories = $optionsReader->getPageDirectories();

// Get PHP files from directory
$files = $optionsReader->getPhpFilesFromDirectory('/path/to/directory');
```

### **2. Configuration Loading**
```php
// Load specific configuration file
$config = $optionsReader->loadConfiguration('pages.php');

// Load all configurations
$allConfigs = $optionsReader->loadAllConfigurations();

// Get pages configuration
$pagesConfig = $optionsReader->getPagesConfig();

// Get sections for specific page
$sections = $optionsReader->getSectionsForPage('page_id_1');
```

### **3. File Finding**
```php
// Find file in directories with priority
$filePath = $optionsReader->findFileInDirectories('pages.php');
```

## 🎯 **Usage Examples**

### **1. Basic Setup**
```php
$optionsReader = OptionsReader::getInstance();

// Set custom options directory
$optionsReader->setOptionsDirectoryPath('/custom/path/to/options');

// Disable child theme override
$optionsReader->setChildThemeOverrideEnabled(false);

// Load configurations
$pages = $optionsReader->getPages();
$sections = $optionsReader->getSections('General Settings');
$fields = $optionsReader->getFields('Site Information');
```

### **2. Custom Directory Structure**
```php
// Custom options directory
$customPath = get_template_directory() . '/custom-options';
$optionsReader->setOptionsDirectoryPath($customPath);

// Structure:
// custom-options/
// ├── pages.php
// ├── general/
// │   ├── basic_settings.php
// │   └── advanced_settings.php
// └── styling/
//     ├── colors.php
//     └── typography.php
```

### **3. Child Theme Override**
```php
// Child theme can override any file
// child-theme/includes/options/general/basic_settings.php
return [
    'name' => 'Basic Settings (Overridden)',
    'fields' => [
        [
            'id' => 'site_title',
            'name' => 'Site Title (Custom)',
            'type' => 'text',
            'value' => 'Custom Title',
            'description' => 'Custom description'
        ]
    ]
];
```

## 🔧 **Configuration Mapping**

### **Redux to Dashboard Field Mapping**

| Redux Property | Dashboard Property | Description |
|----------------|-------------------|-------------|
| `id` | `id` | Field identifier |
| `name` | `title` | Field title |
| `type` | `type` | Field type |
| `value` | `default` | Default value |
| `default_value` | `default` | Default value |
| `sub_title` | `subtitle` | Subtitle |
| `description` | `description` | Description |
| `options` | `options` | Select options |
| `min` | `min` | Slider minimum |
| `max` | `max` | Slider maximum |
| `step` | `step` | Slider step |
| `on` | `on` | Switch on text |
| `off` | `off` | Switch off text |
| `transparent` | `transparent` | Color transparency |
| `alpha` | `alpha` | Color alpha |
| `google` | `google` | Google fonts |
| `font-family` | `font-family` | Typography font family |
| `font-size` | `font-size` | Typography font size |
| `font-weight` | `font-weight` | Typography font weight |
| `line-height` | `line-height` | Typography line height |
| `color` | `color` | Typography color |
| `background-*` | `background-*` | Background properties |
| `mode` | `mode` | Spacing mode |
| `units` | `units` | Spacing units |
| `top/right/bottom/left` | `top/right/bottom/left` | Spacing sides |
| `display_value` | `display_value` | Slider display |
| `resolution` | `resolution` | Slider resolution |
| `layout` | `layout` | Radio layout |

## 🚀 **Best Practices**

### **1. File Organization**
```
options/
├── pages.php (Always required)
├── general/ (Page directory)
│   ├── site_info.php
│   ├── logo_settings.php
│   └── social_media.php
├── styling/ (Page directory)
│   ├── colors.php
│   ├── typography.php
│   └── layout.php
└── advanced/ (Page directory)
    ├── performance.php
    └── seo.php
```

### **2. Naming Conventions**
- **Page directories**: Use descriptive names (e.g., `general`, `styling`, `advanced`)
- **Section files**: Use descriptive names (e.g., `site_info.php`, `logo_settings.php`)
- **Field IDs**: Use snake_case (e.g., `site_title`, `logo_width`)

### **3. Child Theme Override**
- **Complete override**: Override entire files for major changes
- **Partial override**: Override specific sections for minor changes
- **Add new**: Add new sections without overriding existing ones

### **4. Performance**
- **Lazy loading**: Files are loaded only when needed
- **Caching**: Consider implementing caching for large configurations
- **Minimal includes**: Keep section files focused and minimal

## 📚 **Related Documentation**

- [Option Adapter README](README.md)
- [Child Theme Override](CHILD_THEME_OVERRIDE.md)
- [Redux Transformer](redux-transformer.md)
- [Dashboard Framework](../dashboard-framework/README.md)