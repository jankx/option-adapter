Jankx Option
=


# Specifications

## Files and Directories strutures

```
include/options
  - option-section
    - fields
      - single-field.php
      - multi-fields.php

    - sub-section
      - fields
        - single-field.php
        - multi-fields.php
      - sub-options-args.php

    - option-args.php

  - general-fields.php
  
```


## Fields structures

### Section and sub section args

```php
return array(
  'id' => 'id-of-sections',
  'title' => esc_html__('Section title'),
  'subtitle' => esc_html__('Section sub-title'),
  'desc' => esc_html__('The section decription'),
  'icon' => 'fa-user',
  'priority' => 10, // Alias is `sort`
);
```

### Single field

```php
return array(
  'id' => 'field-id',
  'type' => 'data-type',
  'title' => esc_html__('Field title'),
  'subtitle' => esc_html__('Field subtitle')
  'desc' => esc_html__('Field description'),
  'hint' => array(
    'content' => 'This is a <b>hint</b> tool-tip for the text field.<br/><br/>Add any HTML based text you like here.',
  ),
  'priority' => 10, // Alias is `sort`
);
```

### Multi fields

```php
return array(
  array(
    array(
      'id' => 'field-id-1',
      'type' => 'data-type',
      'title' => esc_html__('Field title 1'),
      'subtitle' => esc_html__('Field subtitle 1')
      'desc' => esc_html__('Field description 1'),
      'hint' => array(
        'content' => 'This is a <b>hint</b> tool-tip for the text field.<br/><br/>Add any HTML based text you like here.',
      )
    ),
    array(
      'id' => 'field-id-2',
      'type' => 'text',
      'title' => esc_html__('Field title 2'),
      'subtitle' => esc_html__('Field subtitle 2')
      'desc' => esc_html__('Field description'),
      'priority' => 10, // Alias is `sort`
    )
  )
);
```
