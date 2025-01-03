<?php
namespace Jankx\Adapter\Options\Abstracts;

use ReflectionObject;
use ReflectionProperty;
use Jankx\Adapter\Options\Interfaces\Adapter as AdapterInterface;

abstract class Adapter implements AdapterInterface
{
    public function prepare()
    {
        // Preparing before load framework
    }

    public function convertFieldsToArgs($field)
    {
        $ret = array();
        $ref = new ReflectionObject($field);

        $properties = $ref->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        $mappingFields = static::mapFieldProperties();

        foreach ($properties as $property) {
            $mapKey = isset($mappingFields[$property->getName()])
                ? $mappingFields[$property->getName()]
                : $property->getName();

            $property->setAccessible(true);

            $ret[$mapKey] = $this->getSectionArgs(
                $property->getName(),
                $property->getValue($field)
            );
        }


        if (count($field->props) <= 0) {
            return $ret;
        }

        foreach ($field->props as $prop => $value) {
            $ret[$prop] = $value;
        }
        return $ret;
    }

    public function getSectionArgs($argKey, $value)
    {
        switch ($argKey) {
            case 'fields':
                $ret = array();
                foreach ($value as $field) {
                    $ret[] = $this->convertFieldsToArgs($field);
                }
                return $ret;
            default:
                return $value;
        }
    }

    public function convertSectionToArgs($section)
    {
        $ret = array();
        $ref = new ReflectionObject($section);

        $properties = $ref->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        $mappingFields = static::mapSectionFields();

        foreach ($properties as $property) {
            $mapKey = isset($mappingFields[$property->getName()])
                ? $mappingFields[$property->getName()]
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
