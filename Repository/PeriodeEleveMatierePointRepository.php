<?php

namespace Laurent\BulletinBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Claroline\CoreBundle\Entity\User;
use Laurent\BulletinBundle\Entity\Periode;
use Laurent\BulletinBundle\Entity\PeriodeEleveMatierePoint;

class PeriodeEleveMatierePointRepository extends EntityRepository
{
    public function findPeriodeEleveMatiere(User $user, Periode $periode)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('pemp')
            ->from('Laurent\BulletinBundle\Entity\PeriodeEleveMatierePoint', 'pemp')
            ->where('pemp.periode = :periode')
            ->andWhere('pemp.eleve = :user')
            ->orderBy('pemp.position')
            ->setParameter('periode', $periode)
            ->setParameter('user', $user);
        $query = $qb->getQuery();
        return $results = $query->getResult();
    }
}