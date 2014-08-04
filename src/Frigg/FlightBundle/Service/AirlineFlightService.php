<?php

namespace Frigg\FlightBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AirlineFlightService extends AbstractFlightService
{
    /**
     * Airline subclass constructor
     * @var EntityManager $em
     * @var string $config
     **/
    public function __construct(EntityManager $em, SessionInterface $session, $config)
    {
        parent::__construct($em, $session, $config);
    }

   /**
     * Get session entity from session
     * @return object
     **/
    public function getSessionEntity()
    {
        $entity = $this->em->getRepository('FriggFlightBundle:Airline')->find($this->sessionValue());

        if (!$entity) {
           throw new NotFoundHttpException('Unable to find airline entity');
        }

        $this->setParent($entity);
        return $entity;
    }

    /**
     * Set airline entity
     * @var integer $parentId
     * @return AirlineFlightService
     **/
    public function setParentById($parentId)
    {
        $entity = $this->em->getRepository('FriggFlightBundle:Airline')->find($parentId);

        if (!$entity) {
            throw new NotFoundHttpException('Unable to find airline entity');
        }

        $this->setParent($entity);
        return $this;
    }

    /**
     * Set new flight linked with this airline
     * @var integer $parentId
     * @var integer $flightId
     * @return AirlineFlightService
     **/
    public function setEntityById($parentId, $flightId)
    {
        $entity = $this->em->getRepository('FriggFlightBundle:Flight')->findOneBy(
            array(
                'id' => $flightId,
                'airline' => $parentId,
            )
        );

        if (!$entity) {
            throw new NotFoundHttpException('Unable to find flight entity');
        }

        $this->setEntity($entity);
        return $this;
    }

     /**
     * Fetch scheduled flights group for this airline
     * @return array
     **/
    public function loadGroup()
    {
        if (!$airline = $this->getParent()) {
            throw new NotFoundHttpException('Unable to load flights, missing airline in context.');
        }

        $direction = ($this->getFilter('direction') == 'A' ? 'A' : 'D');
        $isDelayed = ($this->getFilter('is_delayed') == 'Y');
        $fromTime = ($this->getFilter('from_time')) ? $this->getFilter('from_time') : mktime(0, 0, 0);
        $toTime = ($this->getFilter('to_time')) ? $this->getFilter('to_time') : mktime(23, 59, 59);

        $qb = $this->em->createQueryBuilder();
        $query = $qb->select('f')
            ->from('FriggFlightBundle:Flight', 'f')
            ->where($qb->expr()->andX(
                $qb->expr()->lte('f.schedule_time', ':to_time'),
                $qb->expr()->orX(
                    $qb->expr()->gte('f.schedule_time', ':from_time'),
                    $qb->expr()->andX(
                        $qb->expr()->isNotNull('f.flight_status_time'),
                        $qb->expr()->gte('f.flight_status_time', ':status_from_time'),
                        $qb->expr()->eq('f.flight_status', ':status_newtime_id')
                    )
                )
            ))
            ->andWhere('f.airline = :airline');

        if ($direction) {
            $query->andWhere('f.arr_dep = :direction');
            $query->setParameter('direction', $direction);
        }

        if ($isDelayed) {
            $query->andWhere('f.is_delayed = :is_delayed');
            $query->setParameter('is_delayed', $isDelayed);
        }

        $query->orderBy('f.schedule_time', 'ASC')
            ->setParameter('airline', $airline->getId())
            ->setParameter('from_time', new \DateTime(date('Y-m-d H:i:s', $fromTime)), \Doctrine\DBAL\Types\Type::DATETIME)
            ->setParameter('to_time', new \DateTime(date('Y-m-d H:i:s', $toTime)), \Doctrine\DBAL\Types\Type::DATETIME)
            ->setParameter('status_from_time', new \DateTime(date('Y-m-d H:i:s', ($fromTime - (24 * (60 * 60))))), \Doctrine\DBAL\Types\Type::DATETIME)
            ->setParameter('status_newtime_id', 2);

        $result = $query->getQuery()->getResult();
        $this->setGroup($result);
        return $this;

    }
}
