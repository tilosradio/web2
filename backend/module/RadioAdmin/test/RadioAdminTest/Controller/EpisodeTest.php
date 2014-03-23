<?php

namespace RadioAdminTest\Controller;

use RadioAdmin\Controller\Episode;
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
        $this->routeMatch->setParam("permission","guest");
    }



    public function testEpisodeCreate() {
        $start = mktime(10, 0, 0, 10, 28, 2013);
        $end = mktime(12, 0, 0, 10, 28, 2013);
        //when
        $this->request->setUri("/api/episode");
        $this->request->setMethod("post");
        $this->request->getHeaders()->addHeaderLine("content-type: application/json");
        $this->request->setContent(Json::encode(
            ['plannedFrom' => $start,
                'plannedTo' => $end,
                'title' => 'title',
                'content' => 'content',
                'radioshow_id' => 1]));
        $this->routeMatch->setParam('action', 'create');


        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        //then
        $episode = $result->getVariables();
//        var_dump($episode);

        $this->assertTrue($episode['success']);
        $id = $episode['data']['id'];

        $persisted = $this->em->find("\Radio\Entity\Episode", $id);
        $this->assertEquals($id, $persisted->getId());
        $this->assertEquals($start, $persisted->getPlannedFrom()->getTimestamp());
        $this->assertEquals($end, $persisted->getPlannedTo()->getTimestamp());
        $this->assertNotNull($persisted->getText());


    }

    public function testEpisodeEditWithText() {
        $start = mktime(10, 0, 0, 10, 28, 2013);
        $end = mktime(12, 0, 0, 10, 28, 2013);
        //when
        $this->request->setUri("/api/episode/1");
        $this->request->setMethod("put");
        $this->request->getHeaders()->addHeaderLine("content-type: application/json");
        $this->request->setContent(Json::encode(
            [
                'content' => 'new content',
                'title' => 'ccc'
            ]));
        $this->routeMatch->setParam('id', 1);
        $this->routeMatch->setParam('action', 'update');



        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        //then
        $episode = $result->getVariables();
        //var_dump($episode);

        $this->assertTrue($episode['success']);
        $id = $episode['data']['id'];

        $persisted = $this->em->find("\Radio\Entity\Episode", $id);
        $this->assertEquals($id, $persisted->getId());

        $this->assertNotNull($persisted->getText());
        $this->assertEquals("ccc", $persisted->getText()->getTitle());
        $this->assertEquals("new content", $persisted->getText()->getContent());


    }

    public function testEpisodeEditWithTextAfterNull() {
        $start = mktime(10, 0, 0, 10, 21, 2013);
        $end = mktime(12, 0, 0, 10, 21, 2013);
        //when
        $this->request->setUri("/api/episode/2");
        $this->request->setMethod("put");
        $this->request->getHeaders()->addHeaderLine("content-type: application/json");
        $this->request->setContent(Json::encode(
            [
                'content' => 'new content',
                'title' => 'ccc'
            ]));
        $this->routeMatch->setParam('id', 2);
        $this->routeMatch->setParam('action', 'update');



        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        //then
        $episode = $result->getVariables();
        //var_dump($episode);

        $this->assertTrue($episode['success']);
        $id = $episode['data']['id'];

        $persisted = $this->em->find("\Radio\Entity\Episode", $id);
        $this->assertEquals($id, $persisted->getId());

        $this->assertNotNull($persisted->getText());
        $this->assertEquals("ccc", $persisted->getText()->getTitle());
        $this->assertEquals("new content", $persisted->getText()->getContent());


    }

    public function testEpisodeCreateWithText() {
        $start = mktime(10, 0, 0, 10, 28, 2013);
        $end = mktime(12, 0, 0, 10, 28, 2013);
        //when
        $this->request->setUri("/api/episode");
        $this->request->setMethod("post");
        $this->request->getHeaders()->addHeaderLine("content-type: application/json");
        $this->request->setContent(Json::encode(
            ['plannedFrom' => $start,
                'plannedTo' => $end,
                'content' => 'content, content',
                'title' => 'title',
                'radioshow_id' => 1]));
        $this->routeMatch->setParam('action', 'create');


        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        //then
        $episode = $result->getVariables();
        //var_dump($episode);

        $this->assertTrue($episode['success']);
        $id = $episode['data']['id'];

        $persisted = $this->em->find("\Radio\Entity\Episode", $id);
        $this->assertEquals($id, $persisted->getId());
        $this->assertEquals($start, $persisted->getPlannedFrom()->getTimestamp());
        $this->assertEquals($end, $persisted->getPlannedTo()->getTimestamp());
        $this->assertNotNull($persisted->getText());
        $this->assertEquals("title", $persisted->getText()->getTitle());
        $this->assertEquals("content, content", $persisted->getText()->getContent());


    }



}

?>