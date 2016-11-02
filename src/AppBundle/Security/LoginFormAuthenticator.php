<?php
namespace AppBundle\Security;

use AppBundle\Form\LoginForm;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

/**
 * Cette class étand AbstractFormLoginAuthenticator du core Symfony.
 * Elle va s'occuper de gérer l'authentification des utilisateurs.
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    private $formFactory;
    private $em;
    private $router;
    private $passwordEncoder;


    /**
     * @param FormFactoryInterface $formFactory
     * @param EntityManager $em
     * @param RouterInterface $router
     * @param UserPasswordEncoder $passwordEncoder
     */
    public function __construct(FormFactoryInterface $formFactory, EntityManager $em, RouterInterface $router, UserPasswordEncoder $passwordEncoder) {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getCredentials(Request $request) {
        $isLoginSubmit = $request->getPathInfo() == '/login' && $request->isMethod('POST');
        if (!$isLoginSubmit) {
            // skip authentication
            return;
        }
        //creation du formulaire et traiement de la requete
        $form = $this->formFactory->create(LoginForm::class);
        $form->handleRequest($request);
        //mapping des données
        $data = $form->getData();
        //création de la session
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $data['_username']
        );
        return $data;
    }

    public function getUser($credentials, UserProviderInterface $userProvider) {
        $username = $credentials['_username'];
        //retourne notre utilisateur en utilisant le provider (UserProviderInterface $userProvider)
        //cf. security.yml : providers
        return $this->em->getRepository('AppBundle:User')->findOneBy(['email' => $username]);
    }

    public function checkCredentials($credentials, UserInterface $user) {
        $password = $credentials['_password'];
        //ici on vérifie que le mot de passe forunis et le mot passe connu en base sont les même 
        if ($this->passwordEncoder->isPasswordValid($user, $password)) {
            return true;
        }
        return false;
    }

    /**
     * Retourne l'URL de login
     * 
     * (non-PHPdoc)
     * @see \Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator::getLoginUrl()
     */
    protected function getLoginUrl() {
        return $this->router->generate('security_login');
    }

    /**
     * Retourne l'URL vers laquelle rediriger l'utilisateur en cas de succès du login
     */
    protected function getDefaultSuccessRedirectUrl() {
        return $this->router->generate('homepage');
    }
}