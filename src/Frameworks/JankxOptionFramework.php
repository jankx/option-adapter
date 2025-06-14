<?php

namespace Jankx\Adapter\Options\Frameworks;

if (!defined('ABSPATH')) {
    exit('Cheatin huh?');
}

use Jankx\Adapter\Options\Abstracts\Adapter;
use Jankx\Adapter\Options\OptionsReader;
use Jankx\Dashboard\Elements\Page;
use Jankx\Dashboard\Elements\Section;
use Jankx\Dashboard\Factories\FieldFactory;
use Jankx\Dashboard\Interfaces\FieldInterface;
use Jankx\Dashboard\OptionFramework;

class JankxOptionFramework extends Adapter
{
    protected $framework;

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
        return $defaultValue;
    }

    public function register_admin_menu($menu_title, $display_name)
    {
        $optionsReader = OptionsReader::getInstance();

        // Tạo instance của OptionFramework
        $this->framework = new OptionFramework(
            'jankx_options',
            $display_name,
            $menu_title,
        );

        $this->framework
            ->setPageTitle($display_name)
            ->setMenuText($menu_title)
            ->setConfig([
                'logo' => 'https://example.com/logo.png',
                'version' => '2.0.0',
                'description' => 'Configure your theme settings here',
                'social_links' => [
                'facebook' => 'https://facebook.com/mytheme',
                'twitter' => 'https://twitter.com/mytheme',
                'github' => 'https://github.com/mytheme'
                ],
                'support_url' => 'https://example.com/support',
                'documentation_url' => 'https://example.com/docs',
                'menu_position' => 59,
                'menu_icon' => 'dashicons-admin-customizer'
        ]);
    }

    /**
     * Summary of createSections
     * @param \Jankx\Adapter\Options\OptionsReader $optionsReader
     *
     * @return void
     */
    public function createSections($optionsReader)
    {
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
                    $dashboardField = FieldFactory::create(
                        $field->getId(),
                        $field->getTitle(),
                        $field->getType(),
                        $field->getArgs()
                    );
                    if ($dashboardField instanceof FieldInterface) {
                        $dashboardSection->addField($dashboardField);
                    }
                }

                $dashboardPage->addSection($dashboardSection);
            }

            $this->framework->addPage($dashboardPage);
        }
    }
}
