<?php
namespace AppBundle\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\ArticleCategory;

class ArticleCategoryListener
{
    /**
     * Permet d'ajouter certaines données à une catégorie d'article 
     * lors de son enregistrement en base
     * 
     * @param LifecycleEventArgs $args
     * 
     * @return void || null
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