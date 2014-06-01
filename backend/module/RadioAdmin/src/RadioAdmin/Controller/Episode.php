<?php

namespace RadioAdmin\Controller;

use DoctrineORMModule\Proxy\__CG__\Radio\Entity\TextContent;
use Radio\Mapper\ArrayFieldSetter;
use Radio\Mapper\ChildCollection;
use Radio\Mapper\ChildObject;
use Radio\Mapper\DateField;
use Radio\Mapper\Field;
use Radio\Mapper\ObjectFieldSetter;
use Radio\Mapper\ObjectMapper;
use Radio\Mapper\StaticField;
use Radio\Mapper\Tag;
use Radio\Mapper\TimestampField;
use Zend\View\Model\JsonModel;
use Radio\Provider\EntityManager;
use Radio\Mapper\MapperFactory;


/**
 * @SWG\Resource(resourcePath="/episode",basePath="/api")
 */
class Episode extends \Radio\Controller\BaseController
{

    use EntityManager;

    public function create($e)
    {
        try {
            $data = $this->getRawData($e);


            $mapper = new ObjectMapper(new ObjectFieldSetter($this->getEntityManager()));
            $mapper->addMapper(new ChildObject("show", "\Radio\Entity\Show"));
            $tm = $mapper->addMapper(new ChildObject("text", "\Radio\Entity\TextContent"));
            $tm->addMapper(new Field("title"));
            $tm->addMapper(new Field("content"));
            $tm->addMapper(StaticField::of("type", "episode"));
            $tm->addMapper(StaticField::of("format", "normal"));
            $tm->addMapper(StaticField::of("created", new \DateTime()));
            $tm->addMapper(StaticField::of("modified", new \DateTime()));
            $tm->addMapper(StaticField::of("author", $this->getCurrentUser()->getUserName()));
            $tm->addMapper(StaticField::of("alias", ''));

            $mapper->addMapper(new TimestampField("plannedFrom"));
            $mapper->addMapper(new TimestampField("plannedTo"));
            $mapper->addMapper(new TimestampField("realFrom"));
            $mapper->addMapper(new TimestampField("realTo"));

            $tm->addMapper(new Tag("content", $this->getEntityManager()));

            $mt = $tm->addMapper(new ChildCollection('tags', "\Radio\Entity\Tag"));
            $mt->addMapper(Field::of("name"));

            $episode = new \Radio\Entity\Episode();
            $mapper->map($data, $episode);
            if (!$episode->getRealFrom()) {
                $episode->setRealFrom($episode->getPlannedFrom());
            }
            if (!$episode->getRealTo()) {
                $episode->setRealTo($episode->getPlannedTo());
            }
            //var_dump($episode);

            $this->getEntityManager()->persist($episode);
            $this->getEntityManager()->flush();
            return new JsonModel(array("success" => true, "data" => array("id" => $episode->getId())));
        } catch (\Exception $ex) {
            $this->getResponse()->setStatusCode(500);
            return new JsonModel(array("error" => $ex->getMessage()));
        }
    }

    public function update($e)
    {

        $id = $this->params()->fromRoute("id");
        $data = $this->getRawData($e);

        $episode = $this->getEntityManager()->find('Radio\Entity\Episode', $id);

        if (is_null($episode)) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(array("error" => "Episode does not exist."));
        }

        $mapper = new ObjectMapper(new ObjectFieldSetter($this->getEntityManager()));
        $mapper->addMapper(new TimestampField("realFrom"));
        $mapper->addMapper(new TimestampField("realTo"));

        $tm = $mapper->addMapper(new ChildObject("text", "\Radio\Entity\TextContent"));
        $mapper->addMapper(new TimestampField("realFrom"));
        $mapper->addMapper(new TimestampField("realTo"));
        $tm->addMapper(new Field("title"));
        $tm->addMapper(new Field("content"));
        $tm->addMapper(StaticField::of("type", "episode"));
        $tm->addMapper(StaticField::of("format", "normal"));
        $tm->addMapper(StaticField::of("modified", new \DateTime()));
        $tm->addMapper(StaticField::of("author", $this->getCurrentUser()->getUserName()));
        $tm->addMapper(StaticField::of("alias", ''));

        $tm->addMapper(new Tag("content", $this->getEntityManager()));

        $mt = $tm->addMapper(new ChildCollection('tags', "\Radio\Entity\Tag"));
        $mt->addMapper(Field::of("name"));

        $mapper->map($data, $episode);

        if ($episode->getText() && !$episode->getText()->getCreated()) {
            $episode->getText()->setCreated(new \DateTime());
        }


        $this->getEntityManager()->flush();
        return new JsonModel(array("success" => true, 'data' => array('id' => $episode->getId())));
    }

    public function delete($id)
    {
        try {
            $episode = $this->getEntityManager()->find('Radio\Entity\Episode', $id);
            if (is_null($episode)) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel(array("error" => "Episode does not exist in DB."));
            }

            $this->getEntityManager()->remove($episode);
            $this->getEntityManager()->flush();

            return new JsonModel(array("delete" => "success"));
        } catch (\Exception $ex) {
            $this->getResponse()->setStatusCode(500);
            return new JsonModel(array("error" => $ex->getMessage()));
        }
    }

}
