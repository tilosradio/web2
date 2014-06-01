<?php

namespace Radio\Controller;


use Doctrine\ORM\Query\ResultSetMapping;
use Radio\Mapper\ArrayFieldSetter;
use Radio\Mapper\ChildCollection;
use Radio\Mapper\ChildObject;
use Radio\Mapper\DateField;
use Radio\Mapper\Field;
use Radio\Mapper\ListMapper;
use Radio\Mapper\ObjectMapper;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Radio\Provider\EntityManager;
use Radio\Mapper\MapperFactory;

class Tag extends BaseController
{

    use EntityManager;

    public function getList()
    {
        try {

            $rsm = new ResultSetMapping();

            $sql = "select tag.name,count(tt.textcontent_id) as count from tag left join tag_textcontent tt on tt.tag_id = tag.id group by tag.name having count > 0 order by count(tt.textcontent_id) desc";
            $q = $this->getEntityManager()->getConnection()->query($sql);
            $result = [];
            $tags = [];
            foreach ($q as $row) {
                $tags[] = $row;
            }
            $m = new ListMapper();
            $m->addMapper(new Field("name"));
            $m->addMapper(new Field("count"));

            $m->map($tags, $result, new ArrayFieldSetter());

            return new JsonModel($result);
        } catch (Exception $ex) {
            $this->getResponse()->setStatusCode(500);
            return new JsonModel(array("error" => $ex->getMessage()));
        }
    }

    public function get($e)
    {
        try {

            $name = $e->getRouteMatch()->getParam("name");

            if (!$name) {
                return getList();
            }
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->select('t')
                ->from('\Radio\Entity\Tag', 't')
                ->where("t.name = :name")
                ->setMaxResults(100);
            $q = $qb->getQuery();
            $q->setParameter("name", $name);

            $tag = $q->getArrayResult()[0];

            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->select('e', 'x', 't', 's')
                ->from('\Radio\Entity\Episode', 'e')
                ->join("e.text", 'x')
                ->join('x.tags', 't')
                ->join('e.show', 's')
                ->where("t.name = :name");
            $q = $qb->getQuery();
            $q->setParameter("name", $name);

            $episodes = $q->getArrayResult();


            //var_dump($episodes);
            $result = [];
            $m = new ObjectMapper();
            $m->addMapper(new Field("name"));
            $m->map($tag, $result, new ArrayFieldSetter());

            $epi = [];
            $em = new ListMapper();
            $em->addMapper(new Field("id"));
            $em->addMapper(new DateField("plannedFrom"));
            $em->addMapper(new DateField("plannedTo"));

            $sm = $em->addMapper(new ChildObject("show"));
            $sm->addMapper(new Field("id"));
            $sm->addMapper(new Field("name"));
            $sm->addMapper(new Field("alias"));

            $tm = $em->addMapper(new ChildObject("text"));
            $tm->addMapper(new Field("title"));

            $em->map($episodes, $epi, new ArrayFieldSetter());

            $result['episodes'] = $epi;

            return new JsonModel($result);
        } catch (Exception $ex) {
            $this->getResponse()->setStatusCode(500);
            return new JsonModel(array("error" => $ex->getMessage()));
        }
    }


}
