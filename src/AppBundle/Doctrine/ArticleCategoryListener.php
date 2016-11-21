<?php
namespace AppBundle\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\ArticleCategory;

class ArticleCategoryListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $articleCategory = $args->getEntity();
        if (! $articleCategory instanceof ArticleCategory) {
            return;
        }

        if (! $articleCategory->getCreatedAt()) {
            $articleCategory->setCreatedAt(new \DateTime());
        }
    }
}