<?php


namespace RadioTest\Mapper;


use Radio\Mapper\ChildCollection;
use Radio\Mapper\DateField;
use Radio\Mapper\Field;
use Radio\Mapper\ChildObject;
use Radio\Mapper\InternalLinkField;
use Radio\Mapper\ObjectMapper;
use Radio\Mapper\ResourceField;
use Radio\Mapper\TextContent;

class MapperTest extends \PHPUnit_Framework_TestCase
{

    public function testSimpleMapping()
    {

        $a = [];
        $a['id'] = 12;
        $a['name'] = "asd";
        $a['volatile'] = "qwe";

        $to = [];

        $mapper = new ObjectMapper();
        $mapper->addMapper(new Field("id"));
        $mapper->addMapper(new Field("name"));

        $result = $mapper->map($a, $to);

        $this->assertArrayHasKey("id", $to);
        $this->assertArrayHasKey("name", $to);
        $this->assertArrayNotHasKey("volatile", $to);
        $this->assertEquals(12, $to['id']);

    }


    public function testConverterMappings()
    {

        $a = [];
        $a['image'] = "asd.jpg";
        $a['link'] = "/qwe";
        $a['volatile'] = "xx";
        $a['date'] = new \DateTime();
        $a['content'] = "<h1>asd</h1>";
        $a['format'] = "html";
        $baseUrl = "http://tilos.hu";

        $to = [];

        $mapper = new ObjectMapper();
        $mapper->addMapper(new ResourceField("image", $baseUrl));
        $mapper->addMapper(new InternalLinkField("link", $baseUrl));
        $mapper->addMapper(new DateField("date"));
        $mapper->addMapper(new TextContent());



        $result = $mapper->map($a, $to);

        $this->assertArrayHasKey("image", $to);
        $this->assertArrayHasKey("link", $to);
        $this->assertArrayNotHasKey("volatile", $to);
        $this->assertEquals("http://tilos.hu/upload/asd.jpg", $to['image']);
        $this->assertEquals("http://tilos.hu//qwe", $to['link']);
        $this->assertEquals($a['date']->getTimestamp(), $to['date']);
        $this->assertEquals("<h1>asd</h1>", $to['content']);
        $this->assertEquals("<h1>asd</h1>", $to['formatted']);


    }

    public function testChildMapping()
    {

        $a = [];
        $a['id'] = 12;
        $a['name'] = "asd";
        $a['volatile'] = "qwe";

        $b = [];
        $b['id'] = 25;
        $a['b'] = $b;

        $to = [];

        $mapper = new ObjectMapper();
        $mapper->addMapper(new Field("id"));
        $mapper->addMapper(new Field("name"));

        $child = new ChildObject("b");
        $mapper->addMapper($child);
        $child->addMapper(new Field("id"));

        $result = $mapper->map($a, $to);

        //var_dump($to);

        $this->assertArrayHasKey("id", $to);
        $this->assertArrayHasKey("name", $to);
        $this->assertArrayNotHasKey("volatile", $to);
        $this->assertEquals(12, $to['id']);

        $this->assertArrayHasKey("b", $to);
        $this->assertArrayHasKey("id", $to['b']);
        $this->assertEquals(25, $to['b']['id']);

    }

    public function testChildCollection()
    {

        $a = [];
        $a['id'] = 12;
        $a['name'] = "asd";
        $a['volatile'] = "qwe";

        $b1 = [];
        $b1['id'] = 25;

        $b2 = [];
        $b2['id'] = 25;

        $a['b'] = [$b1, $b2];

        $to = [];

        $mapper = new ObjectMapper();
        $mapper->addMapper(new Field("id"));
        $mapper->addMapper(new Field("name"));

        $child = new ChildCollection("b");
        $mapper->addMapper($child);
        $child->addMapper(new Field("id"));

        $result = $mapper->map($a, $to);

        $this->assertArrayHasKey("id", $to);
        $this->assertArrayHasKey("name", $to);
        $this->assertArrayNotHasKey("volatile", $to);
        $this->assertEquals(12, $to['id']);

        $this->assertArrayHasKey("b", $to);
        $this->assertArrayHasKey("id", $to['b'][0]);
        $this->assertEquals(25, $to['b'][0]['id']);

    }
}