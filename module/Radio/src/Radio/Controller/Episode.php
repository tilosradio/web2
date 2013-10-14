<?php

namespace Radio\Controller;

use Zend\View\Model\JsonModel;
use Radio\Provider\EntityManager;

class Episode extends BaseController {
    
    use EntityManager;
    
    public function getList() {
        try {
            // TODO: paging (limit/offset)
            $query = $this->getEntityManager()->createQuery('SELECT e FROM Radio\Entity\Scheduling e WHERE e.weekType = :type OR e.weekType = 0 ORDER BY e.weekDay,e.hourFrom,e.minFrom');
            $query->setParameter("type",date("W")%2 + 1);
            $resultSet = $query->getResult();
            if (empty($resultSet))
                return new JsonModel(array());

            $return = array();

            $weekstart = getdate(strtotime('this week', time()));



            foreach ($resultSet as $result) {
                $a = $result->toArray();
                $epi = array();
                //$epi->setShow($result->getShow());
                $epi['show'] = $result->getShow()->toArrayShort();

                $from = new \DateTime();
                $from->setDate($weekstart['year'], $weekstart['mon'], $weekstart['mday']);
                $from->setTime($result->getHourFrom(), $result->getMinFrom(), 0);
                $from->add(new \DateInterval("P" . $result->getWeekDay() . "D"));
                
                $to = new \DateTime();
                $to->setDate($weekstart['year'], $weekstart['mon'], $weekstart['mday']);
                $to->setTime($result->getHourTo(), $result->getMinTo(), 0);
                $to->add(new \DateInterval("P" . $result->getWeekDay() . "D"));

                $epi['from'] = $from->getTimestamp();
                $epi['to'] = $to->getTimestamp();
                $return[] = (array) $epi;
            }
            return new JsonModel($return);
        } catch (Exception $ex) {
            $this->getResponse()->setStatusCode(500);
            return new JsonModel(array("error" => $ex->getMessage()));
        }
    }

    public function get($id) {
        try {
            $result = $this->getEntityManager()->find("\Radio\Entity\Episode", $id);
            if ($result == null) {
                $this->getResponse()->setStatusCode(404);
                return new JsonModel(array("error" => "Not found"));
            } else {
                $a = $result->toArray();
                $a['shows'] = array();
                foreach ($result->getShows() as $show) {
                    $a['shows'][] = $show->getShow()->toArrayShort();
                }
                return new JsonModel($a);
            }
        } catch (\Exception $ex) {
            $this->getResponse()->setStatusCode(500);
            return new JsonModel(array("error" => $ex->getMessage()));
        }
    }

    public function create($data)
    {
        // TODO: implementation
    }
    
    public function update($id, $data)
    {
        // TODO: implementation
    }
    
    public function delete($id)
    {
        // TODO: implementation
    }
}
