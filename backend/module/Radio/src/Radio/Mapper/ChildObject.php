<?php


namespace Radio\Mapper;


class ChildObject implements Mapper
{

    private $mappers = [];
    private $name;
    private $type;

    function __construct($name, $type = null)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function map(&$from, &$to, $setter)
    {
        if (array_key_exists($this->name, $from)) {

            $newValue = $setter->ensureExists($to, $this->name, $this->type, $from[$this->name]);
            if ($from[$this->name]) {
                foreach ($this->mappers as $mapper) {
                    $mapper->map($from[$this->name], $newValue, $setter);
                }
            }
            $setter->set($to, $this->name, $newValue);

        }
    }

    public function addMapper($mapper)
    {
        $this->mappers[] = $mapper;
        return $mapper;
    }
} 