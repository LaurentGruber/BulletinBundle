<?php

namespace Laurent\BulletinBundle\Controller;

use Laurent\BulletinBundle\Manager\TotauxManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use JMS\DiExtraBundle\Annotation as DI;
use Claroline\CoreBundle\Manager\ToolManager;
use Claroline\CoreBundle\Manager\RoleManager;
use Claroline\CoreBundle\Manager\UserManager;
use Doctrine\ORM\EntityManager;
use Claroline\CoreBundle\Persistence\ObjectManager;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Claroline\CoreBundle\Entity\User;
use Claroline\CoreBundle\Entity\Group;
use Laurent\BulletinBundle\Entity\Periode;
use Laurent\BulletinBundle\Entity\Pemps;
use Laurent\BulletinBundle\Form\Type\PempsType;
use Laurent\BulletinBundle\Form\Type\MatiereType;
use Laurent\SchoolBundle\Entity\Matiere;


class BulletinController extends Controller
{
    private $sc;
    private $toolManager;
    private $roleManager;
    private $userManager;
    private $em;
    private $om;
    /** @var PeriodeEleveMatierePointRepository */
    private $pempRepo;
    /** @var PeriodeElevePointDiversPointRepository */
    private $pemdRepo;
    /** @var GroupRepository */
    private $groupRepo;
    /** @var UserRepository */
    private $userRepo;
    /** @var ClassRepository */
    private $classRepo;
    /** @var ProfMatiereGroupRepository */
    private $pmgrRepo;
    /** @var PeriodeRepository */
    private $periodeRepo;
    private $totauxManager;


    /**
     * @DI\InjectParams({
     *      "sc"                 = @DI\Inject("security.context"),
     *      "toolManager"        = @DI\Inject("claroline.manager.tool_manager"),
     *      "roleManager"        = @DI\Inject("claroline.manager.role_manager"),
     *      "userManager"        = @DI\Inject("claroline.manager.user_manager"),
     *      "em"                 = @DI\Inject("doctrine.orm.entity_manager"),
     *      "om"                 = @DI\Inject("claroline.persistence.object_manager"),
     *      "totauxManager"      = @DI\Inject("laurent.manager.totaux_manager"),
     * })
     */

    public function __construct(
        SecurityContextInterface $sc,
        ToolManager $toolManager,
        RoleManager $roleManager,
        UserManager $userManager,
        TotauxManager $totauxManager,
        EntityManager $em,
        ObjectManager $om
      )
    {
        $this->sc                 = $sc;
        $this->toolManager        = $toolManager;
        $this->roleManager        = $roleManager;
        $this->userManager        = $userManager;
        $this->em                 = $em;
        $this->om                 = $om;
        $this->pempRepo           = $om->getRepository('LaurentBulletinBundle:PeriodeEleveMatierePoint');
        $this->pemdRepo           = $om->getRepository('LaurentBulletinBundle:PeriodeElevePointDiversPoint');
        $this->groupRepo          = $om->getRepository('ClarolineCoreBundle:Group');
        $this->userRepo           = $om->getRepository('ClarolineCoreBundle:User');
        $this->classeRepo         = $om->getRepository('LaurentSchoolBundle:Classe');
        $this->pmgrRepo           = $om->getRepository('LaurentSchoolBundle:ProfMatiereGroup');
        $this->periodeRepo        = $om->getRepository('LaurentBulletinBundle:Periode');
        $this->totauxManager      = $totauxManager;


    }

    /**
     * @EXT\Route("/", name="laurentBulletinIndex")
     */
    public function indexAction()
    {
        $this->checkOpen();

        $periodes = $this->periodeRepo->findAll();

        $periodeCompleted = array();

        foreach ($periodes as $periode){
            $id = $periode->getId();
            $total = 0;
            $nbComp = 0;

            $pemps = $this->pempRepo->findByPeriode($periode);
            foreach ($pemps as $pemp){
                $total = $total + 3;
                if ($pemp->getPoint() >= 0){
                    $nbComp = $nbComp + 1;
                }
                elseif ($pemp->getPresence() >= 0){
                    $nbComp = $nbComp + 1;
                }
                elseif ($pemp->getComportement() >= 0){
                    $nbComp = $nbComp + 1;
                }
            }
            $pemds = $this->pemdRepo->findByPeriode($periode);
            foreach ($pemds as $pemd){
                $total = $total + 1;
                if ($pemd->getPoint() >= 0){
                    $nbComp = $nbComp + 1;
                }

            }
            if ($total != 0) {$pourcent = $nbComp / $total * 100;}
            else {$pourcent = 0;}

            $periodeCompleted[$id] = number_format($pourcent,0);
        }

        return $this->render('LaurentBulletinBundle::BulletinIndex.html.twig', array('periodes' => $periodes, 'periodeCompleted' => $periodeCompleted));
    }

    /**
     * @EXT\Route(
     *     "/periode/{periode}/{group}/list/",
     *     name="laurentBulletinListEleve",
     *     options = {"expose"=true}
     * )
     *
     *
     * @param Periode $periode
     * @param Group $group
     *
     *@EXT\Template("LaurentBulletinBundle::BulletinListEleves.html.twig")
     *
     * @return array|Response
     */
    public function listEleveAction(Periode $periode, Group $group)
    {
        $this->checkOpen();
        $eleves = $this->userRepo->findByGroup($group);
        return array('periode' => $periode, 'eleves' => $eleves, 'group' => $group);
    }

    /**
     * @EXT\Route(
     *     "/periode/{periode}/list/",
     *     name="laurentBulletinListClasse",
     *     options = {"expose"=true}
     * )
     *
     * @param Periode $periode
     *
     * @EXT\ParamConverter("user", options={"authenticatedUser" = true})
     * @param User         $user         the user
     *
     *
     * @return array|Response
     */
    public function listClasseAction(Periode $periode, User $user)
    {
        $this->checkOpen();
        $groups = array();
        if ($this->sc->isGranted('ROLE_BULLETIN_ADMIN')){
            $classes = $this->classeRepo->findAll();
            foreach ($classes as $classe){
                $groups[] = $classe->getGroup();
            }
            $content = $this->renderView('LaurentBulletinBundle::Admin/BulletinListClasses.html.twig',
                array('periode' => $periode, 'groups' => $groups)
                );
            return new Response($content);
        }

        elseif ($this->sc->isGranted('ROLE_PROF')){
            $pmgrs = $this->pmgrRepo->findByProf($user);
            $content = $this->renderView('LaurentBulletinBundle::BulletinListGroups.html.twig',
                array('periode' => $periode, 'pmgrs' => $pmgrs)
                );
            return new Response($content);
        }

        else { return $this->redirect('http://google.be');}


    }

    /**
     * @EXT\Route(
     *     "/prof/periode/{periode}/list/",
     *     name="laurentBulletinListMyGroup",
     *     options = {"expose"=true}
     * )
     *
     * @param Periode $periode
     *
     * @EXT\ParamConverter("user", options={"authenticatedUser" = true})
     * @param User         $user         the user
     *
     *
     * @return array|Response
     */
    public function listMyGroupAction(Periode $periode, User $user)
    {
        $this->checkOpen();
        $groups = array();

        if ($this->sc->isGranted('ROLE_PROF')){
            $pmgrs = $this->pmgrRepo->findByProf($user);
            $content = $this->renderView('LaurentBulletinBundle::BulletinListGroups.html.twig',
                array('periode' => $periode, 'pmgrs' => $pmgrs)
            );
            return new Response($content);
        }

        throw new AccessDeniedException();


    }

    /**
     * @EXT\Route(
     *     "/prof/periode/{periode}/group/{group}/matiere/{matiere}/list/",
     *     name="laurentBulletinListEleveProf",
     *     options = {"expose"=true}
     * )
     *
     *
     * @param Periode $periode
     * @param Group $group
     * @param Matiere $matiere
     *
     *@EXT\Template("LaurentBulletinBundle::BulletinListEleves.html.twig")
     *
     * @return array|Response
     */
    public function listEleveProfAction(Periode $periode, Group $group, Matiere $matiere)
    {
        $this->checkOpen();

        if ($matiere->getName() == 'Titulaire'){
            $titulaireUrl = $this->generateUrl('laurentBulletinListEleve', array('periode' => $periode->getId(), 'group' => $group->getId()));
            return $this->redirect($titulaireUrl);
        }
        else {
            $editMatiereUrl = $this->generateUrl('laurentBulletinEditMatiere', array('periode' => $periode->getId(), 'matiere' => $matiere->getId(), 'group' => $group->getId()));
            return $this->redirect($editMatiereUrl);
        }

    }

    /**
     * @EXT\Route(
     *     "/periode/{periode}/{eleve}/edit/",
     *     name="laurentBulletinEditEleve",
     *     options = {"expose"=true}
     * )
     *
     *
     * @param Periode $periode
     * @param User $eleve
     *
     *@EXT\Template("LaurentBulletinBundle::Admin/BulletinEdit.html.twig")
     *
     * @return array|Response
     */
    public function editEleveAction(Request $request, Periode $periode, User $eleve)
    {
        $this->checkOpen();

        $pemps = $this->pempRepo->findPeriodeEleveMatiere($eleve, $periode);
        $pemds = $this->pemdRepo->findPeriodeElevePointDivers($eleve, $periode);

        $pempCollection = new Pemps;
        foreach ($pemps as $pemp) {
            $pempCollection->getPemps()->add($pemp);
        }
        foreach ($pemds as $pemd) {
            $pempCollection->getPemds()->add($pemd);
        }

        $form = $this->createForm(new PempsType, $pempCollection);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            //if ($form->isValid()) {
              //  throw new \Exception('toto');
            foreach ($pempCollection as $pemp){
                $this->em->persist($pemp);
            }
                $this->em->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked()
                ? 'task_new'
                : $this->generateUrl('laurentBulletinEditEleve', array('periode' => $periode->getId(), 'eleve' => $eleve->getId()));

                return $this->redirect($nextAction);
            //}
           // else {
           //     throw new \Exception('tata');
           // }
        }

        return array('form' => $form->createView(), 'eleve' => $eleve, 'periode' => $periode);


    }

    /**
     * @EXT\Route(
     *     "/periode/{periode}/group/{group}/matiere/{matiere}/edit/",
     *     name="laurentBulletinEditMatiere",
     *     options = {"expose"=true}
     * )
     *
     *
     * @param Periode $periode
     * @param Group $group
     * @param Matiere $matiere
     *
     *@EXT\Template("LaurentBulletinBundle::BulletinEditMatiere.html.twig")
     *
     * @return array|Response
     */
    public function editMatiereAction(Request $request, Periode $periode, Group $group, Matiere $matiere)
    {
        $this->checkOpen();
        $pemps = array();
        $eleves = $this->userRepo->findByGroup($group);
        $pempCollection = new Pemps;
        foreach ( $eleves as $eleve){
            $pempCollection->getPemps()->add($this->pempRepo->findPeriodeMatiereEleve($periode, $eleve, $matiere));
        }

        $form = $this->createForm(new MatiereType, $pempCollection);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            //if ($form->isValid()) {
            //  throw new \Exception('toto');
            foreach ($pempCollection as $pemp){
                $this->em->persist($pemp);
            }
            $this->em->flush();

            return $this->redirect($this->generateUrl('laurentBulletinEditMatiere', array('periode' => $periode->getId(), 'matiere' => $matiere->getId(), 'group' => $group->getId())));
            //}
            // else {
            //     throw new \Exception('tata');
            // }
        }

        return array('form' => $form->createView(), 'matiere' => $matiere, 'group' => $group, 'periode' => $periode);


    }

    /**
     * @EXT\Route(
     *     "/periode/{periode}/eleve/{eleve}/print/",
     *     name="laurentBulletinPrintEleve",
     *     options = {"expose"=true}
     * )
     *
     *
     * @param Periode $periode
     * @param User $eleve
     *
     * @return array|Response
     */
    public function printEleveAction(Request $request, Periode $periode, User $eleve){
        $this->checkOpenPrintPdf($request);
        $totaux = [];
        $totauxMatieres = [];
        $recap = 0;

        if (!$periode->getOnlyPoint()){
            $pemps = $this->pempRepo->findPeriodeEleveMatiere($eleve, $periode);
            $pemds = $this->pemdRepo->findPeriodeElevePointDivers($eleve, $periode);

        } else {
            $pemps = array();
            $pemds = array();

            $periodes = array(1, 2, 3);

            foreach ($periodes as $per){
                $periode = $this->periodeRepo->findOneById($per);
                $pemps[] = $this->pempRepo->findPeriodeEleveMatiere($eleve, $periode);
                $pemds[] = $this->pemdRepo->findPeriodeElevePointDivers($eleve, $periode);

                $totaux[] = $this->totauxManager->getTotalPeriode($periode, $eleve);

            }
            $totauxMatieres = $this->totauxManager->getTotalPeriodes($eleve);
        }

        $template = 'LaurentBulletinBundle::Templates/'.$periode->getTemplate().'.html.twig';


        foreach ($totaux as $total) {
            $recap += $total['totalPourcentage'] / 3;
        }

        $recap = round($recap, 1);

        $params = array('pemps' => $pemps, 'pemds' => $pemds, 'eleve' => $eleve, 'periode' => $periode, 'totaux' => $totaux, 'totauxMatieres' => $totauxMatieres, 'recap' => $recap);

        return $this->render($template, $params);
    }

    /**
     * @EXT\Route(
     *     "/chart/eleve/{eleve}",
     *     name="laurentBulletinShowEleveDataChart",
     *     options = {"expose"=true}
     * )
     *
     * @param User $eleve
     *
     * @return Response
     */
    public function showDataChartAction(User $eleve)
    {
        $this->checkOpen();
        $json = $this->totauxManager->getDataChart($eleve);
        //throw new \Exception(var_dump($json));

        return $this->render('LaurentBulletinBundle::BulletinShowDataChart.html.twig', array('json' => $json));

    }

    private function checkOpen()
    {
        if ($this->sc->isGranted('ROLE_BULLETIN_ADMIN') or $this->sc->isGranted('ROLE_PROF')) {
            return true;
        }

        throw new AccessDeniedException();
    }

    private function checkOpenPrintPdf(Request $request = NULL)
    {
        //$ServerIp =  system("curl -s ipv4.icanhazip.com");

        if ($this->sc->isGranted('ROLE_BULLETIN_ADMIN') or $this->sc->isGranted('ROLE_PROF')) {
            return true;
        }
        elseif (!is_null($request) && $request->getClientIp() === '127.0.0.1'){
            return true;
        }

        elseif (!is_null($request) && $request->getClientIp() == '91.121.211.13'){
            return true;
        }

        throw new AccessDeniedException();
    }
}

