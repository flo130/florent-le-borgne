<?php
namespace AppBundle\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\ArticleSubCategory;

class ArticleSubCategoryListener
{
    /**
     * Permet d'ajouter certaines données à une sous-catégorie d'article 
     * lors de son enregistrement en base
     * 
     * @param LifecycleEventArgs $args
     * 
     * @return void
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