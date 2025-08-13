<?php

namespace Jankx\Adapter\Options\Repositories;

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

use Jankx\Dashboard\Elements\Page;
use Jankx\Dashboard\Elements\Section;
use Jankx\Dashboard\Factories\FieldFactory;
use Jankx\Adapter\Options\OptionsReader;

class ConfigRepository
{
    protected $pages = [];
    protected $sections = [];
    protected $fields = [];
    protected $optionsReader;

    public function __construct()
    {
        $this->optionsReader = OptionsReader::getInstance();
        // Don't load configurations immediately to avoid circular dependencies
        // They will be loaded when needed
    }

        protected function loadConfigurations()
    {
        // Initialize arrays if not already done
        if (!isset($this->pages)) {
            $this->pages = [];
        }
        if (!isset($this->sections)) {
            $this->sections = [];
        }
        if (!isset($this->fields)) {
            $this->fields = [];
        }

        // Load pages configuration with override support
        $pagesConfig = $this->optionsReader->getPagesConfig();

        foreach ($pagesConfig as $pageConfig) {
            $page = $this->makePage($pageConfig);
            $this->addPage($page);

            // Load sections for each page with override support
            $this->loadPageSections($pageConfig);
        }
    }

    protected function loadPageSections($pageConfig)
    {
        $pageId = $pageConfig['id'];
        $pageTitle = $pageConfig['name'];

        // Get all sections for this page from all possible directories
        $sectionsConfig = $this->optionsReader->getSectionsForPage($pageId);

        foreach ($sectionsConfig as $sectionName => $sectionConfig) {
            // Tạo section trước
            $section = $this->makeSection($sectionConfig);

            // Tạo fields và add vào section trước khi add section vào page
            if (isset($sectionConfig['fields'])) {
                foreach ($sectionConfig['fields'] as $fieldConfig) {
                    $field = $this->makeField($fieldConfig);
                    if ($field) {
                        $section->addField($field);
                    }
                }
            }

            // Add section vào page
            $this->addSection($pageTitle, $section);
        }
    }

    protected function makePage($config)
    {
        $page = new Page($config['name'], [], '');

        // Set page properties
        if (isset($config['id'])) {
            $page->setId($config['id']);
        }
        if (isset($config['args']['subtitle'])) {
            $page->setSubtitle($config['args']['subtitle']);
        }
        if (isset($config['args']['description'])) {
            $page->setDescription($config['args']['description']);
        }
        if (isset($config['args']['priority'])) {
            $page->setPriority($config['args']['priority']);
        }
        if (isset($config['args']['icon'])) {
            $page->setIcon($config['args']['icon']);
        }

        return $page;
    }

    protected function makeSection($config)
    {
        $section = new Section($config['name'], []);

        // Set section properties
        if (isset($config['id'])) {
            $section->setId($config['id']);
        }
        if (isset($config['subtitle'])) {
            $section->setSubtitle($config['subtitle']);
        }
        if (isset($config['description'])) {
            $section->setDescription($config['description']);
        }
        if (isset($config['priority'])) {
            $section->setPriority($config['priority']);
        }
        if (isset($config['icon'])) {
            $section->setIcon($config['icon']);
        }

        return $section;
    }

    protected function makeField($config)
    {
        $args = [];

        // Map Redux field properties to Dashboard field args
        if (isset($config['value'])) {
            $args['default'] = $config['value'];
        }
        if (isset($config['default_value'])) {
            $args['default'] = $config['default_value'];
        }
        if (isset($config['sub_title'])) {
            $args['subtitle'] = $config['sub_title'];
        }
        if (isset($config['description'])) {
            $args['description'] = $config['description'];
        }
        if (isset($config['options'])) {
            $args['options'] = $config['options'];
        }
        if (isset($config['min'])) {
            $args['min'] = $config['min'];
        }
        if (isset($config['max'])) {
            $args['max'] = $config['max'];
        }
        if (isset($config['step'])) {
            $args['step'] = $config['step'];
        }
        if (isset($config['on'])) {
            $args['on'] = $config['on'];
        }
        if (isset($config['off'])) {
            $args['off'] = $config['off'];
        }
        if (isset($config['transparent'])) {
            $args['transparent'] = $config['transparent'];
        }
        if (isset($config['alpha'])) {
            $args['alpha'] = $config['alpha'];
        }
        if (isset($config['google'])) {
            $args['google'] = $config['google'];
        }
        if (isset($config['font-family'])) {
            $args['font-family'] = $config['font-family'];
        }
        if (isset($config['font-size'])) {
            $args['font-size'] = $config['font-size'];
        }
        if (isset($config['font-weight'])) {
            $args['font-weight'] = $config['font-weight'];
        }
        if (isset($config['line-height'])) {
            $args['line-height'] = $config['line-height'];
        }
        if (isset($config['color'])) {
            $args['color'] = $config['color'];
        }
        if (isset($config['background-color'])) {
            $args['background-color'] = $config['background-color'];
        }
        if (isset($config['background-image'])) {
            $args['background-image'] = $config['background-image'];
        }
        if (isset($config['background-repeat'])) {
            $args['background-repeat'] = $config['background-repeat'];
        }
        if (isset($config['background-position'])) {
            $args['background-position'] = $config['background-position'];
        }
        if (isset($config['background-size'])) {
            $args['background-size'] = $config['background-size'];
        }
        if (isset($config['mode'])) {
            $args['mode'] = $config['mode'];
        }
        if (isset($config['units'])) {
            $args['units'] = $config['units'];
        }
        if (isset($config['top'])) {
            $args['top'] = $config['top'];
        }
        if (isset($config['right'])) {
            $args['right'] = $config['right'];
        }
        if (isset($config['bottom'])) {
            $args['bottom'] = $config['bottom'];
        }
        if (isset($config['left'])) {
            $args['left'] = $config['left'];
        }
        if (isset($config['display_value'])) {
            $args['display_value'] = $config['display_value'];
        }
        if (isset($config['resolution'])) {
            $args['resolution'] = $config['resolution'];
        }
        if (isset($config['layout'])) {
            $args['layout'] = $config['layout'];
        }

        return FieldFactory::create($config['id'], $config['name'], $config['type'], $args);
    }

    public function addPage(Page $page)
    {
        // Ensure arrays are initialized
        if (!isset($this->pages)) {
            $this->pages = [];
        }
        if (!isset($this->sections)) {
            $this->sections = [];
        }
        if (!isset($this->fields)) {
            $this->fields = [];
        }
        $this->pages[$page->getTitle()] = $page;
    }

    public function getPages()
    {
        if (empty($this->pages)) {
            $this->loadConfigurations();
        }
        return $this->pages;
    }

        public function addSection($pageTitle, Section $section)
    {
        // Ensure configurations are loaded
        if (empty($this->pages)) {
            $this->loadConfigurations();
        }

        // Find the page and add the section to it
        if (isset($this->pages[$pageTitle])) {
            $this->pages[$pageTitle]->addSection($section);
            // Also add to sections array for backward compatibility
            if (!isset($this->sections[$pageTitle])) {
                $this->sections[$pageTitle] = [];
            }
            $this->sections[$pageTitle][] = $section;
        }
    }

        public function getSections($pageTitle)
    {
        // Ensure configurations are loaded
        if (empty($this->pages)) {
            $this->loadConfigurations();
        }

        // Return from sections array for backward compatibility
        if (isset($this->sections[$pageTitle])) {
            return $this->sections[$pageTitle];
        }

        // Fallback to pages array
        if (isset($this->pages[$pageTitle])) {
            return $this->pages[$pageTitle]->getSections();
        }
        return [];
    }

    /**
     * Add field to section
     *
     * @param mixed $sectionTitle
     * @param \Jankx\Dashboard\Interfaces\FieldInterface $field
     * @return void
     */
        public function addField($sectionTitle, $field)
    {
        // Ensure configurations are loaded
        if (empty($this->pages)) {
            $this->loadConfigurations();
        }

        // Find the section and add the field to it
        foreach ($this->pages as $pageTitle => $page) {
            foreach ($page->getSections() as $section) {
                if ($section->getTitle() === $sectionTitle) {
                    $section->addField($field);
                    // Also add to fields array for backward compatibility
                    if (!isset($this->fields[$sectionTitle])) {
                        $this->fields[$sectionTitle] = [];
                    }
                    $this->fields[$sectionTitle][] = $field;
                    return;
                }
            }
        }
    }

    public function getFields($sectionTitle)
    {
        // Ensure configurations are loaded
        if (empty($this->pages)) {
            $this->loadConfigurations();
        }

        // Find the section and return its fields
        foreach ($this->pages as $pageTitle => $page) {
            foreach ($page->getSections() as $section) {
                if ($section->getTitle() === $sectionTitle) {
                    return $section->getFields();
                }
            }
        }
        return [];
    }
}
