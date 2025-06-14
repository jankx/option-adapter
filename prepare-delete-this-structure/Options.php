<?php

namespace Jankx\Adapter\Options\Specs;

if (!defined('ABSPATH')) {
    exit('Cheatin huh?');
}

use Jankx\Adapter\Options\Specs\Section;

class Options
{
    protected $options;
    protected $sections = array();

    public function __construct($options)
    {
        $this->setOptions($options);
    }

    public function setOptions($options)
    {
        if (is_array($options)) {
            $this->options = $options;
        }
    }

    // Transform RAW options to Jankx Option Sections
    public function transformToSections()
    {
        if (empty($this->options)) {
            return $this->sections;
        }

        foreach (array_values($this->options) as $sectionArgs) {
            $section = Section::createFromArray($sectionArgs);
            $fields = array_get($sectionArgs, 'fields', []);
            if (is_array($fields) && count($fields) > 0) {
                $section->createFieldsFromArray($fields);
            }
            array_push($this->sections, $section);
        }
    }

    public function getSections()
    {
        return $this->sections;
    }
}
