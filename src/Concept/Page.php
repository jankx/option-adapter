<?php

namespace Jankx\Adapter\Options\Concept;

use Jankx\Adapter\Options\Interfaces\Page as PageInterface;

final class Page implements PageInterface
{
    protected $title;
    protected $sections = [];

    public function __construct($title, $sections = [])
    {
        $this->title = $title;
        $this->sections = $sections;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getSections()
    {
        return $this->sections;
    }

    public function addSection($section)
    {
        $this->sections[] = $section;
    }
}
