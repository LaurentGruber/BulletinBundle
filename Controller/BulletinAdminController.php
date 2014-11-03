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
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Laurent\BulletinBundle\Entity\PeriodeEleveMatierePoint;
use Laurent\BulletinBundle\Entity\PeriodeElevePointDiversPoint;
use Laurent\BulletinBundle\Entity\Periode;
use Claroline\CoreBundle\Entity\User;
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
    /** @var MatiereRepository */
    private $diversRepo;
    private $om;

    /**
     * @DI\InjectParams({
     *      "sc"                 = @DI\Inject("security.context"),
     *      "toolManager"        = @DI\Inject("claroline.manager.tool_manager"),
     *      "roleManager"        = @DI\Inject("claroline.manager.role_manager"),
     *      "userManager"        = @DI\Inject("claroline.manager.user_manager"),
     *      "om"                 = @DI\Inject("claroline.persistence.object_manager")
     * })
     */

    public function __construct(
        SecurityContextInterface $sc,
        ToolManager $toolManager,
        RoleManager $roleManager,
        UserManager $userManager,
        ObjectManager $om
    )
    {
        $this->sc                 = $sc;
        $this->toolManager        = $toolManager;
        $this->roleManager        = $roleManager;
        $this->userManager        = $userManager;

        $this->om                 = $om;
        $this->groupRepo          = $om->getRepository('ClarolineCoreBundle:Group');
        $this->userRepo          = $om->getRepository('ClarolineCoreBundle:User');
        $this->matiereRepo        = $om->getRepository('LaurentSchoolBundle:Matiere');
        $this->diversRepo        = $om->getRepository('LaurentBulletinBundle:PointDivers');
    }

    /**
     * @EXT\Route("/admin/", name="laurentBulletinAdminIndex")
     */
    public function indexAction()
    {
        $this->checkOpen();
        return $this->render('LaurentBulletinBundle::BulletinAdminIndex.html.twig');
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
     *@EXT\Template("LaurentBulletinBundle::BulletinPrintPdf.html.twig")
     *
     * @return array|Response
     */

    public function PrintPdfAction(Periode $periode, Group $group)
    {
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
     * @EXT\Route("/import/groupPeriodeMatiere", name="laurentAdminSchoolImportGroupPeriodeMatiere")
     * @EXT\Template("LaurentBulletinBundle::adminBulletinImportView.html.twig")
     */
    public function adminSchoolImportGroupPeriodeMatiereAction(Request $request)
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
                $content = $this->renderView('LaurentSchoolBundle::adminSchoolImportView.html.twig',
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
            'action' => $this->generateUrl('laurentAdminSchoolImportGroupPeriodeMatiere'),
            'messages' => ''
        );
    }

    /**
     * @EXT\Route("/import/groupPeriodeDivers", name="laurentAdminSchoolImportGroupPeriodeDivers")
     * @EXT\Template("LaurentBulletinBundle::adminBulletinImportView.html.twig")
     */
    public function adminSchoolImportGroupPeriodeDiversAction(Request $request)
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
                $content = $this->renderView('LaurentSchoolBundle::adminSchoolImportView.html.twig',
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
            'action' => $this->generateUrl('laurentAdminSchoolImportGroupPeriodeDivers'),
            'messages' => ''
        );
    }

    /**
     * @EXT\Route("/import/elevePeriodeMatiere", name="laurentAdminSchoolImportElevePeriodeMatiere")
     * @EXT\Template("LaurentBulletinBundle::adminBulletinImportView.html.twig")
     */
    public function adminSchoolImportElevePeriodeMatiereAction(Request $request)
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
            'action' => $this->generateUrl('laurentAdminSchoolImportElevePeriodeMatiere'),
            'messages' => ''
        );
    }

    /**
     * @EXT\Route("/import/elevePeriodeDivers", name="laurentAdminSchoolImportElevePeriodeDivers")
     * @EXT\Template("LaurentBulletinBundle::adminBulletinImportView.html.twig")
     */
    public function adminSchoolImportElevePeriodeDiversAction(Request $request)
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
                $content = $this->renderView('LaurentSchoolBundle::adminSchoolImportView.html.twig',
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
            'action' => $this->generateUrl('laurentAdminSchoolImportElevePeriodeDivers'),
            'messages' => ''
        );
    }

    private function checkOpen()
    {
        if ($this->sc->isGranted('ROLE_BULLETIN_ADMIN')) {
            return true;
        }

        throw new AccessDeniedException();
    }
}

