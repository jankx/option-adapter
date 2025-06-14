<?php

namespace Jankx\Adapter\Options\Interfaces;

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

interface Section
{
    public function getTitle();

    public function getFields();

    public function addField($field);
}
