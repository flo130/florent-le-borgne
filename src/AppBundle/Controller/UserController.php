<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Form\LoginForm;
use AppBundle\Form\RegistrationForm;

/**
 * Cette class va servire, entre autre, pour gérer les URLs de login et de logout. 
 * Mais le vrai travail d'authent est fait dans un service à part (AppBundle/Security/LoginFormAuthenticator.php).
 * On configure tout ça dans le fichier de config security.yml
 */
class UserController extends Controller
{
    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $form = $this->createForm(LoginForm::class, ['_username' => $lastUsername]);
        return $this->render('AppBundle:pages:loginPage.html.twig', array(
            'form' => $form->createView(),
            'error' => $error,
        ));
    }

    /**
     * @Route("/user/logout", name="security_logout")
     * 
     * Voir la définition de la route de logout dans security.yml
     * Il faut quand même créer cette route car sinon Symfony lèvera un 404.
     * Mais de toutes façon il ne viendra pas ici, il interceptera cette requete avant.
     * C'est Symfony qui se charge de délogger l'utilisateur.
     */
    public function logoutAction()
    {
        throw new \Exception('this should not be reached!');
    }

    /**
     * @Route("/user/register", name="user_register")
     */
    public function registerAction(Request $request)
    {
        $form = $this->createForm(RegistrationForm::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Welcome '.$user->getEmail());
            return $this->get('security.authentication.guard_handler')->authenticateUserAndHandleSuccess(
                $user,
                $request,
                //ce service est à définir dans service.yml
                $this->get('app.security.login_form_authenticator'),
                'main'
            );
        }
        return $this->render('AppBundle:pages:registerPage.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/user/{id}/account", name="user_account")
     */
    public function accountAction(Request $request, User $user)
    {
        die(dump($user));
    }
}