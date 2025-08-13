<?php

namespace Jankx\Adapter\Options\Interfaces;

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

interface Page
{
    public function getId();

    public function getTitle();

    public function getSubtitle();

    public function getDescription();

    public function getPriority();

    public function getIcon();

    public function getSections();

    public function addSection($section);
}
