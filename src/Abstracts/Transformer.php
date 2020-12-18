<?php
namespace Jankx\Option\Abstracts;

use Jankx\Options\Intefaces\Transformer as TransformerInterface;

abstract class Transformer implements TransformerInterface
{
    public function __construct($reader)
    {
    }
}
