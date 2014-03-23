<?php
/**
 * Created by IntelliJ IDEA.
 * User: elek
 * Date: 12/12/13
 * Time: 8:01 PM
 */

namespace Radio\Mapper;

/**
 * Simple field mapping based on name.
 *
 * @package Radio\Mapper
 */
class Field implements Mapper
{

    private $name;

    private $required = false;

    private $validator;

    function __construct($name)
    {
        $this->name = $name;
    }

    public static function of($name)
    {
        return new Field($name);
    }

    public function map(&$from, &$to, $setter)
    {

        if (array_key_exists($this->name, $from)) {
            if (!empty($this->validator)) {
                $result = $this->validator->validate($from[$this->name]);
                if ($result !== true) {
                    throw new \Exception("Hiba a $this->name mezőben: " . $result);
                }
            }
            $setter->set($to, $this->name, $from[$this->name]);
        }

        if ($this->required) {
            $val = $setter->get($to, $this->name);
            if (empty($val)) {
                throw new \Exception("Missing required field: " . $this->name);
            }
        }
    }

    public function valid($validator)
    {
        $this->validator = $validator;
        return $this;
    }

    public function required()
    {
        $this->required = true;
        return $this;
    }


}