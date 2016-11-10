<?php
namespace AppBundle\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\ArticleSubCategory;

class ArticleSubCategoryListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof ArticleSubCategory) {
            if (!$entity->getCreatedAt()) {
                $entity->setCreatedAt(new \DateTime());
            }
        }
    }
}