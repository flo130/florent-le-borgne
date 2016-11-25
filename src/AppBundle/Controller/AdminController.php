<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Cette class sert à l'admin du site
 * 
 * @Security("is_granted('ROLE_ADMIN')")
 * 
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * Page d'accueil de l'admin
     *
     * @Route("/", name="admin"))
     *
     * @Method({"GET"})
     */
    public function HomeAction(Request $request)
    {
        return $this->render('AppBundle:pages/admin:homePage.html.twig');
    }
}