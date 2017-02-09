<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Cette class sert à l'admin du site
 * 
 * @Security("is_granted('ROLE_ADMIN')")
 * 
 * @Route("/admin")
 */
class IndexController extends Controller
{
    /**
     * Page d'accueil de l'admin
     *
     * @Route("/", name="admin_index"))
     *
     * @Method({"GET"})
     */
    public function HomeAction(Request $request)
    {
        //nb articles créés pour n jours
        //nb articles consultés
        //nb users créés
        //autres stats
        //...
        return $this->render('AppBundle:pages/admin:indexPage.html.twig');
    }
}