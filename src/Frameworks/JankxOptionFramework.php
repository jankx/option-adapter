<?php

namespace Jankx\Adapter\Options\Frameworks;

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
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
    protected $optName = 'jankx_options';
    protected $sync = true;

    public function setArgs($args)
    {
        if (isset($args['opt_name'])) {
            $this->optName = $args['opt_name'];
        }
        if (isset($args['sync_with_customizer'])) {
            $this->sync = (bool) $args['sync_with_customizer'];
        }
        error_log("Jankx Adapter setArgs: Sync is " . ($this->sync ? 'ON' : 'OFF'));
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
        // First priority: Theme Mod (for Customizer sync)
        $themeMod = get_theme_mod($name);
        if ($themeMod !== false && !is_null($themeMod)) {
            return $themeMod;
        }

        // Second priority: Main options group
        $options = get_option($this->optName ?: 'jankx_options', []);
        if (is_array($options) && array_key_exists($name, $options)) {
            return $options[$name];
        }
        return $defaultValue;
    }

    public function register_admin_menu($menu_title, $display_name)
    {


        // Tạo instance của OptionFramework
        $this->framework = new OptionFramework(
            $this->optName ?: 'jankx_options',
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
                'menu_icon' => 'dashicons-admin-customizer',
                'menu_slug' => 'jankx-theme-options', // Sử dụng slug thống nhất
                'auto_register_menu' => false, // Tắt auto register vì menu sẽ được tạo bởi JankxAdminPagesServiceProvider
                'sync_with_customizer' => $this->sync, // Bật đồng bộ với WordPress Customizer
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
        error_log("Jankx Adapter: createSections started");
        // Initialize framework if not already done

        // Initialize framework if not already done
        if (!$this->framework) {
            $this->framework = new OptionFramework(
                $this->optName ?: 'jankx_options',
                __('Theme Options', 'jankx'),
                __('Theme Options', 'jankx')
            );

            $this->framework
                ->setPageTitle(__('Theme Options', 'jankx'))
                ->setMenuText(__('Theme Options', 'jankx'))
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
                    'menu_icon' => 'dashicons-admin-customizer',
                    'menu_slug' => 'jankx-theme-options', // Sử dụng slug thống nhất
                    'auto_register_menu' => false, // Tắt auto register vì menu sẽ được tạo bởi JankxAdminPagesServiceProvider
                    'sync_with_customizer' => $this->sync, // Bật đồng bộ với WordPress Customizer
                ]);
        }

        // Retrieve pages from the repository
        $pages = $optionsReader->getPages();
        error_log("Jankx Adapter: found " . count($pages) . " pages");

        // Add pages, sections, and fields to the OptionFramework
        foreach ($pages as $page) {
            $dashboardPage = new Page($page->getTitle(), []);
            $dashboardPage->setId($page->getId());

            $sections = $optionsReader->getSections($page->getTitle());
            foreach ($sections as $section) {
                $dashboardSection = new Section($section->getTitle(), []);
                $dashboardSection->setId($section->getId());

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

    /**
     * Transform WordPress dashicons to Jankx Dashboard icons
     *
     * @param string $dashicon WordPress dashicon
     * @return string Jankx Dashboard icon
     */
    public function transformIcon($dashicon)
    {
        // Jankx Dashboard có thể sử dụng dashicons trực tiếp
        $iconMap = [
            'dashicons-admin-generic' => 'dashicons-admin-generic',
            'dashicons-editor-textcolor' => 'dashicons-editor-textcolor',
            'dashicons-art' => 'dashicons-art',
            'dashicons-layout' => 'dashicons-layout',
            'dashicons-align-wide' => 'dashicons-align-wide',
            'dashicons-align-full-width' => 'dashicons-align-full-width',
            'dashicons-admin-post' => 'dashicons-admin-post',
            'dashicons-admin-tools' => 'dashicons-admin-tools',
        ];

        $mappedIcon = isset($iconMap[$dashicon]) ? $iconMap[$dashicon] : 'dashicons-admin-generic';

        return $mappedIcon;
    }

    /**
     * Lấy OptionFramework instance
     *
     * @return OptionFramework|null
     */
    public function getFramework()
    {
        return $this->framework;
    }
}
