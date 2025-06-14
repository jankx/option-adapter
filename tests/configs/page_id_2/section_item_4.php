<?php
if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

return [
    'id' => 'section_item_4',
    'name' => 'Section 4',
    'description' => 'This is the second section of page 2',
    'fields' => [
        [
            'id' => 'field_3',
            'name' => 'Field 3',
            'type' => 'textarea',
            'value' => 'Value 3',
            'default_value' => 'Default Value 3',
            'sub_title' => 'Subtitle 3',
            'description' => 'Description for field 3',
        ],
        [
            'id' => 'field_4',
            'name' => 'Field 4',
            'type' => 'textarea',
            'value' => 'Value 4',
            'default_value' => 'Default Value 4',
            'sub_title' => 'Subtitle 4',
            'description' => 'Description for field 4',
        ],
    ],
];
