<?php

namespace Radio\Controller;

use DoctrineORMModule\Proxy\__CG__\Radio\Entity\TextContent;
use Radio\Mapper\ArrayFieldSetter;
use Radio\Mapper\ChildObject;
use Radio\Mapper\DateField;
use Radio\Mapper\EpisodeURLField;
use Radio\Mapper\Field;
use Radio\Mapper\InternalLinkField;
use Radio\Mapper\ListMapper;
use Radio\Mapper\ObjectMapper;
use Zend\View\Model\JsonModel;
use Radio\Provider\EntityManager;
use Radio\Mapper\MapperFactory;


/**
 * @SWG\Resource(resourcePath="/episode",basePath="/api")
 */
class Episode extends BaseController
{

    use EntityManager;


    public function getList()
    {
        try {
            $start = $this->params()->fromQuery("start", time());
            $end = $this->params()->fromQuery("end", $start + 60 * 60 * 5);
            $episodes = EpisodeUtil::getEpisodeTimes($this->getEntityManager(), $start, $end);
            $result = [];
            MapperFactory::episodeElementMapper(['baseUrl' => $this->getServerUrl()])->map($episodes, $result, new ArrayFieldSetter());

            return new JsonModel($result);
        } catch (Exception $ex) {
            $this->getResponse()->setStatusCode(500);
            return new JsonModel(array("error" => $ex->getMessage()));
        }
    }


    public function next()
    {
        try {
            $start = time();
            $end = $this->params()->fromQuery("end", $start + 60 * 60 * 5);

            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->select('e', 't', 's')
                ->from('\Radio\Entity\Episode', 'e')
                ->join('e.text', 't')
                ->join('e.show', 's')
                ->where('e.realTo > current_timestamp()')
                ->add('orderBy', 'e.realFrom ASC')
                ->setFirstResult(0)
                ->setMaxResults(5);

            $q = $qb->getQuery();


            $episodes = $q->getArrayResult();
            $result = [];

            $this->episodeSuggestionMapper()->map($episodes, $result, new ArrayFieldSetter());

            return new JsonModel($result);
        } catch (Exception $ex) {
            $this->getResponse()->setStatusCode(500);
            return new JsonModel(array("error" => $ex->getMessage()));
        }
    }

    public function episodeSuggestionMapper()
    {
        $m = new ListMapper();
        $this->simpleEpisodeElementMapper($m);
        $em = $m->addMapper(new ChildObject("text"));
        $em->addMapper(new Field("title"));
        $em->addMapper(new DateField("created"));
        $m->addMapper(new EpisodeURLField());
        return $m;
    }

    public function episodeMapper()
    {
        $m = new ObjectMapper();
        $this->simpleEpisodeElementMapper($m);
        $em = $m->addMapper(new ChildObject("text"));
        $em->addMapper(new Field("title"));
        $em->addMapper(new \Radio\Mapper\TextContent());
        $em->addMapper(new DateField("created"));
        return $m;
    }

    public function simpleEpisodeElementMapper(&$m)
    {
        $m->addMapper(new Field("id"));
        $m->addMapper(new DateField("plannedFrom"));
        $m->addMapper(new DateField("plannedTo"));
        $m->addMapper(new InternalLinkField("m3uUrl", $this->getServerUrl()));

        $sm = $m->addMapper(new ChildObject("show"));
        $sm->addMapper(new Field("name"));
        $m->addMapper(new InternalLinkField("m3uUrl", $this->getServerUrl()));
        $sm->addMapper(new Field("id"));
        $sm->addMapper(new Field("alias"));

    }

    public function last()
    {
        try {
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->select('e', 't', 's')
                ->from('\Radio\Entity\Episode', 'e')
                ->join('e.text', 't')
                ->join('e.show', 's')
                ->where('e.realTo < current_timestamp()')
                ->add('orderBy', 't.created DESC')
                ->setFirstResult(0)
                ->setMaxResults(5);

            $q = $qb->getQuery();


            $episodes = $q->getArrayResult();
            $result = [];
            foreach ($episodes as &$episode) {
                $episode['m3uUrl'] = EpisodeUtil::m3uUrlLink($episode);
            }
            $this->episodeSuggestionMapper()->map($episodes, $result, new ArrayFieldSetter());

            return new JsonModel($result);
        } catch (Exception $ex) {
            $this->getResponse()->setStatusCode(500);
            return new JsonModel(array("error" => $ex->getMessage()));
        }
    }

    public function get($e)
    {
        try {
            $id = $this->getIdentifier($e->getRouteMatch(), $e->getRequest());

            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->select('e', 't', 's')
                ->from('\Radio\Entity\Episode', 'e')
                ->leftJoin('e.text', 't')
                ->join('e.show', 's')
                ->where('e.id = :id');

            $q = $qb->getQuery();
            $q->setParameter("id", $id);
            $result = $q->getArrayResult();

            if ($result == null) {
                $this->getResponse()->setStatusCode(404);
                return new JsonModel(array("error" => "Not found"));
            }

            $a = [];
            $mapper = $this->episodeMapper();
            $mapper->map($result[0], $a);
            return new JsonModel($a);
        } catch (Exception $ex) {
            $this->getResponse()->setStatusCode(500);
            return new JsonModel(array("error" => $ex->getMessage()));
        }
    }

}
