<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Laurent\BulletinBundle\Manager;

use JMS\DiExtraBundle\Annotation as DI;
use Claroline\CoreBundle\Persistence\ObjectManager;
use Claroline\CoreBundle\Entity\User;
use Laurent\BulletinBundle\Entity\Periode;
use Laurent\SchoolBundle\Entity\Matiere;

/**
 * @DI\Service("laurent.manager.totaux_manager")
 */
class TotauxManager
{
    private $om;
    private $pempRepo;
    private $pemdRepo;
    private $periodeRepo;

    /**
     * @DI\InjectParams({
     *     "om" = @DI\Inject("claroline.persistence.object_manager")
     * })
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->pempRepo           = $om->getRepository('LaurentBulletinBundle:PeriodeEleveMatierePoint');
        $this->pemdRepo           = $om->getRepository('LaurentBulletinBundle:PeriodeElevePointDiversPoint');
        $this->periodeRepo        = $om->getRepository('LaurentBulletinBundle:Periode');
    }

    public function getTotalPeriode(Periode $periode, User $eleve) {

        $pemps = $this->pempRepo->findPeriodeEleveMatiere($eleve, $periode);
        $totalPoint = 0;
        $totalTotal = 0;

        foreach ($pemps as $pemp) {
            if ($pemp->getPoint() < 850){
                $totalPoint += $pemp->getPoint();
                $totalTotal += $pemp->getTotal();
            }
        }

        $totalPourcentage = round(($totalPoint / $totalTotal) * 100, 1).' %';

        return array('totalPoint' => $totalPoint, 'totalTotal' => $totalTotal, 'totalPourcentage' => $totalPourcentage);
    }

    public function getTotalPeriodes(User $eleve){
        $periodes = array(1, 2, 3);
        $totaux = array();
        $nbPeriodes = array();


        $periode = $this->periodeRepo->findOneById(3);
        $pemps = $this->pempRepo->findPeriodeEleveMatiere($eleve, $periode);

        foreach ($pemps as $key => $pemp){
            $totaux[$key] = 0;
            $nbPeriodes[$key] = 0;

        }


        foreach ($periodes as $per){
            $periode = $this->periodeRepo->findOneById($per);
            $pemps = $this->pempRepo->findPeriodeEleveMatiere($eleve, $periode);

            foreach ($pemps as $key => $pemp){
                if ($pemp->getPourcentage() != 999){
                    $totaux[$key] += $pemp->getPourcentage();
                    $nbPeriodes[$key]++;
                }
            }
        }

        foreach ($totaux as $key => $total) {
            $totaux[$key] = round($total / $nbPeriodes[$key], 1);
        }

        return $totaux;
    }

    public function getDataChart(User $eleve)
    {
        $periodes = array(1, 2, 3);

        $data = new \StdClass();
        $data->labels = array('Période 1', 'Période 2', 'Examen');
        $data->datasets = array();

        //créons les matières avec les moyens du bord !
        $periode = $this->periodeRepo->findOneById(1);
        $matieres = array();
        $pemps = $this->pempRepo->findPeriodeEleveMatiere($eleve, $periode);

        foreach ($pemps as $pemp) {
            $object = new \StdClass();
            $object->label = $pemp->getMatiere()->getName();
            $object->fillColor = $pemp->getMatiere()->getColor();
            $object->pointColor = $pemp->getMatiere()->getColor();
            $object->pointStrokeColor = $pemp->getMatiere()->getColor();
            $object->pointHighlightFill = $pemp->getMatiere()->getColor();
            $object->pointHighlightStroke = $pemp->getMatiere()->getColor();
            $object->data = $this->getPourcentageMatierePeriode($pemp->getMatiere(), $eleve);

            $data->datasets[] = $object;
        }

        return json_encode($data);
    }

    private function getPourcentageMatierePeriode(Matiere $matiere, User $eleve) {

        $periodes = array(1, 2, 3);
        $pourcPeriode = array();

        foreach ($periodes as $per){
            $periode = $this->periodeRepo->findOneById($per);
            if ($this->pempRepo->findPeriodeMatiereEleve($periode, $eleve, $matiere)->getPourcentage() == 999){
                $pourcPeriode[] = '';
            } else{
                $pourcPeriode[] = round($this->pempRepo->findPeriodeMatiereEleve($periode, $eleve, $matiere)->getPourcentage(), 1);
            }

        }
        return $pourcPeriode;
    }
}