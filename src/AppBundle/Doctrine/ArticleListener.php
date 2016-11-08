<?php
namespace AppBundle\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\Article;

/**
 * Cette classe est délcarée dans service.yml et tagguée doctrine.event_subscriber
 */
class ArticleListener implements EventSubscriber
{
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
        //ici on veut écouter seulement les instance de Article
        if ($entity instanceof Article) {
            if (!$entity->getCreatedAt()) {
                $entity->setCreatedAt(new \DateTime());
            }
            $entity->setUpdatedAt(new \DateTime());
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Article) {
            $entity->setUpdatedAt(new \DateTime());

            // necessary to force the update to see the change
            $em = $args->getEntityManager();
            $meta = $em->getClassMetadata(get_class($entity));
            $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
        }
    }
}