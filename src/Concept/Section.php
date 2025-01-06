<?php

namespace Jankx\Adapter\Options\Concept;

use Jankx\Adapter\Options\Interfaces\Section as SectionInterface;

final class Section implements SectionInterface
{
    protected $title;
    protected $fields = [];

    public function __construct($title, $fields = [])
    {
        $this->title = $title;
        $this->fields = $fields;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function addField($field)
    {
        $this->fields[] = $field;
    }
}
