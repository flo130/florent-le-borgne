<?php
namespace AppBundle\Listener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class LoginListener
{
    /** 
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;


    /**
     * @param Doctrine $doctrine
     */
    public function __construct(Doctrine $doctrine)
    {
        $this->em = $doctrine->getManager();
    }

    /**
     * Ajoute des données à l'entity au moment d'un login réussi
     * 
     * @param InteractiveLoginEvent $event
     * 
     * @return void
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if (!$user->getFirstLogin()) {
            $user->setFirstLogin(new \DateTime());
        }
        $user->setLoginCount($user->getLoginCount() + 1);
        $this->em->persist($user);
        $this->em->flush();
    }
}