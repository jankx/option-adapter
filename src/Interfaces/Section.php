<?php

namespace Jankx\Adapter\Options\Interfaces;

interface Section
{
    public function getTitle();

    public function getFields();

    public function addField($field);
}
