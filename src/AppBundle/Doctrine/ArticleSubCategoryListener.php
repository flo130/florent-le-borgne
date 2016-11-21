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
        $articleSubCategory = $args->getEntity();
        if (! $articleSubCategory instanceof ArticleSubCategory) {
            return;
        }

        if (! $articleSubCategory->getCreatedAt()) {
        	$articleSubCategory->setCreatedAt(new \DateTime());
        }
    }
}