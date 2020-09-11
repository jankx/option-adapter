<?php
namespace Jankx\Option;

abstract class OptionFrameworkAdapter
{
    abstract public function getOption($name, $defaultValue = null);
}
