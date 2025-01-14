<?php

namespace Jankx\Adapter\Options\Repositories;

use Jankx\Dashboard\Elements\Page;
use Jankx\Dashboard\Elements\Section;
use Jankx\Dashboard\Factories\FieldFactory;

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
        return FieldFactory::create($config['id'], $config['name'], $config['type'], [
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
