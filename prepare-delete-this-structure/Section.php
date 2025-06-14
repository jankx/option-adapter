<?php

namespace Jankx\Option\Specs;

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

use Jankx\Option\Specs\Field;

class Section
{
    protected $id;
    protected $title;
    protected $subtitle;
    protected $description;
    protected $requiredSection;
    protected $hint;
    protected $fields = array();
    private $children = array();
    private $priority = 10;

    public function __construct($id, $title, $subtitle = '', $description = '', $icon = '', $requiredSection = true, $hint = array(), $priority = 10)
    {
        // Required fields
        $this->id = $id;
        $this->title = $title;

        // Optional fields
        $this->subtitle = $subtitle;
        $this->description = $description;
        $this->icon = $icon;
        $this->requiredSection = $requiredSection;
        $this->hint = $hint;
        $this->priority = $priority;
    }

    public static function createFromArray($sectionArgs)
    {
        if (!isset($sectionArgs['id'], $sectionArgs['title'])) {
            throw new \Exception(sprintf('The section ID and title are required fields. Please check your values'));
        }

        return new static(
            $sectionArgs['id'],
            $sectionArgs['title'],
            array_get($sectionArgs, 'subtitle'),
            array_get($sectionArgs, 'description', ''),
            array_get($sectionArgs, 'icon', ''),
            array_get($sectionArgs, 'requiredSection'),
            array_get($sectionArgs, 'hint', array()),
            array_get($sectionArgs, 'priority', 10)
        );
    }

    public function isValid()
    {
        return !empty($this->id);
    }

    public function createFieldsFromArray($fields)
    {
        foreach (array_values($fields) as $fieldArgs) {
            $field = Field::createFromArray($fieldArgs);

            // Remove field data
            unset($fieldArgs['id'], $fieldArgs['type'], $fieldArgs['title'], $fieldArgs['subtitle'], $fieldArgs['desc'], $fieldArgs['required'], $fieldArgs['default'], $fieldArgs['hint']);
            $field->setProps($fieldArgs);

            array_push($this->fields, $field);
        }
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }
}
