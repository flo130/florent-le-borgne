<?php
namespace AppBundle\Repository;

use AppBundle\Entity\ArticleComment;
use Doctrine\ORM\EntityRepository;

class ArticleCommentRepository extends EntityRepository
{
    /**
     * Récupère tous les commentaires d'article triés par date de création
     * 
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
     * Récupère tous les commentaires d'article liés à un article
     * 
     * @param int $idArticle
     * 
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
     * Récupère tous les commentaires d'articles liés à un utilisateur
     * 
     * @param int $idUser
     * 
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