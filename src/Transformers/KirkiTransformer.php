<?php

namespace Jankx\Adapter\Options\Transformers;

use Jankx\Adapter\Options\OptionsReader;
use Jankx\Adapter\Options\Interfaces\Page;
use Jankx\Adapter\Options\Interfaces\Section;
use Jankx\Adapter\Options\Interfaces\Field;

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

class KirkiTransformer
{
    /**
     * Transform complete config to Kirki format
     *
     * @param array $args
     * @return array
     */
    public static function transformCompleteConfig(array $args)
    {
        return [
            'option_name' => $args['opt_name'] ?? 'bookix_theme_options',
            'display_name' => $args['display_name'] ?? 'Bookix Theme Options',
            'display_version' => $args['display_version'] ?? '1.0.0',
            'menu_type' => 'customize', // Kirki uses Customizer
        ];
    }

    /**
     * Transform page to Kirki section
     *
     * @param Page $page
     * @return array
     */
    public static function transformPage(Page $page)
    {
        return [
            'id' => $page->getId(),
            'title' => $page->getTitle(),
            'subtitle' => $page->getSubtitle(),
            'priority' => $page->getPriority() ?? 30,
            'description' => $page->getDescription(),
        ];
    }

    /**
     * Transform section to Kirki section
     *
     * @param Section $section
     * @return array
     */
    public static function transformSection(Section $section)
    {
        $kirkiSection = [
            'id' => $section->getId(),
            'title' => $section->getTitle(),
            'subtitle' => $section->getSubtitle(),
            'priority' => $section->getPriority() ?? 30,
            'description' => $section->getDescription(),
            'fields' => [],
        ];

        // Transform fields
        foreach ($section->getFields() as $field) {
            $kirkiSection['fields'][] = self::transformField($field);
        }

        return $kirkiSection;
    }

    /**
     * Transform field to Kirki control
     *
     * @param Field $field
     * @return array
     */
    public static function transformField(Field $field)
    {
        $kirkiField = [
            'id' => $field->getId(),
            'title' => $field->getTitle(),
            'subtitle' => $field->getSubtitle(),
            'type' => self::mapFieldType($field->getType()),
            'default' => $field->getDefault(),
            'description' => $field->getDescription(),
        ];

        // Add field-specific properties
        switch ($field->getType()) {
            case 'select':
            case 'radio':
                if ($field->hasOptions()) {
                    $kirkiField['choices'] = $field->getOptions();
                }
                break;
            case 'color':
                // Color fields don't need additional properties
                break;
            case 'image':
                // Image fields don't need additional properties
                break;
            case 'number':
                if ($field->hasMin()) {
                    $kirkiField['min'] = $field->getMin();
                }
                if ($field->hasMax()) {
                    $kirkiField['max'] = $field->getMax();
                }
                if ($field->hasStep()) {
                    $kirkiField['step'] = $field->getStep();
                }
                break;
            case 'typography':
                $kirkiField['transport'] = 'auto';
                $kirkiField['output'] = [
                    [
                        'element' => 'body',
                        'property' => 'font-family',
                    ],
                ];
                break;
            case 'spacing':
                $kirkiField['transport'] = 'auto';
                $kirkiField['output'] = [
                    [
                        'element' => 'body',
                        'property' => 'padding',
                    ],
                ];
                break;
        }

        return $kirkiField;
    }

    /**
     * Map field type from OptionReader to Kirki
     *
     * @param string $type
     * @return string
     */
    protected static function mapFieldType($type)
    {
        $typeMap = [
            'text' => 'text',
            'textarea' => 'textarea',
            'checkbox' => 'checkbox',
            'radio' => 'radio',
            'select' => 'select',
            'color' => 'color',
            'image' => 'image',
            'number' => 'number',
            'url' => 'url',
            'email' => 'email',
            'password' => 'password',
            'tel' => 'tel',
            'date' => 'date',
            'time' => 'time',
            'datetime-local' => 'datetime-local',
            'range' => 'slider',
            'file' => 'upload',
            'hidden' => 'hidden',
            'typography' => 'typography',
            'spacing' => 'spacing',
            'background' => 'background',
            'multicolor' => 'multicolor',
            'palette' => 'palette',
            'repeater' => 'repeater',
            'sortable' => 'sortable',
            'code' => 'code',
            'editor' => 'editor',
            'switch' => 'switch',
            'toggle' => 'toggle',
            'dimensions' => 'dimensions',
            'slider' => 'slider',
            'dashicons' => 'dashicons',
            'dropdown-pages' => 'dropdown-pages',
            'dropdown-posts' => 'dropdown-posts',
            'dropdown-categories' => 'dropdown-categories',
            'dropdown-tags' => 'dropdown-tags',
            'dropdown-users' => 'dropdown-users',
            'dropdown-menus' => 'dropdown-menus',
            'dropdown-sidebars' => 'dropdown-sidebars',
        ];

        return $typeMap[$type] ?? 'text';
    }

    /**
     * Transform field value for Kirki
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    public static function transformFieldValue($value, $type)
    {
        switch ($type) {
            case 'color':
                return sanitize_hex_color($value);
            case 'url':
                return esc_url_raw($value);
            case 'email':
                return sanitize_email($value);
            case 'number':
                return intval($value);
            case 'checkbox':
                return (bool) $value;
            case 'typography':
                return self::sanitizeTypography($value);
            case 'spacing':
                return self::sanitizeSpacing($value);
            default:
                return sanitize_text_field($value);
        }
    }

    /**
     * Sanitize typography value
     *
     * @param mixed $value
     * @return array
     */
    protected static function sanitizeTypography($value)
    {
        if (is_array($value)) {
            return [
                'font-family' => sanitize_text_field($value['font-family'] ?? ''),
                'font-size' => sanitize_text_field($value['font-size'] ?? ''),
                'font-weight' => sanitize_text_field($value['font-weight'] ?? ''),
                'line-height' => sanitize_text_field($value['line-height'] ?? ''),
                'letter-spacing' => sanitize_text_field($value['letter-spacing'] ?? ''),
                'text-align' => sanitize_text_field($value['text-align'] ?? ''),
                'text-transform' => sanitize_text_field($value['text-transform'] ?? ''),
                'text-decoration' => sanitize_text_field($value['text-decoration'] ?? ''),
            ];
        }
        return [];
    }

    /**
     * Sanitize spacing value
     *
     * @param mixed $value
     * @return array
     */
    protected static function sanitizeSpacing($value)
    {
        if (is_array($value)) {
            return [
                'top' => sanitize_text_field($value['top'] ?? ''),
                'right' => sanitize_text_field($value['right'] ?? ''),
                'bottom' => sanitize_text_field($value['bottom'] ?? ''),
                'left' => sanitize_text_field($value['left'] ?? ''),
            ];
        }
        return [];
    }

    /**
     * Transform OptionsReader data to Kirki format
     *
     * @param OptionsReader $optionsReader
     * @return array
     */
    public static function transformOptionsReader($optionsReader, $adapter = null)
    {
        $kirkiData = [
            'pages' => [],
            'sections' => [],
        ];

        // Get pages from options reader
        $pages = $optionsReader->getPages();

        foreach ($pages as $page) {
            // Create page data
            $pageData = [
                'id' => $page->getId(),
                'title' => $page->getTitle(),
                'subtitle' => $page->getSubtitle(),
                'priority' => $page->getPriority() ?? 30,
                'description' => $page->getDescription(),
                'sections' => [],
            ];

            // Get sections for this page
            $sections = $optionsReader->getSections($page->getTitle());

            // Group sections by page
            foreach ($sections as $section) {
                $sectionData = [
                    'id' => $section->getId(),
                    'title' => $section->getTitle(),
                    'description' => $section->getDescription() ?? '',
                    'priority' => $section->getPriority() ?? 30,
                    'page_id' => $page->getId(), // Link section to page
                    'fields' => [],
                ];

                // Add icon if exists
                if ($section->getIcon()) {
                    $sectionData['icon'] = $section->getIcon();
                }

                // Get fields for this section
                $fields = $optionsReader->getFields($section->getTitle());

                // Transform each field and add to the section
                foreach ($fields as $field) {
                    $transformedField = self::transformField($field);
                    $sectionData['fields'][] = $transformedField;
                }

                // Add section to page
                $pageData['sections'][] = $sectionData;

                // Also add to main sections array for backward compatibility
                $kirkiData['sections'][] = $sectionData;
            }

            // Add page to pages array
            $kirkiData['pages'][] = $pageData;
        }


        return $kirkiData;
    }
}