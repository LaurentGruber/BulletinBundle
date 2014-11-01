<?php

namespace Laurent\BulletinBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Claroline\CoreBundle\Entity\User;
use Laurent\BulletinBundle\Entity\Periode;

class PeriodeElevePointDiversPointRepository extends EntityRepository
{
    public function findPeriodeElevePointDivers(User $user, Periode $periode)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('pemp')
            ->from('Laurent\BulletinBundle\Entity\PeriodeElevePointDiversPoint', 'pemp')
            ->where('pemp.periode = :periode')
            ->andWhere('pemp.eleve = :user')
            ->orderBy('pemp.position')
            ->setParameter('periode', $periode)
            ->setParameter('user', $user);
        $query = $qb->getQuery();
        return $results = $query->getResult();
    }
}