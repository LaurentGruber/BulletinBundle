<?php

namespace Laurent\BulletinBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Laurent\BulletinBundle\Entity\Periode;

class PeriodeEleveDecisionRepository extends EntityRepository
{
    public function findDecisionsByUsersAndPeriode(array $users, Periode $periode)
    {
        $results = array();

        if (count($users) > 0) {
            $dql = '
                SELECT ped
                FROM Laurent\BulletinBundle\Entity\PeriodeEleveDecision ped
                WHERE ped.periode = :periode
                AND ped.user IN (:users)
            ';
            $query = $this->_em->createQuery($dql);
            $query->setParameter('periode', $periode);
            $query->setParameter('users', $users);

            $results = $query->getResult();
        }

        return $results;
    }
}
