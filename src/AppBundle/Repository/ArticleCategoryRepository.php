<?php
namespace AppBundle\Repository;

use AppBundle\Entity\ArticleCategory;
use Doctrine\ORM\EntityRepository;

class ArticleCategoryRepository extends EntityRepository
{
    /**
     * @return ArticleCategory[]
     */
    public function findAllOrderByCreatedDate()
    {
        return $this->createQueryBuilder('article_category')
            ->orderBy('article_category.createdAt', 'DESC')
            ->getQuery()
            ->execute();
    }
}