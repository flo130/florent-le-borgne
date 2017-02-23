<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\UserChangeRoleForm;

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
    public function userAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:pages/admin:userPage.html.twig', array(
            'users' => $em->getRepository('AppBundle:User')->findAll(),
        ));
    }

    /**
     * Page de suppression d'un compte utilisateur
     *
     * @Route("/delete/{slug}", name="admin_user_delete")
     *
     * @Method({"GET"})
     *
     * @param Request $request
     * @param User $user
     *
     * @return Response
     */
    public function deleteAction(Request $request, User $user)
    {
        /** @see AppBundle\Security\UserVoter */
        $this->denyAccessUnlessGranted('delete', $user);
        $em = $this->getDoctrine()->getManager();
        $this->get('logger')->notice('User suppression', array('email' => $user->getEmail()));
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', ucfirst(strtolower($this->get('translator')->trans('app.delete_success'))));
        return $this->redirect($this->generateUrl('admin_user'));
    }

    /**
     * Formualire de changement de role d'un utilisateur
     * 
     * @Route("/change-role/{slug}", name="admin_change_role"))
     * 
     * @Method({"GET", "POST"})
     * 
     * @param Request $request
     * 
     * @return Response || JsonResponse
     */
    public function changeRoleAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(UserChangeRoleForm::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $isValid = $form->isValid();
            if ($isValid) {
                die(var_dump($user));

                $em->persist($article);
                $em->flush();
                $this->get('logger')->info('Article modification', array('id' => $user->getId()));
                $this->addFlash('success', ucfirst(strtolower($this->get('translator')->trans('app.update_success'))));
                $redirectUrl = $this->generateUrl('admin_user', array(
                    'slug' => $user->getSlug(),
                ), UrlGeneratorInterface::ABSOLUTE_URL);
                if (!$request->isXmlHttpRequest()) {
                    return $this->redirect($redirectUrl);
                }
            }
        }
        //retourne un JsonResponse si on est en ajax, une Response sinon
        if ($request->isXmlHttpRequest()) {
            if ($isValid) {
                return new JsonResponse(array(
                    'redirect' => $redirectUrl,
                ), 200);
            } else {
                return new JsonResponse(array(
                    'message' => $isValid ? ucfirst(strtolower($this->get('translator')->trans('app.update_success'))) : '',
                    'form' => $this->renderView('AppBundle:pages/admin:userChangeRolePage.html.twig', array(
                        'form' => $form->createView(),
                    )),
                ), 400);
            }
        } else {
            return $this->render('AppBundle:pages/admin:userChangeRolePage.html.twig', array(
                'form' => $form->createView(),
            ));
        }
    }
}