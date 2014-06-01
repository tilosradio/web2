<?php

namespace RadioAdmin\Controller;

use Radio\Mapper\ChildCollection;
use Radio\Mapper\Field;
use Radio\Mapper\ObjectFieldSetter;
use Radio\Mapper\ObjectMapper;
use Radio\Mapper\Tag;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Radio\Provider\EntityManager;
use Radio\Mapper\MapperFactory;

class Text extends \Radio\Controller\BaseController
{

    use EntityManager;


    public function update($e)
    {
        try {
            $id = $this->params()->fromRoute("id");
            $data = $this->getRawData($e);

            $text = $this->getEntityManager()->find("\Radio\Entity\TextContent", $id);

            if ($text == null) {
                $this->getResponse()->setStatusCode(404);
                return new JsonModel(array("error" => "Not found"));
            }



            $mapper = new ObjectMapper(new ObjectFieldSetter());
            $mapper->addMapper(Field::of("title")->required());
            $mapper->addMapper(Field::of("content")->required());
            $mapper->addMapper(Field::of("alias")->required());
            $mapper->addMapper(new Tag("content", $this->getEntityManager()));

            $mt = $mapper->addMapper(new ChildCollection('tags', "\Radio\Entity\Tag"));
            $mt->addMapper(Field::of("name"));


            $mapper->map($data, $text);
            $text->setModified(new \DateTime());

            $this->getEntityManager()->flush();
            return new JsonModel(array("success" => true));
        } catch (\Exception $ex) {
            $this->getResponse()->setStatusCode(500);
            return new JsonModel(array("error" => $ex->getMessage()));
        }
    }

    public function create($e)
    {
        try {
            $data = $this->getRawData($e);

            $mapper = new ObjectMapper(new ObjectFieldSetter($this->getEntityManager()));
            $mapper->addMapper(Field::of("title")->required());
            $mapper->addMapper(Field::of("content")->required());
            $mapper->addMapper(Field::of("alias")->required());
            $mapper->addMapper(new Tag("content", $this->getEntityManager()));

            $mt = $mapper->addMapper(new ChildCollection('tags', "\Radio\Entity\Tag"));
            $mt->addMapper(Field::of("name"));

            $text = new \Radio\Entity\TextContent();
            $text->setType('page');
            $text->setFormat('normal');
            $text->setCreated(new \DateTime());
            $text->setModified(new \DateTime());
            $text->setAuthor($this->getCurrentUser()->getUsername());
            $mapper->map($data, $text);

            $this->getEntityManager()->persist($text);
            $this->getEntityManager()->flush();
            return new JsonModel(array("success" => true, "data" => array("id" => $text->getId())));
        } catch (\Exception $ex) {
            $this->getResponse()->setStatusCode(500);
            return new JsonModel(array("error" => $ex->getMessage()));
        }
    }


}
