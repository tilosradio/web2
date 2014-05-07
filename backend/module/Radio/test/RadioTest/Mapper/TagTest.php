<?php


namespace RadioTest\Mapper;


use Radio\Controller\Atom;

use Radio\Mapper\ArrayFieldSetter;
use Radio\Mapper\ChildCollection;
use Radio\Mapper\Field;
use Radio\Mapper\ChildObject;
use Radio\Mapper\ObjectFieldSetter;
use Radio\Mapper\ObjectMapper;
use Radio\Mapper\Tag;
use RadioTest\Controller\TestBase;

class TagTest extends TestBase
{

    protected function setUp()
    {
        $this->initTest("Atom", new Atom());
    }


    public function testExtractTags()
    {
        //given
        $content = "asd\nlajos #nagyon #okos";
        $t = new Tag("content", $this->em);

        //when
        $tags = $t->extractTags($content);

        //then
        $this->assertEquals(2, sizeof($tags));
        $this->assertEquals("nagyon", $tags[0]->getName());
    }

    public function testMap()
    {

        //given
        $a = [];
        $a['content'] = "#contentx asd asd\nvalami #txag we";
        $t = new Tag("content", $this->em);
        $to = [];

        //when
        $t->map($a, $to, new ArrayFieldSetter());

        //then
        $from = $a;
        $this->assertArrayHasKey("tags", $from);
        $this->assertEquals(2, sizeof($from['tags']));
        $this->assertEquals("contentx", $from['tags'][0]['name']);
        $this->assertEquals("txag", $from['tags'][1]['name']);
        $this->assertEquals(1, $from['tags'][1]['id']);


    }

}