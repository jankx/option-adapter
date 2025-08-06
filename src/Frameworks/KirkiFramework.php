<?php

/**
 * Kirki Customizer Framework Adapter
 *
 * Kirki allows theme developers to build themes quicker & more easily.
 *
 * With over 30 custom controls ranging from simple sliders to complex typography controls
 * with Google-Fonts integration and features like automatic CSS & postMessage script generation,
 * Kirki makes theme development a breeze.
 *
 * @package Jankx
 * @subpackage Option
 * @author Puleeno Nguyen <puleeno@gmail.com>
 * @link https://kirki.org/
 * @since 1.0.0
 */

namespace Jankx\Adapter\Options\Frameworks;

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

use Jankx\Adapter\Options\Abstracts\Adapter;

class KirkiFramework extends Adapter
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
        error_log('[JANKX DEBUG] KirkiFramework: Using KirkiTransformer');

        // Transform OptionsReader data to Kirki format
        $kirkiData = \Jankx\Adapter\Options\Transformers\KirkiTransformer::transformOptionsReader($optionsReader);

        // Add sections to Kirki
        foreach ($kirkiData['sections'] as $section) {
            $this->addSection($section);
        }
    }

    /**
     * Transform WordPress dashicons to Kirki icons
     *
     * @param string $dashicon WordPress dashicon
     * @return string Kirki icon
     */
    public function transformIcon($dashicon)
    {
        // Kirki có thể sử dụng dashicons trực tiếp
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
        error_log('[JANKX DEBUG] KirkiFramework: Mapping icon "' . $dashicon . '" to "' . $mappedIcon . '"');

        return $mappedIcon;
    }
}
