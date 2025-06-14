<?php
if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

return [
    'id' => 'section_item_1',
    'name' => 'Section 1',
    'description' => 'This is the first section of page 1',
    'fields' => [
        [
            'id' => 'field_1',
            'name' => 'Field 1',
            'type' => 'text',
            'value' => 'Value 1',
            'default_value' => 'Default Value 1',
            'sub_title' => 'Subtitle 1',
            'description' => 'Description for field 1',
        ],
        [
            'id' => 'field_2',
            'name' => 'Field 2',
            'type' => 'image',
            'value' => 'Value 2',
            'options' => [],
            'default_value' => 'Default Value 2',
            'sub_title' => 'Subtitle 2',
            'description' => 'Description for field 2',
        ],
    ],
];