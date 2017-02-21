<?php
namespace AppBundle\Listener;

use AppBundle\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class HashPasswordListener
{
    /**
     * @var UserPasswordEncoder
     */
    private $passwordEncoder;


    /**
     * @param UserPasswordEncoder $passwordEncoder
     */
    public function __construct(UserPasswordEncoder $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * prePersist est un évenement déclanché par Doctrine juste avant de persister une requete SQL en base
     * 
     * @param LifecycleEventArgs $args
     * 
     * @return void || null
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof User) {
            return;
        }
        $this->encodePassword($entity);
    }

    /**
     * prePersist est un évenement déclanché par Doctrine juste avant d'updater une entity en base
     * 
     * @param LifecycleEventArgs $args
     * 
     * @return void || null
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $user = $args->getEntity();
        if (!$user instanceof User) {
            return;
        }
        $this->encodePassword($user);
        //obligatoire pour forcer l'update et voir les changement
        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(get_class($user));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $user);
    }

    /**
     * Permet l'encodage d'un mot de passe
     *
     * @param User $entity
     *
     * @return void || null
     */
    private function encodePassword(User $user)
    {
        if (!$user->getPlainPassword()) {
            return;
        }
        $encoded = $this->passwordEncoder->encodePassword(
            $user,
            $user->getPlainPassword()
        );
        $user->setPassword($encoded);
    }
}