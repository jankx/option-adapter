# Redux Transformer

ReduxTransformer là class chuyển đổi configuration từ standard format sang Redux format và ngược lại.

## 🏗️ Overview

ReduxTransformer cung cấp các phương thức để:

- Chuyển đổi standard configuration sang Redux format
- Chuyển đổi Redux format về standard format
- Map field types giữa standard và Redux
- Transform field values cho Redux
- Generate option names cho Redux

## 📋 Methods

### **1. transformConfig($config)**

Chuyển đổi standard configuration sang Redux format.

```php
$config = [
    'pages' => [
        [
            'id' => 'general',
            'name' => 'General Settings',
            'args' => [
                'description' => 'General theme settings',
            ],
        ],
    ],
];

$reduxConfig = ReduxTransformer::transformConfig($config);
```

### **2. transformPage($page)**

Chuyển đổi page configuration sang Redux section.

```php
$page = [
    'id' => 'general',
    'name' => 'General Settings',
    'args' => [
        'description' => 'General theme settings',
        'icon' => 'dashicons-admin-generic',
        'priority' => 10,
    ],
];

$section = ReduxTransformer::transformPage($page);
// Result:
// [
//     'id' => 'general',
//     'title' => 'General Settings',
//     'desc' => 'General theme settings',
//     'icon' => 'dashicons-admin-generic',
//     'priority' => 10,
//     'fields' => [],
// ]
```

### **3. transformSection($section)**

Chuyển đổi section configuration sang Redux fields.

```php
$section = [
    'id' => 'site_info',
    'name' => 'Site Information',
    'fields' => [
        [
            'id' => 'site_title',
            'name' => 'Site Title',
            'type' => 'text',
            'default_value' => 'My Site',
        ],
    ],
];

$fields = ReduxTransformer::transformSection($section);
```

### **4. transformField($field)**

Chuyển đổi field configuration sang Redux field.

```php
$field = [
    'id' => 'site_title',
    'name' => 'Site Title',
    'type' => 'text',
    'sub_title' => 'Enter your site title',
    'description' => 'This will be displayed in browser tab',
    'default_value' => 'My Site',
    'wordpress_native' => true,
    'option_name' => 'blogname',
];

$reduxField = ReduxTransformer::transformField($field);
// Result:
// [
//     'id' => 'site_title',
//     'type' => 'text',
//     'title' => 'Site Title',
//     'subtitle' => 'Enter your site title',
//     'desc' => 'This will be displayed in browser tab',
//     'default' => 'My Site',
//     'wordpress_native' => true,
//     'option_name' => 'blogname',
// ]
```

### **5. mapFieldType($type)**

Map standard field type sang Redux field type.

```php
$reduxType = ReduxTransformer::mapFieldType('image'); // Returns 'media'
$reduxType = ReduxTransformer::mapFieldType('text'); // Returns 'text'
$reduxType = ReduxTransformer::mapFieldType('color'); // Returns 'color'
```

### **6. addFieldOptions($reduxField, $field)**

Thêm field-specific options cho Redux field.

```php
$field = [
    'id' => 'container_width',
    'type' => 'slider',
    'min' => 800,
    'max' => 1600,
    'step' => 50,
];

$reduxField = [
    'id' => 'container_width',
    'type' => 'slider',
];

$updatedField = ReduxTransformer::addFieldOptions($reduxField, $field);
// Result:
// [
//     'id' => 'container_width',
//     'type' => 'slider',
//     'min' => 800,
//     'max' => 1600,
//     'step' => 50,
// ]
```

### **7. transformCompleteConfig($config)**

Chuyển đổi complete configuration structure sang Redux format.

```php
$config = [
    'display_name' => 'Bookix Options',
    'menu_title' => 'Theme Options',
    'menu_position' => 60,
    'dev_mode' => true,
    'customizer' => true,
    'import_export' => true,
    'pages' => [
        [
            'id' => 'general',
            'name' => 'General Settings',
            'sections' => [
                [
                    'id' => 'site_info',
                    'name' => 'Site Information',
                    'fields' => [
                        [
                            'id' => 'site_title',
                            'name' => 'Site Title',
                            'type' => 'text',
                        ],
                    ],
                ],
            ],
        ],
    ],
];

$reduxConfig = ReduxTransformer::transformCompleteConfig($config);
```

### **8. generateOptionName()**

Generate option name cho Redux dựa trên theme name.

```php
$optionName = ReduxTransformer::generateOptionName();
// Returns: 'bookix_theme_options' (if theme name is 'Bookix')
```

### **9. transformFieldValue($value, $type)**

Transform field value cho Redux.

```php
$typographyValue = ReduxTransformer::transformFieldValue('', 'typography');
// Returns:
// [
//     'font-family' => 'Arial, sans-serif',
//     'font-size' => '16px',
//     'font-weight' => '400',
//     'line-height' => '1.6',
//     'color' => '#333333',
// ]

$colorValue = ReduxTransformer::transformFieldValue('', 'color');
// Returns: '#007cba'
```

### **10. transformBackToStandard($reduxField)**

Chuyển đổi Redux field về standard format.

```php
$reduxField = [
    'id' => 'site_title',
    'type' => 'text',
    'title' => 'Site Title',
    'subtitle' => 'Enter your site title',
    'desc' => 'This will be displayed in browser tab',
    'default' => 'My Site',
    'wordpress_native' => true,
    'option_name' => 'blogname',
];

$field = ReduxTransformer::transformBackToStandard($reduxField);
// Result:
// [
//     'id' => 'site_title',
//     'type' => 'text',
//     'name' => 'Site Title',
//     'sub_title' => 'Enter your site title',
//     'description' => 'This will be displayed in browser tab',
//     'default_value' => 'My Site',
//     'wordpress_native' => true,
//     'option_name' => 'blogname',
// ]
```

### **11. mapFieldTypeBack($type)**

Map Redux field type về standard type.

```php
$standardType = ReduxTransformer::mapFieldTypeBack('media'); // Returns 'image'
$standardType = ReduxTransformer::mapFieldTypeBack('text'); // Returns 'text'
$standardType = ReduxTransformer::mapFieldTypeBack('color'); // Returns 'color'
```

### **12. addFieldOptionsBack($field, $reduxField)**

Thêm field-specific options về standard format.

```php
$field = ['id' => 'test'];
$reduxField = [
    'id' => 'test',
    'type' => 'slider',
    'min' => 0,
    'max' => 100,
    'step' => 5,
];

$updatedField = ReduxTransformer::addFieldOptionsBack($field, $reduxField);
// Result:
// [
//     'id' => 'test',
//     'min' => 0,
//     'max' => 100,
//     'step' => 5,
// ]
```

## 🎨 Field Type Mapping

| Standard Type | Redux Type | Description |
|---------------|------------|-------------|
| `text` | `text` | Text input |
| `textarea` | `textarea` | Multi-line text |
| `image` | `media` | Media upload |
| `icon` | `icon` | Icon picker |
| `color` | `color` | Color picker |
| `select` | `select` | Dropdown select |
| `radio` | `radio` | Radio buttons |
| `checkbox` | `checkbox` | Checkbox |
| `switch` | `switch` | Toggle switch |
| `slider` | `slider` | Range slider |
| `typography` | `typography` | Typography settings |
| `image_select` | `image_select` | Image select |
| `gallery` | `gallery` | Gallery upload |
| `repeater` | `repeater` | Repeater fields |
| `sorter` | `sorter` | Sortable fields |

## 🔧 Field-Specific Options

### **Typography Fields**

```php
$field = [
    'id' => 'body_typography',
    'type' => 'typography',
    'options' => [
        'google' => true,
        'font-family' => true,
        'font-size' => true,
        'font-weight' => true,
        'line-height' => true,
        'color' => true,
    ],
];

$reduxField = ReduxTransformer::transformField($field);
// Result:
// [
//     'id' => 'body_typography',
//     'type' => 'typography',
//     'google' => true,
//     'font-family' => true,
//     'font-size' => true,
//     'font-weight' => true,
//     'line-height' => true,
//     'color' => true,
// ]
```

### **Slider Fields**

```php
$field = [
    'id' => 'container_width',
    'type' => 'slider',
    'min' => 800,
    'max' => 1600,
    'step' => 50,
];

$reduxField = ReduxTransformer::transformField($field);
// Result:
// [
//     'id' => 'container_width',
//     'type' => 'slider',
//     'min' => 800,
//     'max' => 1600,
//     'step' => 50,
// ]
```

### **Repeater Fields**

```php
$field = [
    'id' => 'social_links',
    'type' => 'repeater',
    'fields' => [
        [
            'id' => 'social_icon',
            'name' => 'Icon',
            'type' => 'icon',
        ],
        [
            'id' => 'social_url',
            'name' => 'URL',
            'type' => 'text',
        ],
    ],
];

$reduxField = ReduxTransformer::transformField($field);
// Result:
// [
//     'id' => 'social_links',
//     'type' => 'repeater',
//     'fields' => [
//         [
//             'id' => 'social_icon',
//             'type' => 'icon',
//             'title' => 'Icon',
//         ],
//         [
//             'id' => 'social_url',
//             'type' => 'text',
//             'title' => 'URL',
//         ],
//     ],
// ]
```

### **Media Fields**

```php
$field = [
    'id' => 'site_logo',
    'type' => 'media',
    'options' => [
        'preview_size' => 'medium',
        'library_filter' => ['image'],
    ],
];

$reduxField = ReduxTransformer::transformField($field);
// Result:
// [
//     'id' => 'site_logo',
//     'type' => 'media',
//     'preview_size' => 'medium',
//     'library_filter' => ['image'],
// ]
```

## 🚀 WordPress Native Support

ReduxTransformer hỗ trợ WordPress native fields:

```php
$field = [
    'id' => 'site_title',
    'name' => 'Site Title',
    'type' => 'text',
    'wordpress_native' => true,
    'option_name' => 'blogname',
];

$reduxField = ReduxTransformer::transformField($field);
// Result:
// [
//     'id' => 'site_title',
//     'type' => 'text',
//     'title' => 'Site Title',
//     'wordpress_native' => true,
//     'option_name' => 'blogname',
// ]
```

## 🧪 Testing

ReduxTransformer có đầy đủ test coverage:

```bash
# Run tests
vendor/bin/phpunit tests/ReduxTransformerTest.php
```

### **Test Cases**

- `testTransformPage()` - Test page transformation
- `testTransformField()` - Test field transformation
- `testMapFieldType()` - Test field type mapping
- `testTransformFieldWithOptions()` - Test field with options
- `testTransformFieldWithSlider()` - Test slider field
- `testTransformFieldWithTypography()` - Test typography field
- `testTransformFieldWithRepeater()` - Test repeater field
- `testTransformCompleteConfig()` - Test complete config transformation
- `testGenerateOptionName()` - Test option name generation
- `testTransformFieldValue()` - Test field value transformation
- `testTransformBackToStandard()` - Test reverse transformation
- `testMapFieldTypeBack()` - Test reverse field type mapping
- `testAddFieldOptionsBack()` - Test reverse field options

## 📝 Usage Examples

### **1. Basic Usage**

```php
use Jankx\Adapter\Options\Transformers\ReduxTransformer;

// Transform standard config to Redux
$standardConfig = [
    'pages' => [
        [
            'id' => 'general',
            'name' => 'General Settings',
            'sections' => [
                [
                    'id' => 'site_info',
                    'name' => 'Site Information',
                    'fields' => [
                        [
                            'id' => 'site_title',
                            'name' => 'Site Title',
                            'type' => 'text',
                        ],
                    ],
                ],
            ],
        ],
    ],
];

$reduxConfig = ReduxTransformer::transformCompleteConfig($standardConfig);
```

### **2. Field Transformation**

```php
// Transform individual field
$field = [
    'id' => 'primary_color',
    'name' => 'Primary Color',
    'type' => 'color',
    'default_value' => '#007cba',
];

$reduxField = ReduxTransformer::transformField($field);
```

### **3. Reverse Transformation**

```php
// Transform Redux field back to standard
$reduxField = [
    'id' => 'site_title',
    'type' => 'text',
    'title' => 'Site Title',
    'default' => 'My Site',
];

$field = ReduxTransformer::transformBackToStandard($reduxField);
```

## 🎯 Best Practices

### **1. Configuration Structure**
- ✅ Sử dụng standard format cho configuration
- ✅ Để ReduxTransformer handle transformation
- ✅ Không hardcode Redux-specific properties

### **2. Field Types**
- ✅ Sử dụng standard field types
- ✅ Map field types qua ReduxTransformer
- ✅ Support WordPress native fields

### **3. Testing**
- ✅ Test tất cả transformation methods
- ✅ Test field type mapping
- ✅ Test reverse transformation

### **4. Performance**
- ✅ Cache transformed configurations
- ✅ Avoid redundant transformations
- ✅ Use efficient data structures

---

**Version**: 1.0.0
**Author**: Puleeno Nguyen
**License**: MIT