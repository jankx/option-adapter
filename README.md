# Jankx Option Adapter

Jankx Option Adapter là một hệ thống thông minh cho phép Jankx Framework hoạt động với nhiều framework options khác nhau. Bạn có thể chọn framework yêu thích của mình.

## 🏗️ Architecture Overview

### **1. Core Framework Flow**

```mermaid
graph TD
    A[Jankx Framework] --> B[Framework.php]
    B --> C[getInstance]
    C --> D[loadFramework]
    D --> E[Check External Config]
    E -->|Yes| F[Use External Framework]
    E -->|No| G[Check WordPress Option]
    G -->|Yes| H[Use Saved Framework]
    G -->|No| I[Auto-Detect Framework]
    I --> J[Detect Available Frameworks]
    J -->|Jankx| K[JankxOptionFramework]
    J -->|Redux| L[ReduxFramework]
    J -->|Kirki| M[KirkiFramework]
    J -->|None| N[WordPressSettingAPI]

    F --> O[Active Framework Instance]
    H --> O
    K --> O
    L --> O
    M --> O
    N --> O
```

### **2. Adapter Pattern Structure**

```mermaid
classDiagram
    class Adapter {
        <<abstract>>
        +prepare()
        +convertFieldsToArgs()
        +convertSectionToArgs()
        +mapSectionFields()
        +mapFieldProperties()
    }

    class JankxOptionFramework {
        +setArgs()
        +addSection()
        +getOption()
        +register_admin_menu()
        +createSections()
    }

    class ReduxFramework {
        +setArgs()
        +addSection()
        +getOption()
        +register_admin_menu()
        +createSections()
    }

    class KirkiFramework {
        +setArgs()
        +addSection()
        +getOption()
        +register_admin_menu()
        +createSections()
    }

    class WordPressSettingAPI {
        +setArgs()
        +addSection()
        +getOption()
        +register_admin_menu()
        +createSections()
    }

    Adapter <|-- JankxOptionFramework
    Adapter <|-- ReduxFramework
    Adapter <|-- KirkiFramework
    Adapter <|-- WordPressSettingAPI
```

### **3. Configuration Flow**

```mermaid
graph LR
    A[Config Files] --> B[ConfigRepository]
    B --> C[OptionsReader]
    C --> D[Framework Adapter]
    D --> E[UI Framework]

    subgraph "Config Structure"
        A1[pages.php] --> A2[general/sections.php]
        A2 --> A3[general/fields.php]
        A1 --> A4[colors/sections.php]
        A4 --> A5[colors/fields.php]
    end

    subgraph "Repository Pattern"
        B1[loadConfigurations] --> B2[makePage]
        B2 --> B3[makeSection]
        B3 --> B4[makeField]
    end
```

### **4. Data Flow Architecture**

```mermaid
graph TB
    subgraph "Configuration Layer"
        A[Config Files] --> B[ConfigRepository]
        B --> C[OptionsReader]
    end

    subgraph "Framework Layer"
        D[Framework.php] --> E[Framework Detection]
        E --> F[Adapter Selection]
        F --> G[Active Framework]
    end

    subgraph "Interface Layer"
        H[Helper.php] --> I[getOption]
        I --> J[Framework.getOption]
    end

    C --> G
    G --> J

    subgraph "UI Layer"
        K[Jankx Dashboard] --> L[React UI]
        M[Redux Framework] --> N[Redux UI]
        O[Kirki Framework] --> P[Customizer UI]
        Q[WordPress API] --> R[Native UI]
    end

    G --> K
    G --> M
    G --> O
    G --> Q
```

### **5. Framework Detection Logic**

```mermaid
flowchart TD
    A[Start Detection] --> B[External Config Set?]
    B -->|Yes| C[Use External Framework]
    B -->|No| D[WordPress Option Exists?]
    D -->|Yes| E[Use Saved Framework]
    D -->|No| F[Auto-Detect Available]

    F --> G[Check Jankx Dashboard]
    G -->|Available| H[Select Jankx Framework]
    G -->|Not Available| I[Check Redux]
    I -->|Available| J[Select Redux Framework]
    I -->|Not Available| K[Check Kirki]
    K -->|Available| L[Select Kirki Framework]
    K -->|Not Available| M[Use WordPress API]

    C --> N[Load Framework Adapter]
    E --> N
    H --> N
    J --> N
    L --> N
    M --> N

    N --> O[Initialize Framework]
    O --> P[Register Admin Menu]
    P --> Q[Create Sections]
    Q --> R[Framework Ready]
```

### **6. Helper Function Flow**

```mermaid
sequenceDiagram
    participant U as User Code
    participant H as Helper.php
    participant F as Framework.php
    participant A as Active Adapter
    participant W as WordPress DB

    U->>H: getOption('primary_color', '#007cba')
    H->>H: Check pre-filter
    H->>F: getActiveFramework()
    F->>A: getOption('primary_color', '#007cba')
    A->>W: get_option('jankx_theme_primary_color')
    W->>A: Return value or null
    A->>H: Return value or default
    H->>U: Return final value
```

### **7. Configuration Structure**

```mermaid
graph TD
    A[includes/options/] --> B[pages.php]
    A --> C[general/]
    A --> D[colors/]
    A --> E[typography/]
    A --> F[layout/]

    B --> G[Page Config]
    C --> H[sections.php]
    C --> I[fields.php]
    D --> J[sections.php]
    D --> K[fields.php]
    E --> L[sections.php]
    E --> M[fields.php]
    F --> N[sections.php]
    F --> O[fields.php]

    H --> P[Section Config]
    I --> Q[Field Config]
    J --> P
    K --> Q
    L --> P
    M --> Q
    N --> P
    O --> Q
```

### **8. Method Call Flow**

```mermaid
graph LR
    A[Framework::getInstance] --> B[loadFramework]
    B --> C[detectFramework]
    C --> D[getFrameworkFromConfig]
    D --> E[setFrameworkFromExternal]

    F[OptionsReader::getInstance] --> G[getPages]
    G --> H[getSections]
    H --> I[getFields]

    J[Helper::getOption] --> K[getActiveFramework]
    K --> L[framework.getOption]

    M[ConfigRepository] --> N[loadConfigurations]
    N --> O[makePage]
    O --> P[makeSection]
    P --> Q[makeField]
```

## 🎯 Theme Options Hierarchy Structure

### **1. Main Hierarchy Flow**

```mermaid
graph TD
    A[Theme Options] --> B[Page 1: General]
    A --> C[Page 2: Header]
    A --> D[Page 3: Footer]
    A --> E[Page 4: Colors]
    A --> F[Page 5: Typography]
    A --> G[Page 6: Layout]
    A --> H[Page 7: Social]
    A --> I[Page 8: Advanced]

    B --> B1[Section 1: Site Info]
    B --> B2[Section 2: Logo]
    B --> B3[Section 3: Favicon]

    C --> C1[Section 1: Header Style]
    C --> C2[Section 2: Navigation]
    C --> C3[Section 3: Sticky Header]

    D --> D1[Section 1: Footer Style]
    D --> D2[Section 2: Footer Content]
    D --> D3[Section 3: Footer Widgets]

    E --> E1[Section 1: Primary Colors]
    E --> E2[Section 2: Secondary Colors]
    E --> E3[Section 3: Text Colors]

    F --> F1[Section 1: Body Typography]
    F --> F2[Section 2: Heading Typography]
    F --> F3[Section 3: Button Typography]

    G --> G1[Section 1: Container]
    G --> G2[Section 2: Sidebar]
    G --> G3[Section 3: Grid]

    H --> H1[Section 1: Social Links]
    H --> H2[Section 2: Social Icons]

    I --> I1[Section 1: Custom CSS]
    I --> I2[Section 2: Custom JS]
    I --> I3[Section 3: Analytics]
```

### **2. Detailed Page Structure**

```mermaid
graph TD
    subgraph "Page: General Settings"
        P1[General Page] --> S1[Site Info Section]
        P1 --> S2[Logo Section]
        P1 --> S3[Favicon Section]

        S1 --> F1[Site Title Field]
        S1 --> F2[Site Description Field]
        S1 --> F3[Tagline Field]

        S2 --> F4[Logo Image Field]
        S2 --> F5[Logo Width Field]
        S2 --> F6[Logo Height Field]

        S3 --> F7[Favicon Image Field]
        S3 --> F8[Apple Touch Icon Field]
    end

    subgraph "Page: Colors"
        P2[Colors Page] --> S4[Primary Colors Section]
        P2 --> S5[Secondary Colors Section]
        P2 --> S6[Text Colors Section]

        S4 --> F9[Primary Color Field]
        S4 --> F10[Primary Hover Field]

        S5 --> F11[Secondary Color Field]
        S5 --> F12[Secondary Hover Field]

        S6 --> F13[Text Color Field]
        S6 --> F14[Link Color Field]
        S6 --> F15[Link Hover Field]
    end
```

### **3. Data Structure Relationships**

```mermaid
erDiagram
    THEME_OPTIONS ||--o{ PAGES : contains
    PAGES ||--o{ SECTIONS : contains
    SECTIONS ||--o{ FIELDS : contains

    THEME_OPTIONS {
        string name
        string version
        string description
        array config
    }

    PAGES {
        string title
        string icon
        int priority
        array sections
    }

    SECTIONS {
        string title
        string description
        array fields
        string icon
    }

    FIELDS {
        string id
        string type
        string title
        string description
        mixed default_value
        array args
    }
```

### **4. Configuration File Structure**

```mermaid
graph TD
    A[includes/options/] --> B[pages.php]
    A --> C[general/]
    A --> D[header/]
    A --> E[footer/]
    A --> F[colors/]
    A --> G[typography/]
    A --> H[layout/]
    A --> I[social/]
    A --> J[advanced/]

    B --> B1[Page Configs]

    C --> C1[sections.php]
    C --> C2[fields.php]

    D --> D1[sections.php]
    D --> D2[fields.php]

    E --> E1[sections.php]
    E --> E2[fields.php]

    F --> F1[sections.php]
    F --> F2[fields.php]

    G --> G1[sections.php]
    G --> G2[fields.php]

    H --> H1[sections.php]
    H --> H2[fields.php]

    I --> I1[sections.php]
    I --> I2[fields.php]

    J --> J1[sections.php]
    J --> J2[fields.php]
```

### **5. Class Relationships**

```mermaid
classDiagram
    class ThemeOptions {
        +getInstance()
        +addPage()
        +getPages()
        +getPage()
    }

    class Page {
        +getTitle()
        +getSections()
        +addSection()
        +getIcon()
        +getPriority()
    }

    class Section {
        +getTitle()
        +getFields()
        +addField()
        +getDescription()
        +getIcon()
    }

    class Field {
        +getId()
        +getType()
        +getTitle()
        +getValue()
        +getDefault()
        +getArgs()
    }

    ThemeOptions ||--o{ Page : contains
    Page ||--o{ Section : contains
    Section ||--o{ Field : contains
```

### **6. Navigation Flow**

```mermaid
graph LR
    A[Admin Menu] --> B[Theme Options]
    B --> C[Page 1: General]
    B --> D[Page 2: Header]
    B --> E[Page 3: Footer]
    B --> F[Page 4: Colors]
    B --> G[Page 5: Typography]
    B --> H[Page 6: Layout]
    B --> I[Page 7: Social]
    B --> J[Page 8: Advanced]

    C --> C1[Section 1: Site Info]
    C --> C2[Section 2: Logo]
    C --> C3[Section 3: Favicon]

    C1 --> F1[Site Title]
    C1 --> F2[Site Description]
    C1 --> F3[Tagline]

    C2 --> F4[Logo Image]
    C2 --> F5[Logo Width]
    C2 --> F6[Logo Height]
```

### **7. Data Loading Flow**

```mermaid
sequenceDiagram
    participant A as Admin
    participant T as ThemeOptions
    participant P as Page
    participant S as Section
    participant F as Field
    participant R as Repository

    A->>T: Load Theme Options
    T->>R: getPages()
    R->>T: Return Pages Array
    T->>P: Load Page Sections
    P->>R: getSections()
    R->>P: Return Sections Array
    P->>S: Load Section Fields
    S->>R: getFields()
    R->>S: Return Fields Array
    S->>F: Render Field
    F->>A: Display Field UI
```

### **8. Example Configuration Structure**

```mermaid
graph TD
    subgraph "Theme Options Configuration"
        A[Theme Options] --> B[General Page]
        A --> C[Header Page]
        A --> D[Colors Page]

        B --> B1[Site Info Section]
        B --> B2[Logo Section]

        B1 --> F1[Site Title Field]
        B1 --> F2[Site Description Field]

        B2 --> F3[Logo Image Field]
        B2 --> F4[Logo Width Field]

        C --> C1[Header Style Section]
        C --> C2[Navigation Section]

        C1 --> F5[Header Layout Field]
        C1 --> F6[Sticky Header Field]

        D --> D1[Primary Colors Section]
        D --> D2[Secondary Colors Section]

        D1 --> F7[Primary Color Field]
        D1 --> F8[Primary Hover Field]

        D2 --> F9[Secondary Color Field]
        D2 --> F10[Secondary Hover Field]
    end
```

## 🎯 Key Concepts

### **1. Adapter Pattern**
- **Abstract Adapter**: Base class cho tất cả frameworks
- **Concrete Adapters**: Implementation cho từng framework
- **Interface Consistency**: Tất cả adapters implement cùng interface

### **2. Repository Pattern**
- **ConfigRepository**: Quản lý configuration data
- **OptionsReader**: Interface để đọc options
- **File-based Config**: Configuration từ PHP files

### **3. Factory Pattern**
- **Framework Detection**: Tự động chọn framework phù hợp
- **Adapter Creation**: Tạo adapter instance
- **Fallback Mechanism**: Luôn có fallback option

### **4. Singleton Pattern**
- **Framework Instance**: Chỉ có 1 instance của framework
- **OptionsReader**: Singleton cho reading operations
- **Helper Functions**: Global access point

### **5. One-to-Many Relationships**
- **1 Theme Options** → **Many Pages**
- **1 Page** → **Many Sections**
- **1 Section** → **Many Fields**

### **6. Navigation Structure**
- **Admin Menu** → **Theme Options**
- **Theme Options** → **Page Navigation**
- **Page** → **Section Tabs**
- **Section** → **Field Forms**

### **7. Data Hierarchy**
- **Theme Options** (Root)
  - **Pages** (Level 1)
    - **Sections** (Level 2)
      - **Fields** (Level 3)

### **8. Configuration Files**
- **pages.php** - Define all pages
- **{page}/sections.php** - Define sections for each page
- **{page}/fields.php** - Define fields for each section

## 🚀 Rules & Requirements

### **Rule 1: Call Flow 1 Chiều**
- ✅ **Jankx Framework → option-adapter**: Chỉ có 1 chiều
- ✅ **Không có chiều ngược lại**: option-adapter không gọi lại Jankx Framework
- ✅ **Public Interface**: Chỉ expose các methods cần thiết

### **Rule 2: Menu Title Registration**
- ✅ **Adapter Interface**: Tất cả adapters phải implement `register_admin_menu()`
- ✅ **Framework Detection**: Tự động detect và load framework
- ✅ **Menu Configuration**: Set menu title, position, icon qua adapter

### **Rule 3: Modify option-adapter**
- ✅ **Flexible Architecture**: Có thể modify option-adapter
- ✅ **Extensible Design**: Dễ dàng thêm features mới
- ✅ **Backward Compatibility**: Không break existing functionality

### **Rule 4: Child Theme Override Support**
- ✅ **Directory Priority**: Child → Parent → Framework → Fallback
- ✅ **File Override**: Child theme có thể override từng file
- ✅ **Configuration Merge**: Preserve parent config nếu child không override

### **Rule 5: Standard Data Structure**
- ✅ **Format Chuẩn**: Theo cấu trúc từ `tests/configs/`
- ✅ **Field Properties**: Standard field properties
- ✅ **Security Checks**: ABSPATH check trong tất cả files

### **Rule 6: WordPress Native Field Support**
- ✅ **Direct Integration**: Fields có thể thao tác trực tiếp với WordPress
- ✅ **Action Hooks**: Support actions để chỉnh sửa WordPress data
- ✅ **Automatic Sync**: Tự động sync với WordPress options

### **Rule 7: Service Provider Integration**
- ✅ **ThemeOptionsServiceProvider**: Tạo theme options qua service provider
- ✅ **Dependency Injection**: Sử dụng Application container
- ✅ **Lifecycle Management**: Proper register/boot phases

### **Rule 8: Textdomain Loading Order**
- ✅ **After Textdomain**: Theme options load sau khi setup textdomain
- ✅ **Translation Support**: Tất cả text strings được translate
- ✅ **Hook Priority**: Proper WordPress hook priorities

## Benefits

### **1. Flexibility**
- ✅ Hỗ trợ nhiều framework options
- ✅ Dễ dàng switch giữa frameworks
- ✅ Auto-detection thông minh

### **2. Maintainability**
- ✅ Clean separation of concerns
- ✅ Interface-based design
- ✅ Easy to extend

### **3. Developer Experience**
- ✅ Simple helper functions
- ✅ Configuration-based setup
- ✅ Automatic conversion

### **4. Performance**
- ✅ Lazy loading
- ✅ Caching mechanisms
- ✅ Efficient detection

### **5. Internationalization**
- ✅ Translation support
- ✅ RTL language support
- ✅ WordPress standards compliance

## Usage Examples

### **Cách 1: Auto-Detection (Khuyến nghị)**
```php
// functions.php
use Jankx\Adapter\Options\Framework;
use Jankx\Adapter\Options\Helper;

// Auto-detect và load framework
$optionFramework = Framework::getInstance();
$optionFramework->loadFramework();

// Sử dụng helper
$primary_color = Helper::getOption('primary_color', '#007cba');
```

### **Cách 2: Force Framework**
```php
// functions.php
use Jankx\Adapter\Options\Framework;

// Force sử dụng Jankx Dashboard Framework
Framework::setFrameworkFromExternal('jankx');
$optionFramework = Framework::getInstance();
$optionFramework->loadFramework();
```

### **Cách 3: Configuration-Based**
```php
// Tạo config files
// includes/options/pages.php
// includes/options/general/sections.php
// includes/options/general/fields.php

// Framework sẽ tự động load từ config
```

### **Cách 4: Service Provider (Recommended)**
```php
// app/Providers/ThemeOptionsServiceProvider.php
class ThemeOptionsServiceProvider extends ServiceProvider
{
    public function register(Application $app)
    {
        // Register option-adapter services
    }

    public function boot(Application $app)
    {
        // Boot theme options after textdomain
        add_action('after_setup_theme', [$this, 'bootThemeOptions'], 20);
    }
}

// config/providers.php
'admin' => [
    Jankx\Support\Providers\TranslationServiceProvider::class,
    \App\Providers\ThemeOptionsServiceProvider::class,
],
```

## Supported Frameworks

### **1. Jankx Dashboard Framework**
- ✅ UI đẹp với React
- ✅ Tích hợp hoàn toàn với Jankx Framework
- ✅ Configuration-based setup

### **2. Redux Framework**
- ✅ Nhiều field types
- ✅ Advanced features
- ✅ Large community

### **3. Kirki Framework**
- ✅ WordPress Customizer integration
- ✅ Typography controls
- ✅ Color controls

### **4. WordPress Settings API**
- ✅ Native WordPress
- ✅ Đơn giản, nhẹ
- ✅ Không cần framework bên ngoài

## Framework Priority

```
1. Jankx Dashboard Framework (nếu có)
2. Redux Framework (nếu có)
3. Kirki Framework (nếu có)
4. WordPress Settings API (fallback)
```

## Child Theme Override

### **Directory Priority System**
```
1. Child Theme: get_stylesheet_directory() . '/includes/options/'
2. Parent Theme: get_template_directory() . '/includes/options/'
3. Framework: JANKX_ABSPATH . '/includes/options/'
4. Fallback: option-adapter/tests/configs/
```

### **Override Examples**
```php
// Child theme: includes/options/pages.php
return [
    [
        'id' => 'general',
        'name' => 'General Settings (Custom)',
        'args' => [
            'description' => 'Customized general settings',
        ],
    ],
];

// Child theme: includes/options/general/site_info.php
return [
    'id' => 'site_info',
    'name' => 'Site Information (Custom)',
    'fields' => [
        [
            'id' => 'site_title',
            'name' => 'Site Title',
            'type' => 'text',
            'default_value' => 'My Custom Website',
        ],
    ],
];
```

## WordPress Native Fields

### **Supported Native Fields**
- `blogname` - Site Title
- `blogdescription` - Tagline
- `siteurl` - Site URL
- `home` - Home URL
- `date_format` - Date Format
- `time_format` - Time Format
- `timezone_string` - Timezone

### **Configuration Example**
```php
[
    'id' => 'site_title',
    'name' => 'Site Title',
    'type' => 'text',
    'wordpress_native' => true,
    'option_name' => 'blogname',
    'default_value' => get_option('blogname'),
    'description' => 'This will update WordPress Site Title',
]
```

---

**Version**: 1.0.0
**Author**: Puleeno Nguyen
**License**: MIT
