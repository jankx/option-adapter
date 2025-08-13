<?php

namespace Jankx\Adapter\Options;

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

use Jankx\Adapter\Options\Repositories\ConfigRepository;



class OptionsReader
{
    protected static $instance = null;
    protected $sections = array();
    protected $configRepository;

    protected $optionsDirectoryPath = null;
    protected $childThemeOverrideEnabled = true;

    /**
     * Check if path is absolute
     */
    private function isAbsolutePath($path) {
        return $path[0] === '/' || (strlen($path) > 2 && $path[1] === ':' && $path[2] === '\\');
    }

    private function __construct()
    {
        // Không tạo ConfigRepository trong constructor để tránh vòng lặp
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setOptionsDirectoryPath($optionsDirectoryPath)
    {
        $this->optionsDirectoryPath = $optionsDirectoryPath;
    }

    public function setChildThemeOverrideEnabled($enabled)
    {
        $this->childThemeOverrideEnabled = $enabled;
    }

    public function getOptionsDirectoryPath()
    {
        if (is_null($this->optionsDirectoryPath)) {
            // Default fallback path
            $this->optionsDirectoryPath = get_stylesheet_directory() . '/resources/options';
        }

        return apply_filters(
            'jankx/option/directory/path',
            $this->optionsDirectoryPath
        );
    }

    /**
     * Get all possible options directories with priority
     *
     * @return array
     */
    public function getOptionsDirectories()
    {
        $directories = [];

        // If optionsDirectoryPath is set, use it as relative path
        if ($this->optionsDirectoryPath && !$this->isAbsolutePath($this->optionsDirectoryPath)) {
            // Priority 1: Child theme (highest priority)
            if ($this->childThemeOverrideEnabled) {
                // Force check child theme path even if WordPress doesn't recognize it as child theme
                $childThemePath = get_stylesheet_directory() . '/' . $this->optionsDirectoryPath;
                if (is_dir($childThemePath)) {
                    $directories[] = $childThemePath;
                }
            }

            // Priority 2: Parent theme
            $parentThemePath = get_template_directory() . '/' . $this->optionsDirectoryPath;
            if (is_dir($parentThemePath)) {
                $directories[] = $parentThemePath;
            }
        } else {
            // Legacy behavior - use absolute paths
            // Priority 1: Child theme (highest priority)
            if ($this->childThemeOverrideEnabled && is_child_theme()) {
                $childThemePath = get_stylesheet_directory() . '/resources/options';
                if (is_dir($childThemePath)) {
                    $directories[] = $childThemePath;
                }
            }

            // Priority 2: Parent theme
            $parentThemePath = get_template_directory() . '/resources/options';
            if (is_dir($parentThemePath)) {
                $directories[] = $parentThemePath;
            }
        }

        // Priority 3: Fallback to tests configs
        $fallbackPath = __DIR__ . '/../tests/configs';
        if (is_dir($fallbackPath)) {
            $directories[] = $fallbackPath;
        }

        // Priority 4: Allow plugins and child themes to add custom directories
        $customDirectories = apply_filters('jankx/option/custom_directories', []);
        if (!empty($customDirectories)) {
            foreach ($customDirectories as $customDir) {
                if (is_dir($customDir)) {
                    $directories[] = $customDir;
                }
            }
        }

        return apply_filters('jankx/option/directories', $directories);
    }

    /**
     * Find file in directories with priority
     *
     * @param string $relativePath
     * @return string|null
     */
    public function findFileInDirectories($relativePath)
    {
        $directories = $this->getOptionsDirectories();

        foreach ($directories as $directory) {
            $filePath = $directory . '/' . $relativePath;
            if (file_exists($filePath)) {
                return $filePath;
            }
        }

        return null;
    }

    /**
     * Load configuration with child theme override support
     *
     * @param string $relativePath
     * @return array|null
     */
    public function loadConfiguration($relativePath)
    {
        $filePath = $this->findFileInDirectories($relativePath);

        if (!$filePath) {
            return null;
        }

        return include $filePath;
    }

    /**
     * Get all page directories from options directories
     *
     * @return array
     */
    public function getPageDirectories()
    {
        $pageDirectories = [];
        $directories = $this->getOptionsDirectories();

        foreach ($directories as $directory) {
            if (is_dir($directory)) {
                $subdirs = glob($directory . '/*', GLOB_ONLYDIR);
                foreach ($subdirs as $subdir) {
                    $pageDirectories[] = $subdir;
                }
            }
        }

        return array_unique($pageDirectories);
    }

    /**
     * Get all PHP files from a specific directory
     *
     * @param string $directory
     * @return array
     */
    public function getPhpFilesFromDirectory($directory)
    {
        $files = [];

        if (is_dir($directory)) {
            $phpFiles = glob($directory . '/*.php');
            foreach ($phpFiles as $file) {
                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * Load all configurations from options directories
     *
     * @return array
     */
    public function loadAllConfigurations()
    {
        $configurations = [];
        $pageDirectories = $this->getPageDirectories();

        foreach ($pageDirectories as $pageDir) {
            $pageName = basename($pageDir);
            $configurations[$pageName] = [];

            $phpFiles = $this->getPhpFilesFromDirectory($pageDir);
            foreach ($phpFiles as $file) {
                $sectionName = basename($file, '.php');
                $configurations[$pageName][$sectionName] = include $file;
            }
        }

        // Allow plugins and child themes to modify all configurations
        $configurations = apply_filters('jankx/option/all_configurations', $configurations);

        // Allow plugins and child themes to add custom configurations
        $customConfigurations = apply_filters('jankx/option/custom_configurations', []);
        if (!empty($customConfigurations)) {
            $configurations = array_merge_recursive($configurations, $customConfigurations);
        }

        return $configurations;
    }

    /**
     * Get pages configuration
     *
     * @return array
     */
    public function getPagesConfig()
    {
        $pagesConfig = $this->loadConfiguration('pages.php');

        if (!$pagesConfig) {
            // Fallback to tests configs if no custom config found
            $pagesConfig = include __DIR__ . '/../tests/configs/pages.php';
        }

        return $pagesConfig;
    }

    /**
     * Get sections for a specific page
     *
     * @param string $pageId
     * @return array
     */
    public function getSectionsForPage($pageId)
    {
        $sections = [];
        $directories = $this->getOptionsDirectories();

        foreach ($directories as $directory) {
            $pagePath = $directory . '/' . $pageId;
            if (is_dir($pagePath)) {
                $phpFiles = glob($pagePath . '/*.php');
                foreach ($phpFiles as $file) {
                    $sectionName = basename($file, '.php');
                    $sections[$sectionName] = include $file;
                }
            }
        }

        // Allow plugins and child themes to modify sections for specific page
        $sections = apply_filters('jankx/option/sections_for_page', $sections, $pageId);

        // Allow plugins and child themes to add custom sections for specific page
        $customSections = apply_filters('jankx/option/custom_sections_for_page', [], $pageId);
        if (!empty($customSections)) {
            $sections = array_merge($sections, $customSections);
        }

        return $sections;
    }

    public function getPages()
    {
        // Tạo ConfigRepository khi cần thiết
        if (!$this->configRepository) {
            $this->configRepository = new ConfigRepository();
        }

        $pages = $this->configRepository->getPages();

        // Allow plugins and child themes to modify pages
        $pages = apply_filters('jankx/option/pages', $pages);

        // Allow plugins and child themes to add custom pages
        $customPages = apply_filters('jankx/option/custom_pages_data', []);
        if (!empty($customPages)) {
            $pages = array_merge($pages, $customPages);
        }

        return $pages;
    }

    public function getSections($pageTitle)
    {
        // Tạo ConfigRepository khi cần thiết
        if (!$this->configRepository) {
            $this->configRepository = new ConfigRepository();
        }

        $sections = $this->configRepository->getSections($pageTitle);

        // Allow plugins and child themes to modify sections for specific page
        $sections = apply_filters('jankx/option/sections', $sections, $pageTitle);

        // Allow plugins and child themes to add custom sections for specific page
        $customSections = apply_filters('jankx/option/custom_sections_data', [], $pageTitle);
        if (!empty($customSections)) {
            $sections = array_merge($sections, $customSections);
        }

        return $sections;
    }

    public function getFields($sectionTitle)
    {
        // Tạo ConfigRepository khi cần thiết
        if (!$this->configRepository) {
            $this->configRepository = new ConfigRepository();
        }

        $fields = $this->configRepository->getFields($sectionTitle);

        // Allow plugins and child themes to modify fields for specific section
        $fields = apply_filters('jankx/option/fields', $fields, $sectionTitle);

        // Allow plugins and child themes to add custom fields for specific section
        $customFields = apply_filters('jankx/option/custom_fields_data', [], $sectionTitle);
        if (!empty($customFields)) {
            $fields = array_merge($fields, $customFields);
        }

        return $fields;
    }

    /**
     * Register custom options from plugins or child themes
     *
     * @param string $pageId Page identifier
     * @param array $pageConfig Page configuration
     * @param array $sectionsConfig Sections configuration
     * @return void
     */
    public function registerCustomOptions($pageId, $pageConfig = [], $sectionsConfig = [])
    {
        // Allow plugins and child themes to register custom options
        do_action('jankx/option/register_custom_options', $pageId, $pageConfig, $sectionsConfig);

        // Store custom options for later use
        if (!empty($pageConfig)) {
            add_filter('jankx/option/custom_pages', function($customPages) use ($pageId, $pageConfig) {
                $customPages[$pageId] = $pageConfig;
                return $customPages;
            });
        }

        if (!empty($sectionsConfig)) {
            add_filter('jankx/option/custom_sections_for_page', function($customSections) use ($pageId, $sectionsConfig) {
                $customSections = array_merge($customSections, $sectionsConfig);
                return $customSections;
            });
        }
    }

    /**
     * Get all available filters for plugins and child themes
     *
     * @return array
     */
    public function getAvailableFilters()
    {
        return [
            'jankx/option/directory/path' => 'Modify options directory path',
            'jankx/option/directories' => 'Modify all options directories',
            'jankx/option/custom_directories' => 'Add custom options directories',
            'jankx/option/pages_config' => 'Modify pages configuration',
            'jankx/option/custom_pages' => 'Add custom pages',
            'jankx/option/all_configurations' => 'Modify all configurations',
            'jankx/option/custom_configurations' => 'Add custom configurations',
            'jankx/option/sections_for_page' => 'Modify sections for specific page',
            'jankx/option/custom_sections_for_page' => 'Add custom sections for specific page',
            'jankx/option/pages' => 'Modify pages data',
            'jankx/option/custom_pages_data' => 'Add custom pages data',
            'jankx/option/sections' => 'Modify sections data',
            'jankx/option/custom_sections_data' => 'Add custom sections data',
            'jankx/option/fields' => 'Modify fields data',
            'jankx/option/custom_fields_data' => 'Add custom fields data',
            'jankx/option/register_custom_options' => 'Register custom options hook',
        ];
    }
}
