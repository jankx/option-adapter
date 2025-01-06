<?php

namespace Jankx\Adapter\Options\Frameworks;

use Jankx\Adapter\Options\Abstracts\Adapter;
use Jankx\Adapter\Options\OptionsReader;
use Jankx\Adapter\Options\Specs\Options;
use Jankx\Dashboard\Elements\Field;
use Jankx\Dashboard\Elements\Section;
use Jankx\Dashboard\Elements\Page;
use Jankx\Dashboard\OptionFramework;

class JankxOptionFramework extends Adapter
{
    public function setArgs($args)
    {
    }

    public function addSection($section)
    {
    }

    public static function mapSectionFields()
    {
        return [];
    }

    public static function mapFieldProperties()
    {
        return [];
    }

    public function getOption($name, $defaultValue = null)
    {
    }

    public function register_admin_menu($menu_title, $display_name)
    {
        $optionsReader = OptionsReader::getInstance();

        // Tạo instance của OptionFramework
        $optionFramework = new OptionFramework(
            'jankx_options',
            $display_name,
            $menu_title,
        );

        // Retrieve pages from the repository
        $pages = $optionsReader->getPages();

        // Add pages, sections, and fields to the OptionFramework
        foreach ($pages as $page) {
            $dashboardPage = new Page($page->getTitle(), []);

            $sections = $optionsReader->getSections($page->getTitle());
            foreach ($sections as $section) {
                $dashboardSection = new Section($section->getTitle(), []);

                $fields = $optionsReader->getFields($section->getTitle());
                foreach ($fields as $field) {
                    $dashboardField = new Field(
                        $field->getId(),
                        $field->getTitle(),
                        $field->getType(),
                        $field->getArgs()
                    );
                    $dashboardSection->addField($dashboardField);
                }

                $dashboardPage->addSection($dashboardSection);
            }

            $optionFramework->addPage($dashboardPage);
        }
    }

    public function createSections($options)
    {
        if (is_a($options, Options::class)) {
            foreach ($options->getSections() as $section) {
                $this->addSection($section);
            }
        }
    }
}
