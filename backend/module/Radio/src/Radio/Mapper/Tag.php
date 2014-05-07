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

    public function extractTags($content)
    {
        $matches = [];
        $result = [];
        preg_match_all("/#\w+/", $content, $matches);

        foreach ($matches[0] as $match) {
            $t = new \Radio\Entity\Tag();
            $t->setName(substr($match, 1));
            $result[] = $t;
        }
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
                    $dt[] = array("id" => $result[0]['id'], "name" => $tag->getName());
                } else {
                    //else
                    $dt[] = array('name' => $tag->getName());
                }

            }


            $from['tags'] = $dt;
        }
    }
}