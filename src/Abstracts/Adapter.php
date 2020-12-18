<?php
namespace Jankx\Option\Abstracts;

use Jankx\Option\Interfaces\Adapter as AdapterInterface;

abstract class Adapter implements AdapterInterface
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
}
