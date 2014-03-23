<?php
/**
 * Created by IntelliJ IDEA.
 * User: elek
 * Date: 12/12/13
 * Time: 8:01 PM
 */

namespace Radio\Mapper;

/**
 * Set static value toe a field.
 *
 * @package Radio\Mapper
 */
class StaticField implements Mapper
{

    private $name;

    private $value;

    private $ifEmpty;

    private $validator;

    function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public static function of($name, $value)
    {
        return new StaticField($name, $value);
    }

    public function map(&$from, &$to, $setter)
    {
        if ($this->ifEmpty) {
            if ($setter->get($from,$this->name) == null) {
                return;
            }
        }
        $setter->set($to, $this->name, $this->value);
    }

    public function ifEmpty()
    {
        $this->ifEmpty = true;
        return $this;
    }

}