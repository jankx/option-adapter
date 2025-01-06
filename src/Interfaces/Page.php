<?php

namespace Jankx\Adapter\Options\Interfaces;

interface Page
{
    public function getTitle();

    public function getSections();

    public function addSection($section);
}
