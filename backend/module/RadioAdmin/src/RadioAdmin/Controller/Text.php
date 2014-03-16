<?php

namespace RadioAdmin\Controller;

use Radio\Mapper\Field;
use Radio\Mapper\ObjectFieldSetter;
use Radio\Mapper\ObjectMapper;
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
            $f = new Field("title");
            $mapper->addMapper(Field::of("content")->required());
            $mapper->addMapper(Field::of("alias")->required());

            $mapper->map($data, $text);
            $text->setModified(new \DateTime());

            $this->getEntityManager()->flush();
            return new JsonModel(array("success" => true));
        } catch (\Exception $ex) {
            $this->getResponse()->setStatusCode(500);
            return new JsonModel(array("error" => $ex->getMessage()));
        }
    }


}
