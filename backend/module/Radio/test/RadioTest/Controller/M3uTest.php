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
use Radio\Controller\M3u;

class ControllerTest extends TestBase {

    protected function setUp() {
        $this->initTest("M3u", new \Radio\Controller\M3u());

    }


    public function testDownloadAction() {
        //given
        $this->routeMatch->setParam('action', 'download');
        $this->routeMatch->setParam('from', '1360000000');
        $this->routeMatch->setParam('duration', '90');
        //when
        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        //then        
        $this->assertEquals(200, $response->getStatusCode());


        $body = preg_split("/\n/", $response->getContent());
        //var_dump($body);
        $this->assertEquals(7, sizeof($body));
        $this->assertContains("2013.02.04 1930", $body[4]);
    }
}

?>