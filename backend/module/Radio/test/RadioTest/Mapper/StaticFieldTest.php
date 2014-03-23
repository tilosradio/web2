<?php


namespace RadioTest\Mapper;


use Radio\Mapper\ChildCollection;
use Radio\Mapper\DateField;
use Radio\Mapper\Field;
use Radio\Mapper\ChildObject;
use Radio\Mapper\InternalLinkField;
use Radio\Mapper\ObjectMapper;
use Radio\Mapper\ResourceField;
use Radio\Mapper\StaticField;
use Radio\Mapper\TextContent;

class StaticFieldTest extends \PHPUnit_Framework_TestCase
{

    public function testStaticField()
    {

        $a = [];
        $a['id'] = 12;
        $a['name'] = "asd";
        $a['volatile'] = "qwe";

        $to = [];

        $mapper = new ObjectMapper();
        $mapper->addMapper(new StaticField("id",23));
        $mapper->addMapper(new Field("name"));

        $result = $mapper->map($a, $to);

        $this->assertEquals(23, $to["id"]);


    }

    public function testStaticFieldIfEmpty()
    {

        $a = [];
        $a['id'] = 12;
        $a['name'] = "asd";
        $a['volatile'] = "qwe";

        $to = [];

        $mapper = new ObjectMapper();
        $mapper->addMapper(StaticField::of("id",23)->ifEmpty());
        $mapper->addMapper(new Field("name"));

        $result = $mapper->map($a, $to);

        $this->assertEquals(23, $to["id"]);


    }



}