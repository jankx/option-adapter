<?php
namespace Jankx\Option\Interfaces;

interface Adapter
{
    public function register_admin_menu($menu_title, $display_name);

    public function getOption($name, $defaultValue = null);

    public function addSection($sectionId, $sectionArgs);

    public function setArgs($args);
}
