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
        $this->loadConfigurations();
    }

    protected function loadConfigurations()
    {
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
            $section = $this->makeSection($sectionConfig);
            $this->addSection($pageTitle, $section);

            // Add fields to section
            if (isset($sectionConfig['fields'])) {
                foreach ($sectionConfig['fields'] as $fieldConfig) {
                    $field = $this->makeField($fieldConfig);
                    $this->addField($section->getTitle(), $field);
                }
            }
        }
    }

    protected function makePage($config)
    {
        $icon = isset($config['args']['icon']) ? $config['args']['icon'] : '';
        return new Page($config['name'], [], $icon);
    }

    protected function makeSection($config)
    {
        return new Section($config['name'], []);
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
        $this->pages[$page->getTitle()] = $page;
    }

    public function getPages()
    {
        return $this->pages;
    }

    public function addSection($pageTitle, Section $section)
    {
        if (!isset($this->sections[$pageTitle])) {
            $this->sections[$pageTitle] = [];
        }
        $this->sections[$pageTitle][$section->getTitle()] = $section;
    }

    public function getSections($pageTitle)
    {
        return $this->sections[$pageTitle] ?? [];
    }

    /**
     * Summary of addField
     *
     * @param mixed $sectionTitle
     *
     * @param \Jankx\Dashboard\Interfaces\FieldInterface $field
     *
     * @return void
     */
    public function addField($sectionTitle, $field)
    {
        if (!isset($this->fields[$sectionTitle])) {
            $this->fields[$sectionTitle] = [];
        }

        if (!is_null($field)) {
            $this->fields[$sectionTitle][$field->getId()] = $field;
        }
    }

    public function getFields($sectionTitle)
    {
        return $this->fields[$sectionTitle] ?? [];
    }
}
