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
        error_log('[JANKX DEBUG] OptionsReader: setOptionsDirectoryPath called with: ' . $optionsDirectoryPath);
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

        error_log('[JANKX DEBUG] OptionsReader: optionsDirectoryPath = ' . ($this->optionsDirectoryPath ?: 'null'));

        // If optionsDirectoryPath is set, use it as relative path
        if ($this->optionsDirectoryPath && !$this->isAbsolutePath($this->optionsDirectoryPath)) {
            // Priority 1: Child theme (highest priority)
            if ($this->childThemeOverrideEnabled) {
                // Force check child theme path even if WordPress doesn't recognize it as child theme
                $childThemePath = get_stylesheet_directory() . '/' . $this->optionsDirectoryPath;
                error_log('[JANKX DEBUG] OptionsReader: Checking child theme path: ' . $childThemePath);
                if (is_dir($childThemePath)) {
                    $directories[] = $childThemePath;
                    error_log('[JANKX DEBUG] OptionsReader: Added child theme path: ' . $childThemePath);
                } else {
                    error_log('[JANKX DEBUG] OptionsReader: Child theme path not found: ' . $childThemePath);
                }
            } else {
                error_log('[JANKX DEBUG] OptionsReader: Child theme check failed - childThemeOverrideEnabled: ' . ($this->childThemeOverrideEnabled ? 'true' : 'false') . ', is_child_theme(): ' . (is_child_theme() ? 'true' : 'false'));
            }

            // Priority 2: Parent theme
            $parentThemePath = get_template_directory() . '/' . $this->optionsDirectoryPath;
            if (is_dir($parentThemePath)) {
                $directories[] = $parentThemePath;
                error_log('[JANKX DEBUG] OptionsReader: Added parent theme path: ' . $parentThemePath);
            } else {
                error_log('[JANKX DEBUG] OptionsReader: Parent theme path not found: ' . $parentThemePath);
            }
        } else {
            // Legacy behavior - use absolute paths
            // Priority 1: Child theme (highest priority)
            if ($this->childThemeOverrideEnabled && is_child_theme()) {
                $childThemePath = get_stylesheet_directory() . '/resources/options';
                if (is_dir($childThemePath)) {
                    $directories[] = $childThemePath;
                    error_log('[JANKX DEBUG] OptionsReader: Added child theme path: ' . $childThemePath);
                } else {
                    error_log('[JANKX DEBUG] OptionsReader: Child theme path not found: ' . $childThemePath);
                }
            }

            // Priority 2: Parent theme
            $parentThemePath = get_template_directory() . '/resources/options';
            if (is_dir($parentThemePath)) {
                $directories[] = $parentThemePath;
                error_log('[JANKX DEBUG] OptionsReader: Added parent theme path: ' . $parentThemePath);
            } else {
                error_log('[JANKX DEBUG] OptionsReader: Parent theme path not found: ' . $parentThemePath);
            }
        }

        // Priority 3: Fallback to tests configs
        $fallbackPath = __DIR__ . '/../tests/configs';
        if (is_dir($fallbackPath)) {
            $directories[] = $fallbackPath;
            error_log('[JANKX DEBUG] OptionsReader: Added fallback path: ' . $fallbackPath);
        } else {
            error_log('[JANKX DEBUG] OptionsReader: Fallback path not found: ' . $fallbackPath);
        }

        error_log('[JANKX DEBUG] OptionsReader: Final directories: ' . json_encode($directories));

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
            error_log('[JANKX DEBUG] OptionsReader: Configuration file not found: ' . $relativePath);
            return null;
        }

        error_log('[JANKX DEBUG] OptionsReader: Loading configuration from: ' . $filePath);
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
            error_log('[JANKX DEBUG] OptionsReader: No custom pages.php found, using fallback');
            // Fallback to tests configs if no custom config found
            $pagesConfig = include __DIR__ . '/../tests/configs/pages.php';
        } else {
            error_log('[JANKX DEBUG] OptionsReader: Using custom pages.php');
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

        return $sections;
    }

    public function getPages()
    {
        // Tạo ConfigRepository khi cần thiết
        if (!$this->configRepository) {
            $this->configRepository = new ConfigRepository();
        }
        return $this->configRepository->getPages();
    }

    public function getSections($pageTitle)
    {
        // Tạo ConfigRepository khi cần thiết
        if (!$this->configRepository) {
            $this->configRepository = new ConfigRepository();
        }
        return $this->configRepository->getSections($pageTitle);
    }

    public function getFields($sectionTitle)
    {
        // Tạo ConfigRepository khi cần thiết
        if (!$this->configRepository) {
            $this->configRepository = new ConfigRepository();
        }

        $fields = $this->configRepository->getFields($sectionTitle);
        error_log('[JANKX DEBUG] OptionsReader: getFields for section "' . $sectionTitle . '" returned ' . count($fields) . ' fields');

        return $fields;
    }
}
