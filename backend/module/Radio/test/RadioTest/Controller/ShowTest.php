<?php

namespace RadioTest\Controller;

use RadioTest\Bootstrap;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Application\Controller\IndexController;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use PHPUnit_Framework_TestCase;

class ShowTest extends TestBase
{


    protected function setUp()
    {
        $this->initTest("Radio\Controller\Show", new \Radio\Controller\Show());
        $this->baseData();
    }

    public function testShowGet()
    {
        //when        
        $this->routeMatch->setParam('id', '1');
        $this->routeMatch->setParam('action', 'get');
        $this->routeMatch->setParam("tilosRouter", true);


        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        //then

        $show = $result->getVariables();
        //var_dump($show);
        $this->assertEquals($show['name'], "Good show");
        $this->assertEquals(sizeof($show['urls']), 2);

    }


    public function testListOfEpisodes()
    {
        //when
        $this->routeMatch->setParam('id', '1');
        $this->routeMatch->setParam('action', 'listOfEpisodes');


        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        //then

        $episodes = $result->getVariables();
        //var_dump($show);
        $this->assertTrue(is_array($episodes));


    }


    public function testShowGetWithAlias()
    {
        //when        
        $this->routeMatch->setParam('id', 'goodshow');
        $this->routeMatch->setParam('action', 'get');
        $this->routeMatch->setParam("tilosRouter", true);

        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        //then

        $show = $result->getVariables();

        //var_dump($show);
        $this->assertEquals($show['id'], 1);

    }


    public function testEpisodeGetByAlias()
    {
        //when
        $this->request->setUri("/api/show/goodshow/episode/2013/01/28");
        $this->request->setMethod("get");
        $this->routeMatch->setParam('action', 'getEpisodeByAlias');
        $this->routeMatch->setParam('show', 'goodshow');
        $this->routeMatch->setParam('year', '2013');
        $this->routeMatch->setParam('month', '01');
        $this->routeMatch->setParam('day', '28');


        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        //then
        $episode = $result->getVariables();
        //var_dump($episode);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(count($response) > 0);
        $this->assertEquals(1, $episode['id']);

    }
}

?>