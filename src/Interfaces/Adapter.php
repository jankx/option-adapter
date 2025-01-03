<?php
namespace Jankx\Adapter\Options\Interfaces;

interface Adapter
{
    public function register_admin_menu($menu_title, $display_name);

    public function getOption($name, $defaultValue = null);

    public function createSections($options);

    public function addSection($section);

    public function setArgs($args);

    public function convertSectionToArgs($section);

    public static function mapSectionFields();

    public static function mapFieldProperties();
}
