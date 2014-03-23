<?php

namespace Radio\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="episode")
 * */
class Episode {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * */
    protected $id;
    /**
     * @ORM\Column(type="datetime")
     * */
    protected $plannedFrom;
    /**
     * @ORM\Column(type="datetime")
     * */
    protected $plannedTo;
    /**
     * @ORM\Column(type="datetime")
     * */
    protected $realFrom;
    /**
     * @ORM\Column(type="datetime")
     * */
    protected $realTo;
    /**
     * @ORM\ManyToOne(targetEntity = "Show")
     * @ORM\JoinColumn(name = "radioshow_id", referencedColumnName = "id")
     */
    protected $show;
    /**
     * So called 'Adasnaplo'
     *
     * @ORM\OneToOne(targetEntity="TextContent",cascade={"persist"})
     * @ORM\JoinColumn(name="textcontent_id", referencedColumnName="id")
     */
    protected $text;

    protected $m3uUrl;
    /**
     *
     * false for the auto generated pseudo episde record.
     */
    protected $persistent = true;

    public function getId() {
        return $this->id;
    }

    public function getPlannedFrom() {
        return $this->plannedFrom;
    }

    public function getPlannedTo() {
        return $this->plannedTo;
    }

    public function getRealFrom() {
        return $this->realFrom;
    }

    public function getRealTo() {
        return $this->realTo;
    }

    public function getShow() {
        return $this->show;
    }

    public function setPlannedFrom($plannedFrom) {
        $this->plannedFrom = $plannedFrom;
    }

    public function setPlannedTo($plannedTo) {
        $this->plannedTo = $plannedTo;
    }

    public function setRealFrom($realFrom) {
        $this->realFrom = $realFrom;
    }

    public function setRealTo($realTo) {
        $this->realTo = $realTo;
    }

    public function setShow($show) {
        $this->show = $show;
    }

    public function getText() {
        return $this->text;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function getM3uUrl() {
        return $this->m3uUrl;
    }

    public function setM3uUrl($m3uUrl) {
        $this->m3uUrl = $m3uUrl;
    }

    public function toArray() {
        $a['id'] = $this->getId();

        $a['plannedFrom'] = $this->getPlannedFrom()->getTimestamp();
        $a['plannedTo'] = $this->getPlannedTo()->getTimestamp();
        $a['persistent'] = $this->getPersistent();
        $a['m3u'] = $this->getM3uUrl();
        if ($this->getText()) {
            $a['text'] = $this->getText()->toArray();
        }
        if ($this->getShow()) {
            $a['show'] = [];
            $a['show']['id'] = $this->getShow()->getId();
            $a['show']['name'] = $this->getShow()->getName();
        }
        return $a;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getPersistent() {
        return $this->persistent;
    }

    public function setPersistent($persistent) {
        $this->persistent = $persistent;
    }



}

?>
