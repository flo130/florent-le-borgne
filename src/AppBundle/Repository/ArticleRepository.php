<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Article;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ArticleRepository extends EntityRepository
{
    /**
     * @return int
     */
    public function countAll() 
    {
        return $this->createQueryBuilder('article')
            ->select('COUNT(article)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return int
     */
    public function countPublished()
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.status = :published')
            ->setParameter('published', Article::PUBLISHED_STATUS)
            ->select('COUNT(article)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return int
     */
    public function countDraft()
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.status = :published')
            ->setParameter('published', Article::DRAFT_STATUS)
            ->select('COUNT(article)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param int $page
     * @param int $maxResults
     * @return Paginator
     */
    public function findAllWithPaginatorOrderByCreatedDate($page=1, $maxResults=10)
    {
        $query = $this->createQueryBuilder('article')
            ->setFirstResult(($page - 1) * $maxResults)
            ->setMaxResults($maxResults)
            ->orderBy('article.createdAt', 'DESC');
        $paginator = new Paginator($query);
        return $paginator;
    }

    /**
     * @return Article[]
     */
    public function findAllOrderByCreatedDate()
    {
        return $this->createQueryBuilder('article')
            ->orderBy('article.createdAt', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $page
     * @param int $maxResults
     * @return Paginator
     */
    public function findAllDraftWithPaginatorOrderByCreatedDate($page=1, $maxResults=10)
    {
        $query = $this->createQueryBuilder('article')
            ->setFirstResult(($page - 1) * $maxResults)
            ->setMaxResults($maxResults)
            ->andWhere('article.status = :published')
            ->setParameter('published', Article::DRAFT_STATUS)
            ->orderBy('article.publishedAt', 'DESC');
        $paginator = new Paginator($query);
        return $paginator;
    }

    /**
     * @param int $page
     * @param int $maxResults
     * @return Paginator
     */
    public function findAllPublishedWithPaginatorOrderByCreatedDate($page=1, $maxResults=10)
    {
        $query = $this->createQueryBuilder('article')
            ->setFirstResult(($page - 1) * $maxResults)
            ->setMaxResults($maxResults)
            ->andWhere('article.status = :published')
            ->setParameter('published', Article::PUBLISHED_STATUS)
            ->orderBy('article.publishedAt', 'DESC');
        $paginator = new Paginator($query);
        return $paginator;
    }

    /**
     * @param int $idSubCategory
     * @return Article[]
     */
    public function findBySubCategoryIdOrderByCreatedDate($idSubCategory)
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.articleSubCategory = :idSubCategory')
            ->setParameter('idSubCategory', $idSubCategory)
            ->orderBy('article.createdAt', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * @return Article[]
     */
    public function findAllDraftOrderByPublishedDate()
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.status = :published')
            ->setParameter('published', Article::DRAFT_STATUS)
            ->orderBy('article.publishedAt', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $nb
     * @return Article[]
     */
    public function findXDraftOrderByPublishedDate($nb)
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.status = :published')
            ->setParameter('published', Article::PUBLISHED_STATUS)
            ->setMaxResults($nb)
            ->setFirstResult(0)
            ->orderBy('article.publishedAt', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * @return Article[]
     */
    public function findAllPublishedOrderByPublishedDate()
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.status = :published')
            ->setParameter('published', Article::PUBLISHED_STATUS)
            ->orderBy('article.publishedAt', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $nb
     * @return Article[]
     */
    public function findXPublishedOrderByPublishedDate($nb)
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.status = :published')
            ->setParameter('published', Article::PUBLISHED_STATUS)
            ->setMaxResults($nb)
            ->setFirstResult(0)
            ->orderBy('article.publishedAt', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $idCategory
     * @return Article[]
     */
    public function findPublishedByCategoryOrderByPublishedDate($idCategory)
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.status = :published')
            ->setParameter('published', Article::PUBLISHED_STATUS)
            ->andWhere('article.articleCategory = :idCategory')
            ->setParameter('idCategory', $idCategory)
            ->orderBy('article.publishedAt', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $idCategory
     * @return Article[]
     */
    public function findPublishedBySubCategoryOrderByPublishedDate($idSubCategory)
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.status = :published')
            ->setParameter('published', Article::PUBLISHED_STATUS)
            ->andWhere('article.articleSubCategory = :idSubCategory')
            ->setParameter('idSubCategory', $idSubCategory)
            ->orderBy('article.publishedAt', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $idUser
     * @return Article[]
     */
    public function findAllByUser($idUser)
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.user = :idUser')
            ->setParameter('idUser', $idUser)
            ->getQuery()
            ->execute();
    }
}