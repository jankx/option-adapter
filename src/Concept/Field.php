<?php

namespace Jankx\Adapter\Options\Concept;

use Jankx\Adapter\Options\Interfaces\Field as FieldInterface;

final class Field implements FieldInterface
{
    protected $id;
    protected $title;
    protected $type;
    protected $args = [];

    public function __construct($id, $title, $type, $args = [])
    {
        $this->id = $id;
        $this->title = $title;
        $this->type = $type;
        $this->args = $args;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getArgs()
    {
        return $this->args;
    }
}
