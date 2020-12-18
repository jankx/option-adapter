<?php
namespace Jankx\Option\Interfaces;

interface Adapter
{
    public function getOption($name, $defaultValue = null);
}
