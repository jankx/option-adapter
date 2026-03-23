<?php

namespace Jankx\Adapter\Options\Transformers;

use Jankx\Adapter\Options\Specs\Page;
use Jankx\Adapter\Options\Specs\Section;
use Jankx\Adapter\Options\Specs\Field;

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

class CustomizerTransformer
{
    /**
     * Transform complete config to WordPress Customizer format
     *
     * @param array $args
     * @return array
     */
    public static function transformCompleteConfig(array $args)
    {
        return [
            'opt_name' => $args['opt_name'] ?? 'jankx_options',
            'type' => 'theme_mod', // Use theme_mod by default for customizer
            'capability' => $args['capability'] ?? 'edit_theme_options',
        ];
    }

    /**
     * Transform page to Customizer Panel
     *
     * @param Page $page
     * @return array
     */
    public static function transformPanel(Page $page)
    {
        return [
            'id' => $page->getId(),
            'title' => $page->getTitle(),
            'description' => $page->getDescription(),
            'priority' => $page->getPriority() ?? 160,
        ];
    }

    /**
     * Transform section to Customizer Section
     *
     * @param Section $section
     * @param string|null $panel_id
     * @return array
     */
    public static function transformSection(Section $section, $panel_id = null)
    {
        return [
            'id' => $section->getId(),
            'title' => $section->getTitle(),
            'description' => $section->getDescription(),
            'priority' => $section->getPriority() ?? 30,
            'panel' => $panel_id,
        ];
    }

    /**
     * Transform field to Customizer Setting and Control
     *
     * @param Field $field
     * @param string $section_id
     * @return array
     */
    public static function transformField(Field $field, $section_id)
    {
        $type = $field->getType();
        $control_type = self::mapControlType($type);

        return [
            'id' => $field->getId(),
            'setting' => [
                'default' => $field->getDefault(),
                'type' => 'theme_mod',
                'capability' => 'edit_theme_options',
                'transport' => 'refresh', // or 'postMessage'
                'sanitize_callback' => self::getSanitizeCallback($type),
            ],
            'control' => [
                'label' => $field->getTitle(),
                'description' => $field->getDescription(),
                'section' => $section_id,
                'type' => $control_type,
                'choices' => $field->getOptions(),
            ],
        ];
    }

    /**
     * Map field type to Customizer control type
     *
     * @param string $type
     * @return string
     */
    protected static function mapControlType($type)
    {
        $map = [
            'text' => 'text',
            'textarea' => 'textarea',
            'checkbox' => 'checkbox',
            'radio' => 'radio',
            'select' => 'select',
            'color' => 'color',
            'image' => 'image',
            'number' => 'number',
        ];

        return $map[$type] ?? 'text';
    }

    /**
     * Get sanitize callback for Customizer
     *
     * @param string $type
     * @return string
     */
    protected static function getSanitizeCallback($type)
    {
        return WordPressTransformer::getSanitizeCallback($type);
    }

    /**
     * Transform field value for Customizer
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    public static function transformValue($value, $type)
    {
        return WordPressTransformer::transformFieldValue($value, $type);
    }
}
