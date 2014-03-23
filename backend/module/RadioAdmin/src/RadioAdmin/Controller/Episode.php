<?php

namespace RadioAdmin\Controller;

use DoctrineORMModule\Proxy\__CG__\Radio\Entity\TextContent;
use Radio\Mapper\ArrayFieldSetter;
use Radio\Mapper\ChildObject;
use Radio\Mapper\Field;
use Radio\Mapper\ObjectFieldSetter;
use Radio\Mapper\ObjectMapper;
use Radio\Mapper\StaticField;
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
            $id = $this->params()->fromRoute("id");
            $data = $this->getRawData($e);

            $authService = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
            // identify the user
            $user = $authService->hasIdentity() ? $authService->getIdentity() : null;

            // validation
            if (!isset($data['radioshow_id']) || !isset($data['plannedFrom']) ||
                !isset($data['plannedTo'])
            ) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel(array("error" => "Mandatory fields: radioshow_id, plannedFrom, plannedTo."));
            }
            // validate show id via DB
            $show = $this->getEntityManager()->find('Radio\Entity\Show', $data['radioshow_id']);
            if (is_null($show)) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel(array("error" => "Show id does not exist in DB."));
            }
            // validate textcontent id via DB
            if (isset($data['text']) && isset($data['text']['id'])) {
                $text = $this->getEntityManager()->find('Radio\Entity\TextContent', $data['text']['id']);
                if (is_null($text)) {
                    $this->getResponse()->setStatusCode(400);
                    return new JsonModel(array("error" => "Text id does not exist in DB."));
                }
            }
            // validate timestamps
            if (!is_numeric($data['plannedFrom']) || !is_numeric($data['plannedTo'])) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel(array("error" => "Waiting dates in timestamp format (integer)."));
            }
            // convert timestamps to datetimes
            $plannedFrom = new \DateTime();
            $plannedTo = new \DateTime();
            $realFrom = new \DateTime();
            $realTo = new \DateTime();

            $plannedFrom->setTimestamp($data['plannedFrom']);
            $plannedTo->setTimestamp($data['plannedTo']);
            if (array_key_exists('realFrom', $data)) {
                $realFrom->setTimestamp($data['realFrom']);
            } else {
                $realFrom = $plannedFrom;
            }
            if (array_key_exists('realTo', $data)) {
                $realTo->setTimestamp($data['realTo']);
            } else {
                $realTo = $plannedTo;
            }

            $episode = new \Radio\Entity\Episode();

            $episode->setShow($show);
            $episode->setPlannedFrom($plannedFrom);
            $episode->setPlannedTo($plannedTo);
            $episode->setRealFrom($realFrom);
            $episode->setRealTo($realTo);

            if (!array_key_exists('title', $data) || !array_key_exists('content', $data)) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel(array("error" => "Mandatory fields: title, content"));
            } else {
                $t = new \Radio\Entity\TextContent();
                $t->setTitle($data['title']);
                $t->setContent($data['content']);
                $t->setType("episode");
                $t->setModified(new \DateTime());
                $t->setCreated(new \DateTime());
                $t->setFormat("html");
                $t->setAuthor($user->getUsername());
                $t->setAlias('');
                $this->getEntityManager()->persist($t);
                $episode->setText($t);

            }


            $this->getEntityManager()->persist($episode);
            $this->getEntityManager()->flush();

            return new JsonModel(array("success" => true, 'data' => array('id' => $episode->getId())));
        } catch
        (\Exception $ex) {
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

        $data = array('text'=>$data);
        $mapper = new ObjectMapper(new ObjectFieldSetter());
        $tm = $mapper->addMapper(new ChildObject("text","\Radio\Entity\TextContent"));
        $tm->addMapper(new Field("title"));
        $tm->addMapper(new Field("content"));
        $tm->addMapper(StaticField::of("type","episode"));
        $tm->addMapper(StaticField::of("format","normal"));
        $tm->addMapper(StaticField::of("created",new \DateTime()));
        $tm->addMapper(StaticField::of("modified",new \DateTime()));
        $tm->addMapper(StaticField::of("author",$this->getCurrentUser()->getUserName()));
        $tm->addMapper(StaticField::of("alias",''));

        $mapper->map($data, $episode);


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
