<?php

namespace RadioTest\Controller;

use Radio\Controller\Tag;
use RadioTest\Bootstrap;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Application\Controller\IndexController;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use PHPUnit_Framework_TestCase;

class TagTest extends TestBase
{


    protected function setUp()
    {
        $this->initTest("Radio\Controller\Tag", new Tag());
        $this->baseData();
    }

    public function testTagGetList()
    {
        //given
        $this->routeMatch->setParam('action', 'getList');


        //when
        $result = $this->controller->dispatch($this->request);

        //then
        $response = $this->controller->getResponse();
        $tags = $result->getVariables();
        //var_dump($tags);
        $this->assertEquals(2, sizeof($tags));
        $this->assertEquals("txag", $tags[0]['name']);
        $this->assertEquals(1, $tags[0]['count']);


    }

    public function testTagGet()
    {
        //given
        $this->routeMatch->setParam('action', 'get');
        $this->routeMatch->setParam('name', 'tag2');


        //when
        $result = $this->controller->dispatch($this->request);

        //then
        $response = $this->controller->getResponse();
        $tag = $result->getVariables();
        //var_dump($tags);
        $this->assertEquals("tag2", $tag['name']);
        $this->assertEquals("Episode log", $tag['episodes'][0]['text']['title']);
    }

}

?>