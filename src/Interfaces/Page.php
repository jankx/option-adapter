<?php

namespace Jankx\Adapter\Options\Interfaces;

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

interface Page
{
    public function getTitle();

    public function getSections();

    public function addSection($section);
}
