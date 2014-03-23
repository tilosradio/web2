<?php

namespace RadioAdminTest\Controller;


use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Radio\Controller\Atom;
use RadioAdmin\Controller\Url;
use RadioAdmin\Controller\User;
use RadioTest\Bootstrap;
use RadioTest\Fixitures\BaseData;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Mvc\Router\RouteMatch;


class UrlTest extends TestBase
{

    protected function setUp()
    {
        $this->initTest("RadioAdmin\Controller\Url", new Url());
        $this->baseData();
    }

    public function testCreateUrl()
    {

        $show = $this->em->find("\Radio\Entity\Show", 1);
        $this->assertNotEmpty($show);
        $this->assertEquals(2, count($show->getUrls()));


        $this->user = $this->createUser(1, "admin", "admin");
        //given

        $this->routeMatch->setParam('action', 'create');
        $this->request->setMethod("post");

        $this->request->getHeaders()->addHeaderLine("content-type: application/json");
        $this->request->setContent(Json::encode(
            ['url' => 'http://index.hu', 'showId' => 1]));

        //when
        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        //then

        $res = $result->getVariables();
        var_dump($res);

        $this->assertTrue($res['success']);
        $id = $res['data']['id'];
        $d = $this->em->find("\Radio\Entity\Url", $id);
        $this->assertNotEmpty($d);
        $this->assertEquals("http://index.hu", $d->getUrl());

        $show = $this->em->find("\Radio\Entity\Show", 1);
        $this->assertNotEmpty($show);
        $this->assertEquals(3, count($show->getUrls()));
    }

    public function testDeleteUrl()
    {

        $show = $this->em->find("\Radio\Entity\Show", 1);
        $this->assertNotEmpty($show);
        $this->assertEquals(2, count($show->getUrls()));


        $this->user = $this->createUser(1, "admin", "admin");
        //given

        $this->routeMatch->setParam('action', 'delete');
        $this->routeMatch->setParam('id', '3');

        $this->request->setMethod("delete");
        $this->request->getQuery()->set("showId", 1);

        //when
        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        //then

        $res = $result->getVariables();
        var_dump($res);

        $this->assertTrue($res['success']);

        $show = $this->em->find("\Radio\Entity\Show", 1);
        $this->assertNotEmpty($show);
        $this->assertEquals(1, count($show->getUrls()));
    }
}

?>