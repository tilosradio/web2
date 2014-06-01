<?php

namespace Radio\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tag")
 * */
class Tag
{
    public static $PERSON = 1;
    public static $GENERIC = 0;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     * */
    protected $type = 0;

    /**
     * @ORM\Column(type="string")
     * */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="TextContent", mappedBy="tags")
     **/
    protected $textcontents;

    /**
     * @return mixed
     */
    public function getTextcontents()
    {
        return $this->textcontents;
    }

    /**
     * @param mixed $textcontents
     */
    public function setTextcontents($textcontents)
    {
        $this->textcontents = $textcontents;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }


}

?>