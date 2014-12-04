<?php

namespace Laurent\BulletinBundle\Controller;

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


    /**
     * @DI\InjectParams({
     *      "sc"                 = @DI\Inject("security.context"),
     *      "toolManager"        = @DI\Inject("claroline.manager.tool_manager"),
     *      "roleManager"        = @DI\Inject("claroline.manager.role_manager"),
     *      "userManager"        = @DI\Inject("claroline.manager.user_manager"),
     *      "em"                 = @DI\Inject("doctrine.orm.entity_manager"),
     *      "om"                 = @DI\Inject("claroline.persistence.object_manager")
     * })
     */

    public function __construct(
        SecurityContextInterface $sc,
        ToolManager $toolManager,
        RoleManager $roleManager,
        UserManager $userManager,
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


    }

    /**
     * @EXT\Route("/", name="laurentBulletinIndex")
     */
    public function indexAction()
    {
        $this->checkOpen();
        return $this->render('LaurentBulletinBundle::BulletinIndex.html.twig');
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

        return array('form' => $form->createView(), 'eleve' => $eleve);


    }

    /**
     * @EXT\Route(
     *     "/periode/{periode}/{group}/{matiere}/edit/",
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
                : $this->generateUrl('laurentBulletinEditMatiere', array('periode' => $periode->getId(), 'matiere' => $matiere->getId(), 'eleve' => $eleve->getId()));

            return $this->redirect($nextAction);
            //}
            // else {
            //     throw new \Exception('tata');
            // }
        }

        return array('form' => $form->createView(), 'matiere' => $matiere, 'group' => $group);


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
     *@EXT\Template("LaurentBulletinBundle::Admin/BulletinPrint.html.twig")
     *
     * @return array|Response
     */
    public function printEleveAction(Request $request, Periode $periode, User $eleve){
        $this->checkOpenPrintPdf($request);

        $pemps = $this->pempRepo->findPeriodeEleveMatiere($eleve, $periode);
        $pemds = $this->pemdRepo->findPeriodeElevePointDivers($eleve, $periode);

        return array('pemps' => $pemps, 'pemds' => $pemds, 'eleve' => $eleve, 'periode' => $periode);
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
        $ServerIp =  system("curl -s ipv4.icanhazip.com");
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

