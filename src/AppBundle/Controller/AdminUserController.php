<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;

/**
 * Cette class sert à l'admin des utilisateurs
 * 
 * @Security("is_granted('ROLE_ADMIN')")
 * 
 * @Route("/admin/user")
 */
class AdminUserController extends Controller
{
    /**
     * Page de recherche d'un utilisateur
     *
     * @Route("/search", name="admin_user_search"))
     *
     * @Method({"GET", "POST"})
     */
    public function SearchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:pages/admin:searchUserPage.html.twig', array(
            'users' => $em->getRepository('AppBundle:User')->findAll(),
        ));
    }
}