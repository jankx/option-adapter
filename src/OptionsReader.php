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

    private function __construct()
    {
        $this->configRepository = new ConfigRepository();
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
            $this->optionsDirectoryPath = sprintf('%s/includes/options', constant('JANKX_ABSPATH'));
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

        // Priority 1: Child theme (highest priority)
        if ($this->childThemeOverrideEnabled && is_child_theme()) {
            $childThemePath = get_stylesheet_directory() . '/includes/options';
            if (is_dir($childThemePath)) {
                $directories[] = $childThemePath;
            }
        }

        // Priority 2: Parent theme
        $parentThemePath = get_template_directory() . '/includes/options';
        if (is_dir($parentThemePath)) {
            $directories[] = $parentThemePath;
        }

        // Priority 3: Jankx framework default
        $frameworkPath = sprintf('%s/includes/options', constant('JANKX_ABSPATH'));
        if (is_dir($frameworkPath)) {
            $directories[] = $frameworkPath;
        }

        // Priority 4: Fallback to tests configs
        $fallbackPath = __DIR__ . '/../tests/configs';
        if (is_dir($fallbackPath)) {
            $directories[] = $fallbackPath;
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

        return $sections;
    }

    public function getPages()
    {
        return $this->configRepository->getPages();
    }

    public function getSections($pageTitle)
    {
        return $this->configRepository->getSections($pageTitle);
    }

    public function getFields($sectionTitle)
    {
        return $this->configRepository->getFields($sectionTitle);
    }
}
