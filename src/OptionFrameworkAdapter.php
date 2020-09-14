<?php
namespace Jankx\Option;

abstract class OptionFrameworkAdapter
{
    /**
     * Implement initialize options
     *
     * @return void
     */
    public function prepare()
    {
        // Implement code here
    }

    abstract public function getOption($name, $defaultValue = null);
}
