<?php

namespace Jankx\Adapter\Options\Interfaces;

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

interface Field
{
    public function getId();

    public function getTitle();

    public function getSubtitle();

    public function getType();

    public function getDefault();

    public function getDescription();

    public function getOptions();

    public function hasOptions();

    public function getMin();

    public function hasMin();

    public function getMax();

    public function hasMax();

    public function getStep();

    public function hasStep();

    public function getIcon();

    public function getArgs();
}
