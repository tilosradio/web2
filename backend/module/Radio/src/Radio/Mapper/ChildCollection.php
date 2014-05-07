<?php


namespace Radio\Mapper;


class ChildCollection implements Mapper
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
            if (!array_key_exists($this->name, $to)) {
                $k = [];
                $setter->set($to, $this->name, $k);
            }
            foreach ($from[$this->name] as &$child) {
                $n = $setter->findChild($child, $this->type);
                foreach ($this->mappers as $mapper) {
                    $mapper->map($child, $n, $setter);
                }
                $setter->add($to, $this->name, $n);
            }
        }
    }

    public function addMapper($mapper)
    {
        $this->mappers[] = $mapper;
        return $mapper;
    }
} 