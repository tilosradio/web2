<?php

namespace Radio\Mapper;

/**
 * Simple field mapping based on name.
 *
 * @package Radio\Mapper
 */
class EpisodeURLField implements Mapper
{
    private $validator;

    function __construct()
    {
    }

    public static function of($name)
    {
        return new Field($name);
    }

    public function map(&$from, &$to, $setter)
    {

        $url = '/episode/' . $from['show']['alias'] . "/" . date("Y/m/d", $from['plannedFrom']->getTimestamp());
        $setter->set($to, "url", $url);
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