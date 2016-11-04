<?php
namespace AppBundle\Listener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class LoginListener
{
    /** @var \Symfony\Component\Security\Core\TokenStorage */
    private $tokenStorage;
    
    /** @var \Doctrine\ORM\EntityManager */
    private $em;


    /**
     * @param TokenStorage $tokenStorage
     * @param Doctrine $doctrine
     */
    public function __construct(TokenStorage $tokenStorage, Doctrine $doctrine)
    {
        $this->tokenStorage = $tokenStorage;
        $this->em = $doctrine->getManager();
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
//         if ($this->securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
//             // user has just logged in
//         }
//         if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
//             // user has logged in using remember_me cookie
//         }

        $user = $event->getAuthenticationToken()->getUser();

        if (!$user->getFirstLogin()) {
            $user->setFirstLogin(new \DateTime());
        }
        $user->setLoginCount($user->getLoginCount() + 1);
        
        $this->em->persist($user);
        $this->em->flush();
    }
}