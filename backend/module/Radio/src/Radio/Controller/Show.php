<?php

namespace Radio\Controller;

use Radio\Mapper\ArrayFieldSetter;
use Radio\Mapper\MapperFactory;
use Zend\Db\Sql\Expression;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Radio\Provider\EntityManager;

/**
 * @SWG\Resource(resourcePath="/show",basePath="/api")
 */
class Show extends BaseController
{

    use EntityManager;

    public function findEntityObject($type, $id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('a', 'sa', 's', 'u', 'c')->from('\Radio\Entity\Show', 's');
        if (is_numeric($id)) {
            $qb->where('s.id = :id');
        } else {
            $qb->where('s.alias = :id');
        }
        $qb->leftJoin('s.contributors', 'sa')->leftJoin('sa.author', 'a');
        $qb->leftJoin('s.urls', 'u');
        $qb->leftJoin('s.schedulings', 'c', "WITH", "c.validFrom < :now and c.validTo > :now");
        $qb->orderBy("c.weekDay", "ASC");


        $q = $qb->getQuery();
        $q->setParameter("id", $id);
        $q->setParameter("now", new \DateTime());
        return $q->getArrayResult()[0];
    }

    public function mapEntity($result)
    {
        $a = $result;
        $now = time();
        $episodes = EpisodeUtil::getEpisodeTimes($this->getEntityManager(), $now - 60 * 60 * 24 * 60, $now, $a['id'], true);
        $a['episodes'] = $episodes;
        $r = [];
        MapperFactory::showMapper(['baseUrl' => $this->getServerUrl()])->map($a, $r);
        return $r;

    }

    public function mapEntityListElement($result)
    {
        $r = [];
        MapperFactory::showElementMapper(['baseUrl' => $this->getServerUrl()])->map($result, $r);
        return $r;
    }

    public function findEntityList($type)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('s')->from('\Radio\Entity\Show', 's')->where("s.status = 1")->orderBy("s.name", "ASC");
        $q = $qb->getQuery();
        return $q->getArrayResult();
    }

    /**
     * @SWG\Api(
     *   path="/show",
     *   description="Generic information about radio show",
     * @SWG\Operation(
     *     method="GET",
     *     summary="List all active radioshow"
     *   )
     * )
     */
    public function getList()
    {
        return $this->getEntityList("\Radio\Entity\Show");
    }

    public function get($e)
    {
        $id = $this->getIdentifier($e->getRouteMatch(),$e->getRequest());
        return $this->getEntity("\Radio\Entity\Show", $id);
    }

    public function listOfEpisodes()
    {
        $id = $this->params()->fromRoute("id");
        $start = $this->params()->fromQuery("from", time() - 60 * 24 * 60 * 60);
        $end = $this->params()->fromQuery("to", time());
        $episodes = EpisodeUtil::getEpisodeTimes($this->getEntityManager(), $start, $end, $id, true);

        $result = [];
        $mapper = MapperFactory::shortEpisodeElementMapper(['baseUrl' => $this->getServerUrl()]);
        $mapper->map($episodes, $result, new ArrayFieldSetter());

        return new JsonModel($result);
    }

    public function getEpisodeByAlias(){
        $alias = $this->params()->fromRoute("show");

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('s')->from('\Radio\Entity\Show', 's');
        $qb->where('s.alias = :alias');
        $q = $qb->getQuery();
        $q->setParameter("alias", $alias);
        $res = $q->getArrayResult();
        if (!$res) {
            $this->getResponse()->setStatusCode(404);
            return new JsonModel(array("error" => "Show is missing"));
        } else {
            $res = $res[0];
        }
        $id = $res['id'];

        $month = $end = $this->params()->fromRoute("month");
        $day = $this->params()->fromRoute("day");
        $year = $this->params()->fromRoute("year");
        $start = mktime(0,0,0,$month,$day,$year);
        $end = mktime(23,23,59,$month,$day,$year);

        $episodes = EpisodeUtil::getEpisodeTimes($this->getEntityManager(), $start, $end, $id, true);

        $result = [];
        $mapper = MapperFactory::shortEpisodeElementMapper(['baseUrl' => $this->getServerUrl()]);
        $mapper->map($episodes, $result, new ArrayFieldSetter());
        if ($result) {
            $result = $result[0];
        }
        return new JsonModel($result);
    }




}

