<?php

namespace RadioTest\Controller;

use Radio\Controller\Episode;
use RadioTest\Bootstrap;
use Zend\Form\Element\Radio;
use Zend\Json\Json;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Application\Controller\IndexController;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use PHPUnit_Framework_TestCase;
use Zend\Stdlib\Parameters;

class EpisodeTest extends TestBase {

    protected function setUp() {
        $this->initTest("Radio\Controller\Episode", new Episode());
        $this->baseData();
        $this->user = $this->createUser(1, "admin", "admin");
    }

    public function testEpisodeList() {
        $start = mktime(10, 0, 0, 1, 28, 2013);
        $end = mktime(15, 0, 0, 1, 28, 2013);
        //when        
        $this->request->setUri("/api/episode?start=$start&end=$end");
        $this->request->setQuery(new Parameters(['start' => $start, 'end' => $end]));
        $this->routeMatch->setParam('action', 'getList');

        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        //then
        $episodes = $result->getVariables();
        //var_dump($episodes);
        $this->assertTrue(count($episodes) > 0);
        $this->assertNotEmpty($episodes[0]['url']);
    }

    public function testEpisodeNext() {
//        $this->em->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
        //when
        $this->request->setUri("/api/episode/next");
        $this->routeMatch->setParam('action', 'next');

        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        //then                      e
        $episodes = $result->getVariables();
        //var_dump($episodes);
        $this->assertEquals(0,count($episodes));
    }



    public function testEpisodeGet() {
        //when
        $this->request->setUri("/api/episode/1");
        $this->request->setMethod("get");
        $this->routeMatch->setParam('id', 1);
        $this->routeMatch->setParam('action', 'get');



        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        //then
        $episode = $result->getVariables();
        //var_dump($episode);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(count($response) > 0);
        $this->assertEquals(1, $episode['id']);
        $this->assertEquals(1, sizeof($episode['text']['tags']));


    }

    public function testEpisodeGetWithNullText() {
        //when
        $this->request->setUri("/api/episode/2");
        $this->request->setMethod("get");
        $this->routeMatch->setParam('id', 2);
        $this->routeMatch->setParam('action', 'get');



        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        //then
        $episode = $result->getVariables();
        //var_dump($episode);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(count($response) > 0);
        $this->assertEquals(2, $episode['id']);

    }

}

?>