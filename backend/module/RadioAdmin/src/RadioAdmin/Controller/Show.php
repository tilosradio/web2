<?php

namespace RadioAdmin\Controller;

use Radio\Controller\BaseController;
use Radio\Mapper\ArrayFieldSetter;
use Radio\Mapper\Field;
use Radio\Mapper\MapperFactory;
use Radio\Mapper\ObjectFieldSetter;
use Radio\Mapper\ObjectMapper;
use Zend\Db\Sql\Expression;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Radio\Provider\EntityManager;


class Show extends BaseController
{

    use EntityManager;


    public function update($e)
    {

        $id = $this->params()->fromRoute("id");
        $data = $this->getRawData($e);

        $show = $this->getEntityManager()->find("\Radio\Entity\Show", $id);

        $mapper = new ObjectMapper(new ObjectFieldSetter());
        $mapper->addMapper(new Field("name"));
        $mapper->addMapper(new Field("description"));
        $mapper->addMapper(new Field("definition"));

        if ($this->isAdmin()) {
          $mapper->addMapper(new Field("status"));
          $mapper->addMapper(new Field("type"));
          $mapper->addMapper(Field::of("alias")->required());

        }

        $mapper->map($data, $show);

        $this->getEntityManager()->persist($show);
        $this->getEntityManager()->flush();
        return new JsonModel(array("success" => true));

    }


    public function create($e)
    {
        try {
            $data = $this->getRawData($e);

            $mapper = new ObjectMapper(new ObjectFieldSetter());
            $mapper->addMapper(new Field("name"));
            $mapper->addMapper(new Field("description"));
            $mapper->addMapper(new Field("definition"));
            if ($this->isAdmin()) {
                $mapper->addMapper(new Field("status"));
                $mapper->addMapper(new Field("type"));
                $mapper->addMapper(Field::of("alias")->required());
            }

            $show = new \Radio\Entity\Show();
            $show->setType(0);
            $show->setStatus(0);
            $mapper->map($data, $show);

            $this->getEntityManager()->persist($show);
            $this->getEntityManager()->flush();
            return new JsonModel(array("success" => true, "data" => array("id" => $show->getId())));
        } catch (\Exception $ex) {
            $this->getResponse()->setStatusCode(500);
            return new JsonModel(array("error" => $ex->getMessage()));
        }
    }

}

