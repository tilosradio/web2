<?php


namespace RadioTest\Controller;


use Radio\Formatter\RestrictedHtmlTransformer;
use Radio\Formatter\TagFormatter;

class TagFormatterTest extends \PHPUnit_Framework_TestCase
{

    public function testSimple()
    {

        $t = new TagFormatter();
        $this->assertEquals("őz asd egy&#337; qwe",$t->format("őz asd egy&#337; qwe"));
        $this->assertEquals("asd <a href=\"/tag/tag\"><span class=\"label label-primary\">tag</span></a> qwe",$t->format("asd #tag qwe"));
        $this->assertEquals("asd <a href=\"/tag/tag barmi\"><span class=\"label label-primary\">tag barmi</span></a> qwe",$t->format("asd #{tag barmi} qwe"));
        $this->assertEquals("asd <a href=\"/tag/tag\"><span class=\"label label-success\">tag</span></a> qwe",$t->format("asd @tag qwe"));
    }
}