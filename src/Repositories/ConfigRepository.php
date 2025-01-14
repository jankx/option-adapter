<?php

namespace Jankx\Adapter\Options\Repositories;

use Jankx\Dashboard\Elements\Field;
use Jankx\Dashboard\Elements\Page;
use Jankx\Dashboard\Elements\Section;

class ConfigRepository
{
    protected $pages = [];
    protected $sections = [];
    protected $fields = [];

    public function __construct()
    {
        $this->loadConfigurations();
    }

    protected function loadConfigurations()
    {
        // Load pages configuration
        $pagesConfig = include __DIR__ . '/../../tests/configs/pages.php';

        foreach ($pagesConfig as $pageConfig) {
            $page = $this->makePage($pageConfig);
            $this->addPage($page);

            // Load sections for each page
            $sectionsPath = __DIR__ . '/../../tests/configs/' . $pageConfig['id'];
            foreach (glob($sectionsPath . '/*.php') as $sectionFile) {
                $sectionConfig = include $sectionFile;
                $section = $this->makeSection($sectionConfig);
                $this->addSection($page->getTitle(), $section);

                // Add fields to section
                foreach ($sectionConfig['fields'] as $fieldConfig) {
                    $field = $this->makeField($fieldConfig);
                    $this->addField($section->getTitle(), $field);
                }
            }
        }
    }

    protected function makePage($config)
    {
        return new Page($config['name'], []);
    }

    protected function makeSection($config)
    {
        return new Section($config['name'], []);
    }

    protected function makeField($config)
    {
        return new Field($config['id'], $config['name'], $config['type'], [
            'value' => $config['value'],
            'default_value' => $config['default_value'],
            'sub_title' => $config['sub_title'],
            'description' => $config['description'],
        ]);
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

    public function addField($sectionTitle, Field $field)
    {
        if (!isset($this->fields[$sectionTitle])) {
            $this->fields[$sectionTitle] = [];
        }
        $this->fields[$sectionTitle][$field->getId()] = $field;
    }

    public function getFields($sectionTitle)
    {
        return $this->fields[$sectionTitle] ?? [];
    }
}
