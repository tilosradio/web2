<?php

namespace Radio\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity 
 * @ORM\Table(name="textcontent")
 * */
class TextContent {

    /**
     * @ORM\Id 
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue 
     * */
    protected $id;

    /**
     * @ORM\Column(type="string",length=255) 
     * */
    protected $title;

    /**
     * @ORM\Column(type="string",length=15) 
     * 
     * type: eg.: blog, episode (=adásnapló), news
     * 
     * */
    protected $type;

    /**
     * @ORM\Column(type="string",length=15) 
     * 
     * format: eg.: html, markdown, old
     * 
     * */
    protected $format;

    /**
     * @ORM\Column(type="text") 
     * */
    protected $content;

    /**
     * @ORM\Column(type="datetime") 
     * */
    protected $created;

    /**
     * @ORM\Column(type="datetime") 
     * */
    protected $modified;

    /**
     * @ORM\Column(type="string") 
     * */
    protected $author;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="textcontents", cascade="persist")
     * @ORM\JoinTable(name="tag_textcontent")
     **/
    protected $tags = [];

    /**
     * @ORM\Column(type="string", length=60) 
     * */
    protected $alias;

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @param mixed $tags
     */
    public function addTags($tag)
    {
        $this->tags[] = $tag;
    }


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function getCreated() {
        return $this->created;
    }

    public function setCreated($created) {
        $this->created = $created;
    }

    public function getModified() {
        return $this->modified;
    }

    public function setModified($modified) {
        $this->modified = $modified;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor($author) {
        $this->author = $author;
    }

    public function getAlias() {
        return $this->alias;
    }

    public function setAlias($alias) {
        $this->alias = $alias;
    }

    public function getFormat() {
        return $this->format;
    }

    public function setFormat($format) {
        $this->format = $format;
    }

    public function toArray() {
        $a = array();
        $a['id'] = $this->getId();
        $a['title'] = $this->getTitle();
        $a['type'] = $this->getType();
        $a['content'] = $this->getContent();
        $a['alias'] = $this->getAlias();
        return $a;
    }

}

?>