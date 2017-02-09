<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Cette class sert à l'admin des utilisateurs
 * 
 * @Security("is_granted('ROLE_ADMIN')")
 * 
 * @Route("/admin/user")
 */
class UserController extends Controller
{
    /**
     * Page de recherche d'un utilisateur
     *
     * @Route("/", name="admin_user"))
     *
     * @Method({"GET"})
     */
    public function UserAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:pages/admin:userPage.html.twig', array(
            'users' => $em->getRepository('AppBundle:User')->findAll(),
        ));
    }

    /**
     * Formualire de changement de role d'un utilisateur
     * 
     * @Route("/change-role/{name}", name="admin_change_role"))
     * 
     * @Method({"GET", "POST"})
     * 
     * @param Request $request
     * 
     * @return Response || JsonResponse
     */
    public function ChangeRoleAction(Request $request, User $user) {

        return new Response("uuuuuuuuuuuuu");

        $em = $this->getDoctrine()->getManager();
        $image = $user->getAvatar();
        $form = $this->createForm(UserForm::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            if (!$user->getAvatar()) {
                $user->setImage($image);
            }
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Update successfully');
        }
        return $this->render('AppBundle:pages:userAccountPage.html.twig', array(
                'user' => $user,
                'userForm' => $form->createView(),
                'userArticles' => $em->getRepository('AppBundle:Article')->findAllByUser($user->getId()),
                'userComments' => $em->getRepository('AppBundle:ArticleComment')->findByUserIdOrderByCreatedDate($user->getId()),
        ));
    }
}