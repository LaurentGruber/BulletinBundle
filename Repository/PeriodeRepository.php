<?php

namespace Laurent\BulletinBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PeriodeRepository extends EntityRepository
{
    public function findNonOnlyPointPeriodes()
    {
        $dql = '
            SELECT p
            FROM Laurent\BulletinBundle\Entity\Periode p
            WHERE p.onlyPoint IS NULL
            OR p.onlyPoint = false
        ';
        $query = $this->_em->createQuery($dql);

        return $query->getResult();
    }
}
