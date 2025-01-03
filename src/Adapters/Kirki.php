<?php
/**
 * Kirki Customizer Framework Adapter
 *
 * Kirki allows theme developers to build themes quicker & more easily.
 *
 * With over 30 custom controls ranging from simple sliders to complex typography controls
 * with Google-Fonts integration and features like automatic CSS & postMessage script generation,
 * Kirki makes theme development a breeze.
 *
 * @package Jankx
 * @subpackage Option
 * @author Puleeno Nguyen <puleeno@gmail.com>
 * @link https://kirki.org/
 * @since 1.0.0
 */
namespace Jankx\Adapter\Options;

use Jankx\Adapter\Options\Abstracts\Adapter;

class Kirki extends Adapter
{
    public function setArgs($args)
    {
    }

    public function addSection($section)
    {
    }

    public static function mapSectionFields()
    {
        return [];
    }

    public static function mapFieldProperties()
    {
        return [];
    }

    public function getOption($name, $defaultValue = null)
    {
    }

    public function register_admin_menu($menu_title, $display_name)
    {
    }

    public function createSections($options)
    {
        if (is_a($options, Options::class)) {
            foreach ($options->getSections() as $section) {
                $this->addSection($section);
            }
        }
    }
}
