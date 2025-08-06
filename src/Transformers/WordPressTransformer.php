<?php

namespace Jankx\Adapter\Options\Transformers;

use Jankx\Adapter\Options\OptionsReader;
use Jankx\Adapter\Options\Specs\Page;
use Jankx\Adapter\Options\Specs\Section;
use Jankx\Adapter\Options\Specs\Field;

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

class WordPressTransformer
{
    /**
     * Transform complete config to WordPress Settings API format
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
            'menu_type' => 'settings', // WordPress Settings API
        ];
    }

    /**
     * Transform page to WordPress settings page
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
     * Transform section to WordPress settings section
     *
     * @param Section $section
     * @return array
     */
    public static function transformSection(Section $section)
    {
        $wordpressSection = [
            'id' => $section->getId(),
            'title' => $section->getTitle(),
            'subtitle' => $section->getSubtitle(),
            'priority' => $section->getPriority() ?? 30,
            'description' => $section->getDescription(),
            'fields' => [],
        ];

        // Transform fields
        foreach ($section->getFields() as $field) {
            $wordpressSection['fields'][] = self::transformField($field);
        }

        return $wordpressSection;
    }

    /**
     * Transform field to WordPress settings field
     *
     * @param Field $field
     * @return array
     */
    public static function transformField(Field $field)
    {
        $wordpressField = [
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
                    $wordpressField['options'] = $field->getOptions();
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
                    $wordpressField['min'] = $field->getMin();
                }
                if ($field->hasMax()) {
                    $wordpressField['max'] = $field->getMax();
                }
                if ($field->hasStep()) {
                    $wordpressField['step'] = $field->getStep();
                }
                break;
        }

        return $wordpressField;
    }

    /**
     * Map field type from OptionReader to WordPress Settings API
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
            'range' => 'range',
            'file' => 'file',
            'hidden' => 'hidden',
        ];

        return $typeMap[$type] ?? 'text';
    }

    /**
     * Transform field value for WordPress Settings API
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
            default:
                return sanitize_text_field($value);
        }
    }

    /**
     * Get sanitize callback for field type
     *
     * @param string $type
     * @return string
     */
    public static function getSanitizeCallback($type)
    {
        $callbackMap = [
            'text' => 'sanitize_text_field',
            'textarea' => 'sanitize_textarea_field',
            'checkbox' => 'rest_sanitize_boolean',
            'color' => 'sanitize_hex_color',
            'url' => 'esc_url_raw',
            'email' => 'sanitize_email',
            'number' => 'intval',
            'password' => 'sanitize_text_field',
            'tel' => 'sanitize_text_field',
            'date' => 'sanitize_text_field',
            'time' => 'sanitize_text_field',
            'datetime-local' => 'sanitize_text_field',
            'range' => 'intval',
            'file' => 'sanitize_text_field',
            'hidden' => 'sanitize_text_field',
        ];

        return $callbackMap[$type] ?? 'sanitize_text_field';
    }

    /**
     * Transform OptionsReader data to WordPress Settings API format
     *
     * @param OptionsReader $optionsReader
     * @return array
     */
        public static function transformOptionsReader(OptionsReader $optionsReader)
    {
        error_log('[JANKX DEBUG] WordPressTransformer: Starting transformation');

        $wordpressData = [
            'pages' => [],
        ];

        // Get pages from options reader
        $pages = $optionsReader->getPages();

        foreach ($pages as $page) {
            // Transform page to WordPress settings page
            $wordpressPage = self::transformPage($page);

            // Get sections for this page
            $sections = $optionsReader->getSections($page->getTitle());

            foreach ($sections as $section) {
                // Transform section to WordPress format
                $transformedSection = self::transformSection($section);

                // Merge fields from all sections
                if (isset($transformedSection['fields'])) {
                    $wordpressPage['fields'] = array_merge(
                        $wordpressPage['fields'] ?? [],
                        $transformedSection['fields']
                    );
                }
            }

            $wordpressData['pages'][] = $wordpressPage;
        }

        error_log('[JANKX DEBUG] WordPressTransformer: Transformation completed');
        return $wordpressData;
    }
}