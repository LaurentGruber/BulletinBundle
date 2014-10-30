<?php

namespace Laurent\BulletinBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;

class BulletinController extends Controller
{
    /**
     * @EXT\Route("/", name="laurentBulletinIndex")
     */
    public function indexAction()
    {
        return $this->render('LaurentBulletinBundle::BulletinIndex.html.twig');
    }
}

?>