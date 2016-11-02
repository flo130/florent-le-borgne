<?php
namespace AppBundle\Repository;

use AppBundle\Entity\ArticleComment;
use Doctrine\ORM\EntityRepository;

class ArticleCommentRepository extends EntityRepository
{
    /**
     * @return ArticleComment[]
     */
    public function findAllOrderByCreatedDate()
    {
        return $this->createQueryBuilder('article_comment')
            ->orderBy('article_comment.createdAt', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * @return ArticleComment[]
     */
    public function findByArticleIdOrderByCreatedDate($idArticle)
    {
        return $this->createQueryBuilder('article_comment')
            ->andWhere('article_comment.article = :idArticle')
            ->setParameter('idArticle', $idArticle)
            ->orderBy('article_comment.createdAt', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * @return ArticleComment[]
     */
    public function findByUserIdOrderByCreatedDate($idUser)
    {
        return $this->createQueryBuilder('article_comment')
            ->andWhere('article_comment.user = :idArticle')
            ->setParameter('idArticle', $idUser)
            ->orderBy('article_comment.createdAt', 'DESC')
            ->getQuery()
            ->execute();
    }
}