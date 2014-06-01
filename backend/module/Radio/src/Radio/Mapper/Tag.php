<?php
/**
 * Created by IntelliJ IDEA.
 * User: elek
 * Date: 12/12/13
 * Time: 8:01 PM
 */

namespace Radio\Mapper;

use Radio\Formatter\Formatter;

/**
 * Find tags from a content field.
 *
 * @package Radio\Mapper
 */
class Tag implements Mapper
{

    private $name;

    private $em;

    function __construct($fieldName, $em)
    {
        $this->name = $fieldName;
        $this->em = $em;
    }

    public function addMatches($matches, $type, &$result)
    {
        foreach ($matches[1] as $match) {
            $t = new \Radio\Entity\Tag();
            $t->setType($type);
            $t->setName($match);
            $result[] = $t;
        }
    }

    public function extractTags($content)
    {
        $matches = [];
        $result = [];
        $w = "[\w+&;]";
        preg_match_all("/#(" . $w . "+)/", $content, $matches);
        $this->addMatches($matches, \Radio\Entity\Tag::$GENERIC, $result);

        preg_match_all("/\#\{(.+?)\}/", $content, $matches);
        $this->addMatches($matches, \Radio\Entity\Tag::$GENERIC, $result);

        preg_match_all("/@(" . $w . "+)/", $content, $matches);
        $this->addMatches($matches, \Radio\Entity\Tag::$PERSON, $result);

        preg_match_all("/\@\{(.+?)\}/", $content, $matches);
        $this->addMatches($matches, \Radio\Entity\Tag::$PERSON, $result);

        return $result;
    }

    public function map(&$from, &$to, $setter)
    {
        if (array_key_exists($this->name, $from)) {
            $content = $from[$this->name];
            $dt = [];
            $tags = $this->extractTags($content);
            foreach ($tags as $tag) {
                $qb = $this->em->createQueryBuilder();
                $qb->select('t')
                    ->from('\Radio\Entity\Tag', 't')
                    ->where('t.name = :name');

                $q = $qb->getQuery();
                $q->setParameter("name", $tag->getName());
                $result = $q->getArrayResult();
                if ($result) {
                    //if $tag has already been exists
                    $dt[] = array("id" => $result[0]['id'], "name" => $tag->getName(), 'type' => $tag->getType());
                } else {
                    //else
                    $dt[] = array('name' => $tag->getName(), 'type' => $tag->getType());
                }

            }


            $from['tags'] = $dt;
        }
    }
}