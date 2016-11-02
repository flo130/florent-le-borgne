<?php
namespace AppBundle\Doctrine;

use AppBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

/**
 * Cette class permet d'écouter des event générés par Doctrine. Elle va permettre de hasher le password.
 * 
 * Les events à écouter sont définits par la méthode getSubscribedEvents
 * qui doit être codée puisqu'on implemente l'interface EventSubscriber
 * 
 * Cette classe est délcarée dans service.yml et tagguée doctrine.event_subscriber
 */
class HashPasswordListener implements EventSubscriber
{
    private $passwordEncoder;


    public function __construct(UserPasswordEncoder $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Event Doctrine que l'ont veut écouter
     * 
     * (non-PHPdoc)
     * @see \Doctrine\Common\EventSubscriber::getSubscribedEvents()
     */
    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate'];
    }

    /**
     * prePersist est un évenement déclanché par Doctrine juste avant de persister une requete SQL en base
     * 
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        //ici on veut écouter seulement les instance de User, le reste, on "return" (on en veut pas)
        if (!$entity instanceof User) {
            return;
        }

        //encode le mot de passe
        $this->encodePassword($entity);
    }

    /**
     * @param User $entity
     */
    private function encodePassword(User $entity)
    {
        if (!$entity->getPlainPassword()) {
            return;
        }

        $encoded = $this->passwordEncoder->encodePassword(
            $entity,
            $entity->getPlainPassword()
        );

        $entity->setPassword($encoded);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof User) {
            return;
        }

        $this->encodePassword($entity);

        // necessary to force the update to see the change
        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(get_class($entity));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
    }
}