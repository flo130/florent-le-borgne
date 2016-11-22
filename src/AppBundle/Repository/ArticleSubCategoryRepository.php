<?php
namespace AppBundle\Repository;

use AppBundle\Entity\ArticleSubCategory;
use Doctrine\ORM\EntityRepository;

class ArticleSubCategoryRepository extends EntityRepository
{
    /**
     * Récupère toutes les sous-catégories triées par date de création
     * 
     * @return ArticleSubCategory[]
     */
    public function findAllOrderByCreatedDate()
    {
        return $this->createQueryBuilder('article_sub_category')
            ->orderBy('article_sub_category.createdAt', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * Récupère toutes les sous-catégories triées par ordre alphabétique
     * 
     * @return QueryBuilder
     */
    public function createAlphabeticalQueryBuilder()
    {
        return $this->createQueryBuilder('article_sub_category')
            ->orderBy('article_sub_category.articleSubCategory', 'ASC');
    }
}