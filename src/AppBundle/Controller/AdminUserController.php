<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
    	return $this->render('AppBundle:pages/admin:searchUserPage.html.twig');
    }

    /**
     * Page détail d'un utilisateur
     * 
     * @Route("/edit/{id}", name="admin_user_edit"))
     * 
     * @Method({"GET", "POST"})
     * 
     * @param User $user
     */
    public function EditAction(Request $request, User $user)
    {
        die('admin_user_edit');
    }
}