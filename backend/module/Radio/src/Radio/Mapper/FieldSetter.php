<?php
namespace Radio\Mapper;


interface FieldSetter
{

    public function set(&$container, $propertyName, &$value);

    public function get(&$container, $propertyName);

    public function ensureExists(&$container, $propertyName, $type, $originalChild);

    public function add(&$container, $propertyName, &$value);

    public function findChild($from, $type);

}

?>