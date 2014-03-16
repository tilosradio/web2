<?php

namespace RadioAdminTest\Controller;


use RadioAdmin\Controller\Auth;
use Radio\Entity\User;
use Zend\Json\Json;

class AuthTest extends TestBase {

    protected function setUp() {
        $this->initTest("Radio\Controller\Auth", new Auth());
        $this->baseData();
        $this->routeMatch->setParam("permission","guest");
    }

    public function testPasswordReset() {
        //given
        $this->routeMatch->setParam('action', 'passwordReset');

        $this->request->setMethod("post");
        $this->request->getHeaders()->addHeaderLine("content-type: application/json");
        $this->request->setContent(Json::encode(
            ['email' => 'test@test.hu']));


        //when
        $result = $this->controller->dispatch($this->request);

        //then
        $response = $this->controller->getResponse();
        $model = $this->event->getResult()->getVariables();


        //shoud be ok
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains("belépőkód", $model['message']);
        $this->assertTrue($model['success']);

        $this->em->createQueryBuilder();

        //we have the token in the db
        $q = $this->em->createQueryBuilder()->select("p")->from('\Radio\Entity\ChangePasswordToken', "p")->where('p.user = :user')->getQuery();
        $q->setParameter("user", $this->em->find("\Radio\Entity\User", 1));
        $result = $q->getArrayResult();
        $this->assertEquals(1, count($result));
        $old_token = $result[0]['token'];

        //regeneration, old record shoud be overwritten
        $result = $this->controller->dispatch($this->request);

        $response = $this->controller->getResponse();
        $model = $this->event->getResult()->getVariables();

        $q = $this->em->createQueryBuilder()->select("p")->from('\Radio\Entity\ChangePasswordToken', "p")->where('p.user = :user')->getQuery();
        $q->setParameter("user", $this->em->find("\Radio\Entity\User", 1));
        $result = $q->getArrayResult();
        $this->assertEquals(1, count($result));
        $new_token = $result[0]['token'];

        $this->assertNotEquals($old_token, $new_token);

    }

    public function testPasswordResetPhase2() {
        //given
        $this->routeMatch->setParam('action', 'passwordReset');
        $this->request->setMethod("post");
        $this->request->getHeaders()->addHeaderLine("content-type: application/json");
        $this->request->setContent(Json::encode(
            ['email' => 'test@test.hu']));


        //when
        $result = $this->controller->dispatch($this->request);

        //then
        $response = $this->controller->getResponse();
        $model = $this->event->getResult()->getVariables();


        //shoud be ok
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains("belépőkód", $model['message']);
        $this->assertTrue($model['success']);

        $this->em->createQueryBuilder();

        //we have the token in the db
        $q = $this->em->createQueryBuilder()->select("p")->from('\Radio\Entity\ChangePasswordToken', "p")->where('p.user = :user')->getQuery();
        $q->setParameter("user", $this->em->find("\Radio\Entity\User", 1));
        $result = $q->getArrayResult();
        $this->assertEquals(1, count($result));
        $token = $result[0]['token'];

        //phase2, try to change the password with wrong token
        $this->request->setContent(Json::encode(
            ['email' => 'test@test.hu',
                'token' => 'othertoken',
                'password' => 'newpassword']));
        $result = $this->controller->dispatch($this->request);

        $response = $this->controller->getResponse();
        $model = $this->event->getResult()->getVariables();
        $this->assertEquals(400, $response->getStatusCode());
        //var_dump($model);


        //phase2 try to change the passwork with good token
        $this->request->setContent(Json::encode(
            ['email' => 'test@test.hu',
                'token' => $token,
                'password' => 'newpassword']));
        $result = $this->controller->dispatch($this->request);

        $response = $this->controller->getResponse();
        $model = $this->event->getResult()->getVariables();
        //var_dump($model);
        $this->assertEquals(200, $response->getStatusCode());


        $user = $this->em->find("\Radio\Entity\User", 1);
        $this->assertTrue(User::testPassword($user, 'newpassword'));


    }

    public function testPasswordResetNoUser() {
        //given
        $this->routeMatch->setParam('action', 'passwordReset');
        $this->request->setMethod("post");
        $this->request->getHeaders()->addHeaderLine("content-type: application/json");
        $this->request->setContent(Json::encode(
            ['email' => 'test@test.com']));


        //when
        $result = $this->controller->dispatch($this->request);

        //then
        $response = $this->controller->getResponse();
        $model = $this->event->getResult()->getVariables();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains("nem ismerem", $model['error']);
    }


    public function testPasswordResetNoUserButAuthor() {
        //given
        $this->routeMatch->setParam('action', 'passwordReset');
        $this->request->setMethod("post");
        $this->request->getHeaders()->addHeaderLine("content-type: application/json");
        $this->request->setContent(Json::encode(
            ['email' => 'author2@tilos.hu']));


        //when
        $result = $this->controller->dispatch($this->request);

        //then
        $response = $this->controller->getResponse();
        $model = $this->event->getResult()->getVariables();

        $this->assertEquals(200, $response->getStatusCode());

        $author = $this->em->find("\Radio\Entity\Author",301);
        $this->assertNotNull($author->getUser());
    }

} 