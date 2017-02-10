<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\LoginForm;
use AppBundle\Form\RegistrationForm;
use AppBundle\Form\UserForm;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Form\ArticleCreateForm;

/**
 * Cette class va servir, entre autre, pour gérer les URLs de login et de logout. 
 * Mais le vrai travail d'authent est fait dans un service à part (AppBundle/Security/LoginFormAuthenticator.php).
 * On configure tout ça dans le fichier de config security.yml
 * 
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/login", name="user_login")
     * 
     * @Method({"GET", "POST"})
     * 
     * @param Request $request
     * 
     * @return Response || JsonResponse
     */
    public function loginAction(Request $request)
    {
        //il faut que l'utilisateur soit annonyme
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('homepage');
        }

        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $form = $this->createForm(LoginForm::class, ['_username' => $lastUsername]);

        //retourne un JsonResponse si on est en ajax, une Response sinon
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'form' => $form->createView(),
            ), $error ? 200 : 400);
        } else {
            return $this->render('AppBundle:pages:userLoginPage.html.twig', array(
                'form' => $form->createView(),
                'error' => $error,
            ));
        }
    }

    /**
     * Voir la définition de la route de logout dans security.yml
     * Il faut quand même créer cette route car sinon Symfony lèvera un 404.
     * Mais de toutes façon il ne viendra pas ici, il interceptera cette requete avant.
     * C'est Symfony qui se charge de délogger l'utilisateur.
     * 
     * @Route("/logout", name="user_logout")
     * 
     * @Method({"GET"})
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function logoutAction()
    {
        throw new \Exception('this should not be reached!');
    }

    /**
     * Partie creation d'un compte utilisateur
     * 
     * @Route("/register", name="user_register")
     * 
     * @Method({"GET", "POST"})
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function registerAction(Request $request)
    {
        //il faut que l'utilisateur soit annonyme
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('homepage');
        }
        $form = $this->createForm(RegistrationForm::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', ucfirst(strtolower($this->get('translator')->trans('app.user.welcome'))) . ' ' . $user->getEmail());
            return $this->get('security.authentication.guard_handler')->authenticateUserAndHandleSuccess(
                $user,
                $request,
                //ce service est à définir dans service.yml
                $this->get('app.security.login_form_authenticator'),
                'main'
            );
        }
        return $this->render('AppBundle:pages:userRegisterPage.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Page d'un compte utilisateur
     * 
     * @todo : trouver un moyen de faire mieux pour ne pas uploader une valeur vide si pas d'image
     * Au lieu de 
     *     $image = $article->getImage()
     *     [...]
     *     if (!$article->getImage()) {
     *         $article->setImage($image);
     *     }
     * Essayer avec :
     *     $article->setImage(new File($this->getParameter('uploads').'/'.$article->getImage()))
     * 
     * @Route("/account/{name}", name="user_account")
     * 
     * @Method({"GET", "POST"})
     * 
     * @param Request $request
     * @param User $user
     * 
     * @return Response
     */
    public function accountAction(Request $request, User $user)
    {
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