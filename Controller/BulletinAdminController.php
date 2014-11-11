<?php

namespace Laurent\BulletinBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use JMS\DiExtraBundle\Annotation as DI;
use Claroline\CoreBundle\Manager\ToolManager;
use Claroline\CoreBundle\Manager\RoleManager;
use Claroline\CoreBundle\Manager\UserManager;
use Claroline\CoreBundle\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Laurent\BulletinBundle\Entity\Periode;
use Laurent\BulletinBundle\Entity\PeriodeEleveMatierePoint;
use Laurent\BulletinBundle\Entity\PeriodeElevePointDiversPoint;
use Laurent\BulletinBundle\Form\Admin\PeriodeType;
use Claroline\CoreBundle\Entity\Group;

class BulletinAdminController extends Controller
{
    private $sc;
    private $toolManager;
    private $roleManager;
    private $userManager;
    /** @var GroupRepository */
    private $groupRepo;
    /** @var UserRepository */
    private $userRepo;
    /** @var MatiereRepository */
    private $matiereRepo;
    /** @var DiversRepository */
    private $diversRepo;
    /** @var PeriodeRepository */
    private $periodeRepo;
    /** @var PeriodeEleveMatierePointRepository */
    private $pempRepo;
    /** @var PeriodeElevePointDiversPointRepository */
    private $pemdRepo;
    private $om;
    private $em;
    /** @var  string */
    private $pdfDir;

    /**
     * @DI\InjectParams({
     *      "sc"                 = @DI\Inject("security.context"),
     *      "toolManager"        = @DI\Inject("claroline.manager.tool_manager"),
     *      "roleManager"        = @DI\Inject("claroline.manager.role_manager"),
     *      "userManager"        = @DI\Inject("claroline.manager.user_manager"),
     *      "om"                 = @DI\Inject("claroline.persistence.object_manager"),
     *      "em"                 = @DI\Inject("doctrine.orm.entity_manager"),
     *      "pdfDir"             = @DI\Inject("%laurent.directories.pdf%")
     * })
     */

    public function __construct(
        SecurityContextInterface $sc,
        ToolManager $toolManager,
        RoleManager $roleManager,
        UserManager $userManager,
        ObjectManager $om,
        EntityManager $em,
        $pdfDir
    )
    {
        $this->sc                 = $sc;
        $this->toolManager        = $toolManager;
        $this->roleManager        = $roleManager;
        $this->userManager        = $userManager;
        $this->pdfDir             = $pdfDir;

        $this->om                 = $om;
        $this->em                 = $em;
        $this->groupRepo          = $om->getRepository('ClarolineCoreBundle:Group');
        $this->userRepo          = $om->getRepository('ClarolineCoreBundle:User');
        $this->matiereRepo        = $om->getRepository('LaurentSchoolBundle:Matiere');
        $this->diversRepo        = $om->getRepository('LaurentBulletinBundle:PointDivers');
        $this->periodeRepo        = $om->getRepository('LaurentBulletinBundle:Periode');
        $this->pempRepo           = $om->getRepository('LaurentBulletinBundle:PeriodeEleveMatierePoint');
        $this->pemdRepo           = $om->getRepository('LaurentBulletinBundle:PeriodeElevePointDiversPoint');

    }

    /**
     * @EXT\Route("/admin/", name="laurentBulletinAdminIndex")
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

        return $this->render('LaurentBulletinBundle::Admin/BulletinAdminIndex.html.twig', array('periodes' => $periodes, 'periodeCompleted' => $periodeCompleted));
    }

    /**
     * @EXT\Route(
     *     "/admin/{periode}/{group}/pdf/",
     *     name="laurentBulletinPrintPdf",
     *     options = {"expose"=true}
     * )
     *
     *
     * @param Periode $periode
     * @param Group $group
     *
     *@EXT\Template("LaurentBulletinBundle::Admin/BulletinPrintPdf.html.twig")
     *
     * @return array|Response
     */

    public function PrintPdfAction(Periode $periode, Group $group)
    {
        #throw new \Exception($this->pdfDir);
        $this->checkOpen();
        $eleves = $this->userRepo->findByGroup($group);
        $eleve = 25;

        $pageUrl = $this->generateUrl('laurentBulletinPrintEleve', array('periode' => $periode->getId(), 'eleve' => $eleve), true); // use absolute path!

        return new Response(
            $this->get('knp_snappy.pdf')->getOutput($pageUrl),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="file.pdf"'
            )
        );
    }

    /**
     * @EXT\Route("/import/groupPeriodeMatiere", name="laurentBulletinImportGroupPeriodeMatiere")
     * @EXT\Template("LaurentBulletinBundle::Admin/adminBulletinImportView.html.twig")
     */
    public function bulletinImportGroupPeriodeMatiereAction(Request $request)
    {
        $this->checkOpen();
        $em = $this->get('doctrine')->getManager();

        $form = $this->createFormBuilder()
            ->add('fichier', 'file', array('label' => 'Fichier CSV'))
            ->add('envoyer', 'submit', array('attr' => array('class' => 'btn btn-primary')))
            ->getForm()
        ;

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $messages = array();
                $fichier = $form->get('fichier')->getNormData();
                $file = fopen($fichier->getPathname(), 'r');
                $this->om->startFlushSuite();

                while (($gpmCsv = fgetcsv($file, 0, ";", '"')) !== FALSE && is_array($gpmCsv) && count($gpmCsv) === 4) {
                    $groupName = $gpmCsv[0];
                    $matiereName = $gpmCsv[1];
                    $periodeId = $gpmCsv[2];
                    $ordre = $gpmCsv[3];


                    if ($this->matiereRepo->findOneByViewName($matiereName)){

                        $matiere = $this->matiereRepo->findOneByViewName($matiereName);
                        $total = $matiere->getNbPeriode() * 10;

                        $periode = $this->getDoctrine()->getRepository('LaurentBulletinBundle:Periode')->findOneById($periodeId);

                        $group = $this->groupRepo->findOneByName($groupName);
                        $users = $this->userRepo->findByGroup($group);

                        foreach($users as $user){
                            $pemp = new PeriodeEleveMatierePoint();
                            $pemp->setEleve($user);
                            $pemp->setMatiere($matiere);
                            $pemp->setTotal($total);
                            $pemp->setPeriode($periode);
                            $pemp->setPosition($ordre);

                            $em->persist($pemp);
                        }

                        $messages[] = "<b>La matière  $matiereName a été ajouté aux élèves du group $groupName pour la période $periodeId.</b>";
                    }

                    else {
                        $messages[] = "<b>La matière  $matiereName n'existe pas il faut d'abord le créer avant de l'ajouter</b>";
                    }

                }

                $this->om->endFlushSuite();

                fclose($file);
                $content = $this->renderView('LaurentSchoolBundle::Admin/adminSchoolImportView.html.twig',
                    array('form' => $form->createView(),
                        'titre' => '',
                        'action' => $this->generateUrl('laurentAdminSchoolImportGroupPeriodeMatiere'),
                        'messages' => $messages
                    ));

                return new Response($content);

            }
        }
        return array('form' => $form->createView(),
            'titre' => '',
            'action' => $this->generateUrl('laurentBulletinImportGroupPeriodeMatiere'),
            'messages' => ''
        );
    }

    /**
     * @EXT\Route("/import/groupPeriodeDivers", name="laurentBulletinImportGroupPeriodeDivers")
     * @EXT\Template("LaurentBulletinBundle::Admin/adminBulletinImportView.html.twig")
     */
    public function bulletinImportGroupPeriodeDiversAction(Request $request)
    {
        $this->checkOpen();
        $em = $this->get('doctrine')->getManager();

        $form = $this->createFormBuilder()
            ->add('fichier', 'file', array('label' => 'Fichier CSV'))
            ->add('envoyer', 'submit', array('attr' => array('class' => 'btn btn-primary')))
            ->getForm()
        ;

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $messages = array();
                $fichier = $form->get('fichier')->getNormData();
                $file = fopen($fichier->getPathname(), 'r');
                $this->om->startFlushSuite();

                while (($gpmCsv = fgetcsv($file, 0, ";", '"')) !== FALSE && is_array($gpmCsv) && count($gpmCsv) === 5) {
                    $groupName = $gpmCsv[0];
                    $diversName = $gpmCsv[1];
                    $total = $gpmCsv[2];
                    $periodeId = $gpmCsv[3];
                    $ordre = $gpmCsv[4];


                    if ($this->diversRepo->findOneByName($diversName)){

                        $divers = $this->diversRepo->findOneByName($diversName);

                        $periode = $this->getDoctrine()->getRepository('LaurentBulletinBundle:Periode')->findOneById($periodeId);

                        $group = $this->groupRepo->findOneByName($groupName);
                        $users = $this->userRepo->findByGroup($group);

                        foreach($users as $user){
                            $pemd = new PeriodeElevePointDiversPoint();
                            $pemd->setEleve($user);
                            $pemd->setDivers($divers);
                            $pemd->setTotal($total);
                            $pemd->setPeriode($periode);
                            $pemd->setPosition($ordre);

                            $em->persist($pemd);
                        }

                        $messages[] = "<b>Le  $diversName a été ajouté aux élèves du group $groupName pour la période $periodeId.</b>";
                    }

                    else {
                        $messages[] = "<b>Le  $diversName n'existe pas il faut d'abord le créer avant de l'ajouter</b>";
                    }

                }

                $this->om->endFlushSuite();

                fclose($file);
                $content = $this->renderView('LaurentSchoolBundle::Admin/adminSchoolImportView.html.twig',
                    array('form' => $form->createView(),
                        'titre' => '',
                        'action' => $this->generateUrl('laurentAdminSchoolImportGroupPeriodeDivers'),
                        'messages' => $messages
                    ));

                return new Response($content);

            }
        }
        return array('form' => $form->createView(),
            'titre' => '',
            'action' => $this->generateUrl('laurentBulletinImportGroupPeriodeDivers'),
            'messages' => ''
        );
    }

    /**
     * @EXT\Route("/import/elevePeriodeMatiere", name="laurentBulletinImportElevePeriodeMatiere")
     * @EXT\Template("LaurentBulletinBundle::Admin/adminBulletinImportView.html.twig")
     */
    public function bulletinImportElevePeriodeMatiereAction(Request $request)
    {
        $this->checkOpen();
        $em = $this->get('doctrine')->getManager();

        $form = $this->createFormBuilder()
            ->add('fichier', 'file', array('label' => 'Fichier CSV'))
            ->add('envoyer', 'submit', array('attr' => array('class' => 'btn btn-primary')))
            ->getForm()
        ;

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $messages = array();
                $fichier = $form->get('fichier')->getNormData();
                $file = fopen($fichier->getPathname(), 'r');
                $this->om->startFlushSuite();

                while (($gpmCsv = fgetcsv($file, 0, ";", '"')) !== FALSE && is_array($gpmCsv) && count($gpmCsv) === 4) {
                    $eleveName = $gpmCsv[0];
                    $matiereName = $gpmCsv[1];
                    $periodeId = $gpmCsv[2];
                    $ordre = $gpmCsv[3];


                    if ($this->matiereRepo->findOneByViewName($matiereName)){

                        $matiere = $this->matiereRepo->findOneByViewName($matiereName);
                        $total = $matiere->getNbPeriode() * 10;

                        $periode = $this->getDoctrine()->getRepository('LaurentBulletinBundle:Periode')->findOneById($periodeId);

                        $user = $this->userRepo->findOneByUsername($eleveName);

                        $pemp = new PeriodeEleveMatierePoint();
                        $pemp->setEleve($user);
                        $pemp->setMatiere($matiere);
                        $pemp->setTotal($total);
                        $pemp->setPeriode($periode);
                        $pemp->setPosition($ordre);

                        $em->persist($pemp);


                        $messages[] = "<b>La matière  $matiereName a été ajouté à l'élève $eleveName pour la période $periodeId.</b>";
                    }

                    else {
                        $messages[] = "<b>La matière  $matiereName n'existe pas il faut d'abord le créer avant de l'ajouter</b>";
                    }

                }

                $this->om->endFlushSuite();

                fclose($file);
                $content = $this->renderView('LaurentSchoolBundle::adminSchoolImportView.html.twig',
                    array('form' => $form->createView(),
                        'titre' => '',
                        'action' => $this->generateUrl('laurentAdminSchoolImportElevePeriodeMatiere'),
                        'messages' => $messages
                    ));

                return new Response($content);

            }
        }
        return array('form' => $form->createView(),
            'titre' => '',
            'action' => $this->generateUrl('laurentBulletinImportElevePeriodeMatiere'),
            'messages' => ''
        );
    }

    /**
     * @EXT\Route("/import/elevePeriodeDivers", name="laurentBulletinImportElevePeriodeDivers")
     * @EXT\Template("LaurentBulletinBundle::Admin/adminBulletinImportView.html.twig")
     */
    public function bulletinImportElevePeriodeDiversAction(Request $request)
    {
        $this->checkOpen();
        $em = $this->get('doctrine')->getManager();

        $form = $this->createFormBuilder()
            ->add('fichier', 'file', array('label' => 'Fichier CSV'))
            ->add('envoyer', 'submit', array('attr' => array('class' => 'btn btn-primary')))
            ->getForm()
        ;

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $messages = array();
                $fichier = $form->get('fichier')->getNormData();
                $file = fopen($fichier->getPathname(), 'r');
                $this->om->startFlushSuite();

                while (($gpmCsv = fgetcsv($file, 0, ";", '"')) !== FALSE && is_array($gpmCsv) && count($gpmCsv) === 5) {
                    $eleveName = $gpmCsv[0];
                    $diversName = $gpmCsv[1];
                    $total = $gpmCsv[2];
                    $periodeId = $gpmCsv[3];
                    $ordre = $gpmCsv[4];


                    if ($this->diversRepo->findOneByName($diversName)){

                        $divers = $this->diversRepo->findOneByName($diversName);

                        $periode = $this->getDoctrine()->getRepository('LaurentBulletinBundle:Periode')->findOneById($periodeId);

                        $user = $this->userRepo->findOneByUsername($eleveName);

                        $pemd = new PeriodeElevePointDiversPoint();
                        $pemd->setEleve($user);
                        $pemd->setDivers($divers);
                        $pemd->setTotal($total);
                        $pemd->setPeriode($periode);
                        $pemd->setPosition($ordre);
                        $em->persist($pemd);


                        $messages[] = "<b>Le  $diversName a été ajouté à l'élève $eleveName pour la période $periodeId.</b>";
                    }

                    else {
                        $messages[] = "<b>Le  $diversName n'existe pas il faut d'abord le créer avant de l'ajouter</b>";
                    }

                }

                $this->om->endFlushSuite();

                fclose($file);
                $content = $this->renderView('LaurentSchoolBundle::Admin/adminSchoolImportView.html.twig',
                    array('form' => $form->createView(),
                        'titre' => '',
                        'action' => $this->generateUrl('laurentAdminSchoolImportElevePeriodeDivers'),
                        'messages' => $messages
                    ));

                return new Response($content);

            }
        }
        return array('form' => $form->createView(),
            'titre' => '',
            'action' => $this->generateUrl('laurentBulletinImportElevePeriodeDivers'),
            'messages' => ''
        );
    }

    /**
     * @EXT\Route(
     *     "/export/{group}/list/",
     *     name="laurentBulletinExportGroupList",
     *     options = {"expose"=true}
     * )
     *
     *
     * @param Group $group
     *
     */
    public function bulletinExportGroupListAction(Request $request, Group $group)
    {
        $this->checkOpen();

        $answers = $this->userRepo->findByGroup($group);

        $handle = fopen('php://memory', 'r+');
        $header = array();

        foreach ($answers as $answer) {
            fputcsv($handle, $answer->getData());
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        $groupName = $group->getName();
        $contD= "attachment; filename=$groupName.csv";

        return new Response($content, 200, array(
            'Content-Type' => 'application/force-download',
            'Content-Disposition' => $contD
        ));
    }

    /**
     * @EXT\Route("/admin/periode/add", name="laurentBulletinPeriodeAdd", options = {"expose"=true})
     *
     * @EXT\Template("LaurentBulletinBundle::Admin/PeriodeForm.html.twig")
     */
    public function adminSchoolPeriodeAddAction(Request $request)
    {
        $this->checkOpen();
        $periode = new Periode();
        $form = $this->createForm(new PeriodeType, $periode);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->em->persist($periode);
                $this->em->flush();
            }
        }
        return array('form' => $form->createView());
    }

    /**
     * @EXT\Route(
     *     "admin/{periode}/edit/",
     *     name="laurentBulletinPeriodeEdit",
     *     options = {"expose"=true}
     * )
     *
     * @param Periode $periode

     * @EXT\Template("LaurentBulletinBundle::Admin/PeriodeForm.html.twig")
     */

    public function adminSchoolPeriodeEditAction(Request $request, Periode $periode)
    {
        $this->checkOpen();

       $form = $this->createForm(new PeriodeType, $periode);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->em->persist($periode);
                $this->em->flush();
            }
        }
        return array('form' => $form->createView());
    }

    private function checkOpen()
    {
        if ($this->sc->isGranted('ROLE_BULLETIN_ADMIN')) {
            return true;
        }

        throw new AccessDeniedException();
    }
}

