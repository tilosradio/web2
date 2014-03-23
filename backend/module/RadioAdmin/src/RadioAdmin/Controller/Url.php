<?php

namespace RadioAdmin\Controller;

use Radio\Controller\BaseController;
use Radio\Mapper\ArrayFieldSetter;
use Radio\Mapper\DateField;
use Radio\Mapper\Field;
use Radio\Mapper\ListMapper;
use Radio\Mapper\ObjectFieldSetter;
use Radio\Mapper\ObjectMapper;
use Radio\Mapper\TimestampField;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Radio\Provider\EntityManager;
use Radio\Mapper\MapperFactory;


class Url extends BaseController
{

    use EntityManager;

    public function update($e)
    {

        $id = $this->params()->fromRoute("id");
        $data = $this->getRawData($e);

        $scheduling = $this->getEntityManager()->find("\Radio\Entity\Scheduling", $id);
        $mapper = new ObjectMapper(new ObjectFieldSetter());
        $mapper->addMapper(new Field("weekDay"));
        $mapper->addMapper(new Field("weekType"));
        $mapper->addMapper(new Field("hourFrom"));
        $mapper->addMapper(new Field("minFrom"));
        $mapper->addMapper(new Field("duration"));
        $mapper->addMapper(new TimestampField("base"));
        $mapper->addMapper(new TimestampField("validFrom"));
        $mapper->addMapper(new TimestampField("validTo"));
        $mapper->map($data, $scheduling);
        $this->getEntityManager()->flush();
        return new JsonModel(array("success" => true));
    }


    public function delete($e)
    {

        $id = $this->params()->fromRoute("id");

        $showId = $this->params()->fromQuery("showId");
        $show = $this->getEntityManager()->find("\Radio\Entity\Show", $showId);
        if (empty($show)) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(array("error" => "Show is missing."));
        }
        $urlToDelete = -1;

        for ($i = 0; $i < count($show->getUrls()); $i++) {
            $url = $show->getUrls()[$i];
            if ($url->getId() == $id) {
                $urlToDelete = $i;
            }
        }

        if ($urlToDelete == -1) {
            $this->getResponse()->setStatusCode(500);
            return new JsonModel(array("error" => "Url is not related to the specific show"));
        }

        $show->getUrls()->remove($urlToDelete);
        $this->getEntityManager()->remove($url);
        $this->getEntityManager()->flush();



        return new JsonModel(array("success" => true));
    }

    public function create($e)
    {

        $data = $this->getRawData($e);
        $showId = $data['showId'];
        $show = $this->getEntityManager()->find("\Radio\Entity\Show", $showId);
        if (empty($show)) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(array("error" => "Show is missing."));
        }

        $url = new \Radio\Entity\Url();

        $mapper = new ObjectMapper(new ObjectFieldSetter());
        $mapper->addMapper(new Field("url"));
        $mapper->map($data, $url);

        $this->getEntityManager()->persist($url);
        $this->getEntityManager()->flush();

        $show->getUrls()->add($url);
        $this->getEntityManager()->persist($show);
        $this->getEntityManager()->flush();

        return new JsonModel(array("success" => true, "data" => array("id" => $url->getId())));
    }


}
