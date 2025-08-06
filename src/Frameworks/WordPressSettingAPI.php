<?php

namespace Jankx\Adapter\Options\Frameworks;

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

use Jankx\Adapter\Options\Abstracts\Adapter;

class WordPressSettingAPI extends Adapter
{
    public function getOption($name, $defaultValue = null)
    {
        return get_option($name, $defaultValue);
    }

    public function setArgs($args)
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

    public function addSection($section)
    {
    }

    public function register_admin_menu($menu_title, $display_name)
    {
        // add_menu_page($menu_title, $display_name, 'manage_options', 'jankx', null, null, 65);
    }

    /**
     * Summary of createSections
     * @param \Jankx\Adapter\Options\OptionsReader $optionsReader
     *
     * @return void
     */
        public function createSections($optionsReader)
    {
        // Log transformer being used
        error_log('[JANKX DEBUG] WordPressSettingAPI: Using WordPressTransformer');

        // Transform OptionsReader data to WordPress Settings API format
        $wordpressData = \Jankx\Adapter\Options\Transformers\WordPressTransformer::transformOptionsReader($optionsReader);

        // Add pages to WordPress Settings API
        foreach ($wordpressData['pages'] as $page) {
            $this->addSection($page);
        }
    }

    /**
     * Transform WordPress dashicons to WordPress Settings API icons
     *
     * @param string $dashicon WordPress dashicon
     * @return string WordPress Settings API icon
     */
    public function transformIcon($dashicon)
    {
        // WordPress Settings API có thể sử dụng dashicons trực tiếp
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
        error_log('[JANKX DEBUG] WordPressSettingAPI: Mapping icon "' . $dashicon . '" to "' . $mappedIcon . '"');

        return $mappedIcon;
    }
}
