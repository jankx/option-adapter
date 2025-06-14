<?php

namespace Jankx\Adapter\Options\Interfaces;

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

interface Field
{
    public function getId();

    public function getTitle();

    public function getType();

    public function getArgs();
}
