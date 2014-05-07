<?php
namespace Radio\Mapper;

class ArrayFieldSetter implements FieldSetter
{
    function set(&$container, $propertyName, &$value)
    {
        $container[$propertyName] = $value;
    }

    function get(&$container, $propertyName)
    {
        return $container[$propertyName];
    }

    public function ensureExists(&$container, $propertyName, $type, $originalChild)
    {
        if (!array_key_exists($propertyName, $container)) {
            $container[$propertyName] = array();
        }
        return $container[$propertyName];
    }

    public function add(&$container, $propertyName, &$value)
    {
        $container[$propertyName][] = $value;
    }

    public function findChild($from, $type)
    {
        return [];
    }
}

?>