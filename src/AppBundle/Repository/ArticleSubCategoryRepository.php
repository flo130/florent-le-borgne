<?php
namespace AppBundle\Repository;

use AppBundle\Entity\ArticleSubCategory;
use Doctrine\ORM\EntityRepository;

class ArticleSubCategoryRepository extends EntityRepository
{
    /**
     * @return ArticleSubCategory[]
     */
    public function findAllOrderByCreatedDate()
    {
        return $this->createQueryBuilder('article_sub_category')
        ->orderBy('article_sub_category.createdAt', 'DESC')
        ->getQuery()
        ->execute();
    }
}