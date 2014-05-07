<?php

namespace RadioAdminTest\Controller;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Radio\Controller\Atom;
use RadioAdmin\Controller\Show;
use RadioAdmin\Controller\Text;
use RadioAdmin\Controller\User;
use RadioTest\Bootstrap;
use RadioTest\Fixitures\BaseData;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Mvc\Router\RouteMatch;

class TextTest extends TestBase
{

    protected function setUp()
    {
        $this->initTest("RadioAdmin\Controller\Text", new Text());
        $this->baseData();
        $this->routeMatch->setParam("permission", "admin");

    }



    public function testCreatePage()
    {
        $this->user = $this->createUser(1, "admin", "admin");
        //given

        $this->routeMatch->setParam('action', 'create');
        $this->request->setMethod("post");

        $this->request->getHeaders()->addHeaderLine("content-type: application/json");
        $this->request->setContent(Json::encode(
            ['title' => 'xxx', 'alias' => 'blabla', 'content' => 'qwe']));

        //when
        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        //then

        $res = $result->getVariables();
        var_dump($res);

        $this->assertTrue($res['success']);
        $id = $res['data']['id'];
        $d = $this->em->find("\Radio\Entity\TextContent", $id);
        $this->assertNotEmpty($d);
        $this->assertEquals("xxx", $d->getTitle());
        $this->assertEquals("blabla", $d->getAlias());
        $this->assertEquals("qwe", $d->getContent());



    }

    public function testCreatePageWithTags()
    {
        $this->user = $this->createUser(1, "admin", "admin");
        //given

        $this->routeMatch->setParam('action', 'create');
        $this->request->setMethod("post");

        $this->request->getHeaders()->addHeaderLine("content-type: application/json");
        $this->request->setContent(Json::encode(
            ['title' => 'xxx', 'alias' => 'blabla', 'content' => "Ahoj #bela ez itt qwe\n #txag"]));

        //when
        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        //then

        $res = $result->getVariables();
        //var_dump($res);

        $this->assertTrue($res['success']);
        $id = $res['data']['id'];
        $d = $this->em->find("\Radio\Entity\TextContent", $id);

        $this->assertNotEmpty($d);
        $this->assertEquals(2, sizeof($d->getTags()));
        $this->assertEquals("xxx", $d->getTitle());
        $this->assertEquals("blabla", $d->getAlias());
        $this->assertEquals("Ahoj #bela ez itt qwe\n #txag", $d->getContent());
        $this->assertEquals(2, sizeof($d->getTags()));
        $this->assertEquals("bela", $d->getTags()[0]->getName());
        $this->assertEquals("txag", $d->getTags()[1]->getName());



    }

    

}

?>