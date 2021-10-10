<?php
namespace Jankx\Option\Specs;

class Field
{
    protected $id;
    protected $type;
    protected $title;
    protected $subtitle;
    protected $desc;
    protected $requiredField;
    protected $defaultValue;
    protected $hint;
    protected $priority = 10;
    protected $props = array();

    public function __construct($id, $title, $type, $subtitle = '', $desc = '', $requiredField = false, $defaultValue = '', $hint = array(), $priority = 10)
    {
        // Required fields
        $this->id = $id;
        $this->title = $title;
        $this->type = $type;

        // Optional fields
        $this->subtitle = $subtitle;
        $this->desc = $desc;
        $this->requiredField = $requiredField;
        $this->defaultValue = $defaultValue;
        $this->hint = $hint;
        $this->priority = $priority;
    }

    public function setProps($props)
    {
        if (is_array($this->props)) {
            $this->props = $props;
        }
    }

    public static function createFromArray($field)
    {
        if (!isset($field['id'], $field['title'])) {
            throw new \Exception(sprintf('The field required ID and title ares required. Please check your values'));
        }

        return new static(
            $field['id'],
            array_get($field, 'title', ''),
            array_get($field, 'type', 'text'),
            array_get($field, 'subtitle', ''),
            array_get($field, 'desc', ''),
            array_get($field, 'required', false),
            array_get($field, 'default', ''),
            array_get($field, 'hint', '')
        );
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }
}
