# Jankx Option Adapter

Một package framework-agnostic để tích hợp các option frameworks khác nhau vào WordPress themes.

## Tổng quan

Option Adapter cung cấp một interface thống nhất để làm việc với các option frameworks khác nhau (Redux, Kirki, WordPress Settings API, Customizer) mà không cần hardcode framework-specific logic vào theme.

## Kiến trúc

```mermaid
graph TD
    A[Theme] --> B[Option Adapter]
    B --> C[Framework Detection]
    C --> D[Adapter Selection]
    D --> E[Redux Adapter]
    D --> F[Kirki Adapter]
    D --> G[WordPress Settings API Adapter]
    D --> H[Customizer Adapter]
    D --> I[Jankx Dashboard Adapter]

    E --> J[Redux Transformer]
    F --> K[Kirki Transformer]
    G --> L[WordPress Transformer]
    H --> M[Customizer Transformer]
    I --> N[Native Transformer]

    J --> O[Redux Framework]
    K --> P[Kirki Framework]
    L --> Q[WordPress Settings API]
    M --> R[WordPress Customizer]
    N --> S[Jankx Dashboard]
```

## Cấu trúc thư mục

```
src/
├── Abstracts/
│   └── Adapter.php              # Interface cho tất cả adapters
├── Frameworks/
│   ├── ReduxFramework.php       # Adapter cho Redux Framework
│   ├── KirkiFramework.php       # Adapter cho Kirki Framework
│   ├── WordPressSettingAPI.php  # Adapter cho WordPress Settings API
│   ├── CustomizeFramework.php   # Adapter cho WordPress Customizer
│   └── JankxOptionFramework.php # Native adapter cho Jankx Dashboard
├── Transformers/
│   ├── ReduxTransformer.php     # Transformer cho Redux
│   ├── KirkiTransformer.php     # Transformer cho Kirki
│   ├── WordPressTransformer.php # Transformer cho WordPress Settings API
│   └── CustomizeTransformer.php # Transformer cho Customizer
├── Repositories/
│   └── ConfigRepository.php     # Repository để load config
├── Specs/
│   ├── Page.php                 # Page specification
│   ├── Section.php              # Section specification
│   └── Field.php                # Field specification
├── Framework.php                # Core framework detection & loading
└── OptionsReader.php            # Reader cho options data
```

## Core Components

### 1. Framework.php
Core class để detect và load option framework phù hợp.

**Methods:**
- `detectFramework()` - Detect framework có sẵn
- `getFrameworkFromConfig()` - Lấy framework từ config
- `setFrameworkFromExternal()` - Set framework từ bên ngoài
- `getAdapter()` - Lấy adapter instance

### 2. OptionsReader.php
Đọc và parse options data từ files.

**Methods:**
- `getPages()` - Lấy danh sách pages
- `getSections($pageTitle)` - Lấy sections của page
- `getFields($sectionTitle)` - Lấy fields của section
- `setOptionsDirectoryPath($path)` - Set path cho options directory

### 3. Adapter Interface
Interface chung cho tất cả adapters.

**Methods:**
- `createSections($optionsReader)` - Tạo sections cho framework
- `transformIcon($dashicon)` - Transform icon cho framework

## Supported Frameworks

### 1. Redux Framework
- **Adapter:** `ReduxFramework`
- **Transformer:** `ReduxTransformer`
- **Icon Mapping:** `dashicons` → `elusiveicons`

### 2. Kirki Framework
- **Adapter:** `KirkiFramework`
- **Transformer:** `KirkiTransformer`
- **Icon Mapping:** `dashicons` (direct)

### 3. WordPress Settings API
- **Adapter:** `WordPressSettingAPI`
- **Transformer:** `WordPressTransformer`
- **Icon Mapping:** `dashicons` (direct)

### 4. WordPress Customizer
- **Adapter:** `CustomizeFramework`
- **Transformer:** `CustomizeTransformer`
- **Icon Mapping:** `dashicons` (direct)

### 5. Jankx Dashboard (Native)
- **Adapter:** `JankxOptionFramework`
- **Transformer:** Native (không cần transformer)
- **Icon Mapping:** `dashicons` (direct)

## Usage

### 1. Basic Usage

```php
use Jankx\Adapter\Options\Framework as OptionFramework;

// Initialize framework
$framework = OptionFramework::getInstance();

// Get adapter
$adapter = $framework->getAdapter();

// Create sections
$optionsReader = OptionsReader::getInstance();
$optionsReader->setOptionsDirectoryPath('resources/options');
$adapter->createSections($optionsReader);
```

### 2. Configuration

```php
// Trong config/app.php
'options' => [
    'framework' => 'redux', // hoặc 'kirki', 'wordpress', 'customize', 'jankx'
    'display_name' => 'Theme Options',
    'menu_title' => 'Theme Options',
    'page_slug' => 'theme-options',
    'dev_mode' => true,
],
```

### 3. Options Data Structure

```
resources/options/
├── pages.php                    # Pages configuration
├── general/                     # Page directory
│   ├── sections.php            # Sections cho page
│   ├── site-information/       # Section directory
│   │   └── fields.php         # Fields cho section
│   └── social-media/           # Section directory
│       └── fields.php         # Fields cho section
└── typography/                 # Page directory
    ├── sections.php
    └── body-typography/
        └── fields.php
```

### 4. Page Configuration (pages.php)

```php
return [
    'general' => [
        'title' => 'General Settings',
        'icon' => 'dashicons-admin-generic',
        'priority' => 30,
    ],
    'typography' => [
        'title' => 'Typography',
        'icon' => 'dashicons-editor-textcolor',
        'priority' => 30,
    ],
];
```

### 5. Section Configuration (sections.php)

```php
return [
    'site-information' => [
        'title' => 'Site Information',
        'description' => 'Basic site settings',
    ],
    'social-media' => [
        'title' => 'Social Media',
        'description' => 'Social media links',
    ],
];
```

### 6. Field Configuration (fields.php)

```php
return [
    'site_title' => [
        'type' => 'text',
        'title' => 'Site Title',
        'subtitle' => 'Main site title',
        'description' => 'Enter your site title',
        'default' => 'Bookix - Book Store',
    ],
    'site_logo' => [
        'type' => 'image',
        'title' => 'Site Logo',
        'subtitle' => 'Upload logo',
        'description' => 'Upload your site logo',
    ],
];
```

## Field Types Support

### Basic Fields
- `text` - Text input
- `textarea` - Textarea
- `image` - Media upload
- `color` - Color picker
- `select` - Select dropdown
- `radio` - Radio buttons
- `checkbox` - Checkbox
- `switch` - Toggle switch

### Advanced Fields
- `slider` - Range slider
- `typography` - Typography settings
- `background` - Background settings
- `spacing` - Spacing controls
- `image_select` - Image select
- `gallery` - Gallery upload
- `repeater` - Repeater field
- `sorter` - Sortable list

## Icon Transformation

Mỗi framework có cách xử lý icons khác nhau:

### Redux Framework
```php
// dashicons → elusiveicons
'dashicons-admin-generic' => 'el el-cog'
'dashicons-editor-textcolor' => 'el el-font'
'dashicons-art' => 'el el-picture'
```

### Other Frameworks
```php
// Sử dụng dashicons trực tiếp
'dashicons-admin-generic' => 'dashicons-admin-generic'
```

## Child Theme Support

Option Adapter tự động hỗ trợ child theme overrides:

1. **Child theme priority** - Đọc từ child theme trước
2. **Parent theme fallback** - Fallback về parent theme
3. **Deep merge** - Merge arrays một cách thông minh

## Framework Detection Priority

1. **External configuration** - `setFrameworkFromExternal()`
2. **Config file** - `Config::get('app.options.framework')`
3. **WordPress options** - Database stored preference
4. **Auto detection** - Detect available frameworks

## Error Handling

- **Graceful degradation** - Fallback khi framework không có sẵn
- **Debug logging** - Log chi tiết cho development
- **Exception handling** - Catch và handle exceptions

## Performance

- **Lazy loading** - Chỉ load khi cần
- **Caching** - Cache configuration và data
- **Memory efficient** - Tối ưu memory usage

## Development

### Adding New Framework

1. **Create Adapter:**
```php
class NewFrameworkAdapter implements Adapter
{
    public function createSections($optionsReader) { /* ... */ }
    public function transformIcon($dashicon) { /* ... */ }
}
```

2. **Create Transformer:**
```php
class NewFrameworkTransformer
{
    public static function transformPage($page) { /* ... */ }
    public static function transformField($field) { /* ... */ }
}
```

3. **Register in Framework:**
```php
// Trong Framework.php
protected $supportedFrameworks = [
    'newframework' => NewFrameworkAdapter::class,
];
```

### Testing

```php
// Test framework detection
$framework = OptionFramework::getInstance();
$adapter = $framework->getAdapter();
assert($adapter instanceof ExpectedAdapter);

// Test icon transformation
$icon = $adapter->transformIcon('dashicons-admin-generic');
assert($icon === 'expected-icon');
```

## License

MIT License - Xem file LICENSE để biết thêm chi tiết.
