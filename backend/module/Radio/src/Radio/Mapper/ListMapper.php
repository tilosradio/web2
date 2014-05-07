<?php
/**
 * Created by IntelliJ IDEA.
 * User: elek
 * Date: 12/12/13
 * Time: 7:58 PM
 */

namespace Radio\Mapper;


class ListMapper implements Mapper
{

    private $mappers = [];

    private $type;

    function __construct($type = null)
    {
        $this->type = $type;

    }

    public function map(&$from, &$to, $setter)
    {
        foreach ($from as $item) {
            $newValue = $setter->findChild($item, $this->type);
            foreach ($this->mappers as $mapper) {
                $mapper->map($item, $newValue, $setter);
            }
            $to[] = $newValue;
        }
        return $to;
    }

    public function addMapper($mapper)
    {
        $this->mappers[] = $mapper;
        return $mapper;
    }
}