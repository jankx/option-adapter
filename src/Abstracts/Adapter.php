<?php
namespace Jankx\Option\Abstracts;

use ReflectionObject;
use ReflectionProperty;
use Jankx\Option\Interfaces\Adapter as AdapterInterface;

abstract class Adapter implements AdapterInterface
{
    protected static $mapSectionFields = array();
    protected static $mapFieldProperties = array();

    protected function getSectionFields($fields)
    {
        $ret = array();
        foreach ($fields as $field) {
            $ret[] = $this->convertObjectToArgs(
                $field,
                static::$mapFieldProperties
            );
        }
        return $ret;
    }

    protected function getSectionArgs($argName, $value)
    {
        switch ($argName) {
            case 'fields':
                return $this->getSectionFields($value);
            default:
                return $value;
        }
    }

    public function convertObjectToArgs($section, $mapFields)
    {
        $ret = array();
        $ref = new ReflectionObject($section);

        $properties = $ref->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);

        foreach ($properties as $property) {
            $mapKey = isset(static::$mapSectionFields[$property->getName()])
                ? static::$mapSectionFields[$property->getName()]
                : $property->getName();

            $property->setAccessible(true);

            $ret[$mapKey] = $this->getSectionArgs(
                $property->getName(),
                $property->getValue($section)
            );
        }

        return $ret;
    }
}
