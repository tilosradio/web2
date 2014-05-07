<?php

namespace Radio\Mapper;

use Radio\Controller\EpisodeUtil;

/**
 * Simple field mapping based on name.
 *
 * @package Radio\Mapper
 */
class EpisodeResourceURLField implements Mapper
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
        $resources = array();
        $resources['m3u'] = array('url' => "xxx");
        $duration = ($from['plannedTo']->getTimestamp() - $from['plannedFrom']->getTimestamp()) / 60;

        $streams = array();
        $links = EpisodeUtil::getMp3StreamLinks($from['plannedFrom']->getTimestamp(), $duration);
        foreach ($links as $link) {
            $streams[] = array('url' => $link['file'], 'start' => $link['epoch']);
        }
        $resources = array('stream' => $streams);
        //$url = '/episode/' . $from['show']['alias'] . "/" . date("Y/m/d", $from['plannedFrom']->getTimestamp());
        $setter->set($to, "resources", $resources);
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