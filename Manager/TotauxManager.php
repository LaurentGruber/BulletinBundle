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

    public function getTotalPeriodesMatiere(User $eleve){
        $periodes = array(1, 2, 3);
        $totaux = array();
        $nbPeriodes = array();


        $periode = $this->periodeRepo->findOneById(3);
        $pemps = $this->pempRepo->findPeriodeEleveMatiere($eleve, $periode);

        foreach ($pemps as $key => $pemp){
            $totaux[$pemp->getMatiere()->getName()] = 0;
            $nbPeriodes[$pemp->getMatiere()->getName()] = 0;

        }


        foreach ($periodes as $per){
            $periode = $this->periodeRepo->findOneById($per);
            $pemps = $this->pempRepo->findPeriodeEleveMatiere($eleve, $periode);

            foreach ($pemps as $key => $pemp){
                if ($pemp->getPourcentage() != 999){
                    $totaux[$pemp->getMatiere()->getName()] += $pemp->getPourcentage();
                    $nbPeriodes[$pemp->getMatiere()->getName()]++;
                }
            }
        }

        foreach ($totaux as $key => $total) {
            $totaux[$key] = round($total / $nbPeriodes[$key], 1);
        }

        return $totaux;
    }

    public function getDataChart(User $eleve, $isCeb = true)
    {
        $periodes = array(1, 2, 3);
        $matCeb = array("Français", "Math", "Néerlandais", "Histoire", "Géographie", "Sciences");

        $data = new \StdClass();
        $data->labels = array('Période 1', 'Période 2', 'Examen');
        $data->datasets = array();

        //créons les matières avec les moyens du bord !
        $periode = $this->periodeRepo->findOneById(1);
        $matieres = array();
        $pemps = $this->pempRepo->findPeriodeEleveMatiere($eleve, $periode);

        foreach ($pemps as $pemp) {
            $pempInCeb = in_array($pemp->getMatiere()->getName(), $matCeb) ? true: false;
            $object = new \StdClass();
            $object->label = $pemp->getMatiere()->getName();
            $object->fillColor = $pemp->getMatiere()->getColor();
            $object->pointColor = $pemp->getMatiere()->getColor();
            $object->pointStrokeColor = $pemp->getMatiere()->getColor();
            $object->pointHighlightFill = $pemp->getMatiere()->getColor();
            $object->pointHighlightStroke = $pemp->getMatiere()->getColor();
            $object->data = $this->getPourcentageMatierePeriode($pemp->getMatiere(), $eleve);

            if ($pempInCeb && $isCeb) $data->datasets[] = $object;
            if (!$pempInCeb && !$isCeb) $data->datasets[] = $object;
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

    public function getMoyennePresence(User $user)
    {
        $results = array();
        $periodes = $this->periodeRepo->findNonOnlyPointPeriodes();
        $nbPeriodes = count($periodes);
        $points = $this->pempRepo->findPEMPByUserAndNonOnlyPointPeriode($user);

        foreach ($points as $point) {
            $presence = is_null($point->getPresence()) ? 0 : intval($point->getPresence());
            $matiere = $point->getMatiere();
            $matiereId = $matiere->getId();

            if (!isset($results[$matiereId])) {
                $results[$matiereId] = array();
                $results[$matiereId]['matiere'] = $matiere->getName();
                $results[$matiereId]['presence'] = $presence;
            } else {
                $results[$matiereId]['presence'] += $presence;
            }
        }

        foreach ($results as $key => $result) {
            $results[$key]['presence'] /= $nbPeriodes;
        }

        return $results;
    }

    public function getMoyenneComportement(User $user)
    {
        $results = array();
        $periodes = $this->periodeRepo->findNonOnlyPointPeriodes();
        $nbPeriodes = count($periodes);
        $points = $this->pempRepo->findPEMPByUserAndNonOnlyPointPeriode($user);

        foreach ($points as $point) {
            $comportement = is_null($point->getComportement()) ?
                0 :
                intval($point->getComportement());
            $matiere = $point->getMatiere();
            $matiereId = $matiere->getId();

            if (!isset($results[$matiereId])) {
                $results[$matiereId] = array();
                $results[$matiereId]['matiere'] = $matiere->getName();
                $results[$matiereId]['comportement'] = $comportement;
            } else {
                $results[$matiereId]['comportement'] += $comportement;
            }
        }

        foreach ($results as $key => $result) {
            $results[$key]['comportement'] /= $nbPeriodes;
        }

        return $results;
    }
}