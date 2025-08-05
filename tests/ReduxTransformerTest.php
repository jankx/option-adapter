<?php

namespace Jankx\Adapter\Options\Tests;

use PHPUnit\Framework\TestCase;
use Jankx\Adapter\Options\Transformers\ReduxTransformer;

class ReduxTransformerTest extends TestCase
{
    public function testTransformPage()
    {
        $page = [
            'id' => 'general',
            'name' => 'General Settings',
            'args' => [
                'description' => 'General theme settings',
                'icon' => 'dashicons-admin-generic',
                'priority' => 10,
            ],
        ];

        $result = ReduxTransformer::transformPage($page);

        $this->assertEquals('general', $result['id']);
        $this->assertEquals('General Settings', $result['title']);
        $this->assertEquals('General theme settings', $result['desc']);
        $this->assertEquals('dashicons-admin-generic', $result['icon']);
        $this->assertEquals(10, $result['priority']);
        $this->assertIsArray($result['fields']);
    }

    public function testTransformField()
    {
        $field = [
            'id' => 'site_title',
            'name' => 'Site Title',
            'type' => 'text',
            'sub_title' => 'Enter your site title',
            'description' => 'This will be displayed in browser tab',
            'default_value' => 'My Site',
            'wordpress_native' => true,
            'option_name' => 'blogname',
        ];

        $result = ReduxTransformer::transformField($field);

        $this->assertEquals('site_title', $result['id']);
        $this->assertEquals('text', $result['type']);
        $this->assertEquals('Site Title', $result['title']);
        $this->assertEquals('Enter your site title', $result['subtitle']);
        $this->assertEquals('This will be displayed in browser tab', $result['desc']);
        $this->assertEquals('My Site', $result['default']);
        $this->assertTrue($result['wordpress_native']);
        $this->assertEquals('blogname', $result['option_name']);
    }

    public function testMapFieldType()
    {
        $this->assertEquals('text', ReduxTransformer::mapFieldType('text'));
        $this->assertEquals('textarea', ReduxTransformer::mapFieldType('textarea'));
        $this->assertEquals('media', ReduxTransformer::mapFieldType('image'));
        $this->assertEquals('color', ReduxTransformer::mapFieldType('color'));
        $this->assertEquals('typography', ReduxTransformer::mapFieldType('typography'));
        $this->assertEquals('slider', ReduxTransformer::mapFieldType('slider'));
        $this->assertEquals('switch', ReduxTransformer::mapFieldType('switch'));
        $this->assertEquals('unknown', ReduxTransformer::mapFieldType('unknown'));
    }

    public function testTransformFieldWithOptions()
    {
        $field = [
            'id' => 'primary_color',
            'name' => 'Primary Color',
            'type' => 'color',
            'default_value' => '#007cba',
            'sub_title' => 'Choose primary color',
            'description' => 'This will be used for buttons and links',
        ];

        $result = ReduxTransformer::transformField($field);

        $this->assertEquals('primary_color', $result['id']);
        $this->assertEquals('color', $result['type']);
        $this->assertEquals('Primary Color', $result['title']);
        $this->assertEquals('#007cba', $result['default']);
    }

    public function testTransformFieldWithSlider()
    {
        $field = [
            'id' => 'container_width',
            'name' => 'Container Width',
            'type' => 'slider',
            'default_value' => 1200,
            'min' => 800,
            'max' => 1600,
            'step' => 50,
        ];

        $result = ReduxTransformer::transformField($field);

        $this->assertEquals('container_width', $result['id']);
        $this->assertEquals('slider', $result['type']);
        $this->assertEquals(1200, $result['default']);
        $this->assertEquals(800, $result['min']);
        $this->assertEquals(1600, $result['max']);
        $this->assertEquals(50, $result['step']);
    }

    public function testTransformFieldWithTypography()
    {
        $field = [
            'id' => 'body_typography',
            'name' => 'Body Typography',
            'type' => 'typography',
            'options' => [
                'google' => true,
                'font-family' => true,
                'font-size' => true,
                'font-weight' => true,
                'line-height' => true,
                'color' => true,
            ],
        ];

        $result = ReduxTransformer::transformField($field);

        $this->assertEquals('body_typography', $result['id']);
        $this->assertEquals('typography', $result['type']);
        $this->assertTrue($result['google']);
        $this->assertTrue($result['font-family']);
        $this->assertTrue($result['font-size']);
        $this->assertTrue($result['font-weight']);
        $this->assertTrue($result['line-height']);
        $this->assertTrue($result['color']);
    }

    public function testTransformFieldWithRepeater()
    {
        $field = [
            'id' => 'social_links',
            'name' => 'Social Links',
            'type' => 'repeater',
            'fields' => [
                [
                    'id' => 'social_icon',
                    'name' => 'Icon',
                    'type' => 'icon',
                    'default_value' => 'fab fa-facebook',
                ],
                [
                    'id' => 'social_url',
                    'name' => 'URL',
                    'type' => 'text',
                    'default_value' => '',
                ],
            ],
        ];

        $result = ReduxTransformer::transformField($field);

        $this->assertEquals('social_links', $result['id']);
        $this->assertEquals('repeater', $result['type']);
        $this->assertIsArray($result['fields']);
        $this->assertCount(2, $result['fields']);
        $this->assertEquals('social_icon', $result['fields'][0]['id']);
        $this->assertEquals('social_url', $result['fields'][1]['id']);
    }

    public function testTransformCompleteConfig()
    {
        $config = [
            'display_name' => 'Bookix Options',
            'menu_title' => 'Theme Options',
            'menu_position' => 60,
            'dev_mode' => true,
            'customizer' => true,
            'import_export' => true,
            'pages' => [
                [
                    'id' => 'general',
                    'name' => 'General Settings',
                    'args' => [
                        'description' => 'General theme settings',
                    ],
                    'sections' => [
                        [
                            'id' => 'site_info',
                            'name' => 'Site Information',
                            'fields' => [
                                [
                                    'id' => 'site_title',
                                    'name' => 'Site Title',
                                    'type' => 'text',
                                    'default_value' => 'My Site',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $result = ReduxTransformer::transformCompleteConfig($config);

        $this->assertIsString($result['opt_name']);
        $this->assertEquals('Bookix Options', $result['display_name']);
        $this->assertEquals('Theme Options', $result['menu_title']);
        $this->assertEquals(60, $result['page_priority']);
        $this->assertTrue($result['dev_mode']);
        $this->assertTrue($result['customizer']);
        $this->assertTrue($result['show_import_export']);
        $this->assertIsArray($result['sections']);
        $this->assertCount(1, $result['sections']);
        $this->assertEquals('general', $result['sections'][0]['id']);
        $this->assertEquals('General Settings', $result['sections'][0]['title']);
        $this->assertIsArray($result['sections'][0]['fields']);
        $this->assertCount(1, $result['sections'][0]['fields']);
        $this->assertEquals('site_title', $result['sections'][0]['fields'][0]['id']);
    }

    public function testGenerateOptionName()
    {
        $optionName = ReduxTransformer::generateOptionName();
        $this->assertIsString($optionName);
        $this->assertStringEndsWith('_theme_options', $optionName);
    }

    public function testTransformFieldValue()
    {
        // Test typography
        $typographyValue = ReduxTransformer::transformFieldValue('', 'typography');
        $this->assertIsArray($typographyValue);
        $this->assertArrayHasKey('font-family', $typographyValue);
        $this->assertArrayHasKey('font-size', $typographyValue);
        $this->assertArrayHasKey('font-weight', $typographyValue);
        $this->assertArrayHasKey('line-height', $typographyValue);
        $this->assertArrayHasKey('color', $typographyValue);

        // Test color
        $colorValue = ReduxTransformer::transformFieldValue('', 'color');
        $this->assertEquals('#007cba', $colorValue);

        // Test media
        $mediaValue = ReduxTransformer::transformFieldValue('', 'media');
        $this->assertEquals('', $mediaValue);

        // Test gallery
        $galleryValue = ReduxTransformer::transformFieldValue('', 'gallery');
        $this->assertIsArray($galleryValue);
        $this->assertEmpty($galleryValue);

        // Test repeater
        $repeaterValue = ReduxTransformer::transformFieldValue('', 'repeater');
        $this->assertIsArray($repeaterValue);
        $this->assertEmpty($repeaterValue);

        // Test sorter
        $sorterValue = ReduxTransformer::transformFieldValue('', 'sorter');
        $this->assertIsArray($sorterValue);
        $this->assertEmpty($sorterValue);

        // Test default
        $defaultValue = ReduxTransformer::transformFieldValue('test_value', 'text');
        $this->assertEquals('test_value', $defaultValue);
    }

    public function testTransformBackToStandard()
    {
        $reduxField = [
            'id' => 'site_title',
            'type' => 'text',
            'title' => 'Site Title',
            'subtitle' => 'Enter your site title',
            'desc' => 'This will be displayed in browser tab',
            'default' => 'My Site',
            'wordpress_native' => true,
            'option_name' => 'blogname',
        ];

        $result = ReduxTransformer::transformBackToStandard($reduxField);

        $this->assertEquals('site_title', $result['id']);
        $this->assertEquals('text', $result['type']);
        $this->assertEquals('Site Title', $result['name']);
        $this->assertEquals('Enter your site title', $result['sub_title']);
        $this->assertEquals('This will be displayed in browser tab', $result['description']);
        $this->assertEquals('My Site', $result['default_value']);
        $this->assertTrue($result['wordpress_native']);
        $this->assertEquals('blogname', $result['option_name']);
    }

    public function testMapFieldTypeBack()
    {
        $this->assertEquals('text', ReduxTransformer::mapFieldTypeBack('text'));
        $this->assertEquals('textarea', ReduxTransformer::mapFieldTypeBack('textarea'));
        $this->assertEquals('image', ReduxTransformer::mapFieldTypeBack('media'));
        $this->assertEquals('color', ReduxTransformer::mapFieldTypeBack('color'));
        $this->assertEquals('typography', ReduxTransformer::mapFieldTypeBack('typography'));
        $this->assertEquals('slider', ReduxTransformer::mapFieldTypeBack('slider'));
        $this->assertEquals('switch', ReduxTransformer::mapFieldTypeBack('switch'));
        $this->assertEquals('unknown', ReduxTransformer::mapFieldTypeBack('unknown'));
    }

    public function testAddFieldOptionsBack()
    {
        $field = ['id' => 'test'];
        $reduxField = [
            'id' => 'test',
            'type' => 'slider',
            'min' => 0,
            'max' => 100,
            'step' => 5,
        ];

        $result = ReduxTransformer::addFieldOptionsBack($field, $reduxField);

        $this->assertEquals(0, $result['min']);
        $this->assertEquals(100, $result['max']);
        $this->assertEquals(5, $result['step']);
    }
}