<?php
namespace AppBundle\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\User;

class UserListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof User) {
            if (!$entity->getCreatedAt()) {
                $entity->setCreatedAt(new \DateTime());
            }
        }
    }
}