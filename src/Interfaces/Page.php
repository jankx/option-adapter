<?php

namespace Jankx\Adapter\Options\Interfaces;

if (!defined('ABSPATH')) {
    exit('Cheatin huh?');
}

interface Page
{
    public function getTitle();

    public function getSections();

    public function addSection($section);
}
