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
use Laurent\BulletinBundle\Entity\Periode;
use Laurent\BulletinBundle\Entity\Pemps;
use Laurent\BulletinBundle\Form\Type\PempsType;


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
        $this->pempRepo          = $om->getRepository('LaurentBulletinBundle:PeriodeEleveMatierePoint');


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
     *     "/{periode}/{eleve}/edit/",
     *     name="laurentBulletinEditEleve",
     *     options = {"expose"=true}
     * )
     *
     *
     * @param Periode $periode
     * @param User $eleve
     *
     *@EXT\Template("LaurentBulletinBundle::BulletinEdit.html.twig")
     *
     * @return array|Response
     */
    public function editEleveAction(Request $request, Periode $periode, User $eleve)
    {
        $this->checkOpen();

        $pemps = $this->pempRepo->findPeriodeEleveMatiere($eleve, $periode);
        $pempCollection = new Pemps;
        foreach ($pemps as $pemp) {
            $pempCollection->getPemps()->add($pemp);
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

        return array('form' => $form->createView(), 'pemps' => $pemps);


    }


    private function checkOpen()
    {
        if ($this->sc->isGranted('ROLE_BULLETIN_ADMIN')) {
            return true;
        }

        throw new AccessDeniedException();
    }
}

