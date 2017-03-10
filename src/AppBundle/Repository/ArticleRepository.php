<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Article;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ArticleRepository extends EntityRepository
{
    /**
     * Retourne le nombre total d'articles
     * 
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
     * Retourne le nombre total d'articles qui sont publiés
     * 
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
     * Retourne le nombre total d'articles qui sont en brouillons
     * 
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
     * Retourne tous les articles triés par date de création
     *
     * @return Article[]
     */
    public function findAllOrderByCreatedDate()
    {
        return $this->createQueryBuilder('article')
            ->orderBy('article.createdAt', 'ASC')
            ->getQuery()
            ->execute();
    }

    /**
     * Retourne une requete paginée d'articles, triés par date de création
     * 
     * @param int $page
     * @param int $maxResults
     * 
     * @return Paginator
     */
    public function findAllWithPaginatorOrderByCreatedDate($page=1, $maxResults=10)
    {
        $query = $this->createQueryBuilder('article')
            ->setFirstResult(($page - 1) * $maxResults)
            ->setMaxResults($maxResults)
            ->orderBy('article.createdAt', 'ASC');
        $paginator = new Paginator($query);
        return $paginator;
    }

    /**
     * Retourne une requete paginée des articles en brouillon, triés par date de creation
     * 
     * @param int $page
     * @param int $maxResults
     * 
     * @return Paginator
     */
    public function findAllDraftWithPaginatorOrderByCreatedDate($page=1, $maxResults=10)
    {
        $query = $this->createQueryBuilder('article')
            ->setFirstResult(($page - 1) * $maxResults)
            ->setMaxResults($maxResults)
            ->andWhere('article.status = :published')
            ->setParameter('published', Article::DRAFT_STATUS)
            ->orderBy('article.createdAt', 'ASC');
        $paginator = new Paginator($query);
        return $paginator;
    }

    /**
     * Retourne une requete paginée des articles publiés, triés par date de publication
     * 
     * @param int $page
     * @param int $maxResults
     * 
     * @return Paginator
     */
    public function findAllPublishedWithPaginatorOrderByPublishedDate($page=1, $maxResults=10)
    {
        $query = $this->createQueryBuilder('article')
            ->setFirstResult(($page - 1) * $maxResults)
            ->setMaxResults($maxResults)
            ->andWhere('article.status = :published')
            ->setParameter('published', Article::PUBLISHED_STATUS)
            ->orderBy('article.publishedAt', 'ASC');
        $paginator = new Paginator($query);
        return $paginator;
    }

    /**
     * Retourne la liste des articles en brouillon, triés par date de creation
     * 
     * @return Article[]
     */
    public function findAllDraftOrderByCreatedDate()
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.status = :published')
            ->setParameter('published', Article::DRAFT_STATUS)
            ->orderBy('article.createdAt', 'ASC')
            ->getQuery()
            ->execute();
    }

    /**
     * Retourne X articles en brouillon, triés par date de creation
     * 
     * @param int $nb
     * 
     * @return Article[]
     */
    public function findXDraftOrderByCreatedDate($nb)
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.status = :published')
            ->setParameter('published', Article::PUBLISHED_STATUS)
            ->setMaxResults($nb)
            ->setFirstResult(0)
            ->orderBy('article.createdAt', 'ASC')
            ->getQuery()
            ->execute();
    }

    /**
     * Retourne tous les articles publiés triés par date de publication
     * 
     * @return Article[]
     */
    public function findAllPublishedOrderByPublishedDate()
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.status = :published')
            ->setParameter('published', Article::PUBLISHED_STATUS)
            ->orderBy('article.publishedAt', 'ASC')
            ->getQuery()
            ->execute();
    }

    /**
     * Retourne X articles publiés triés par date de publication
     * 
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
            ->orderBy('article.publishedAt', 'ASC')
            ->getQuery()
            ->execute();
    }

    /**
     * Retourne tous les articles publiés, liés à une catégorie, triés par date de publication
     * 
     * @param int $idCategory
     * 
     * @return Article[]
     */
    public function findPublishedByCategoryOrderByPublishedDate($idCategory)
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.status = :published')
            ->setParameter('published', Article::PUBLISHED_STATUS)
            ->andWhere('article.category = :idCategory')
            ->setParameter('idCategory', $idCategory)
            ->orderBy('article.publishedAt', 'ASC')
            ->getQuery()
            ->execute();
    }

    /**
     * Retourne tous les articles publiés, liés à un path de catégories, triés par date de publication.
     * Un path de catégories est une arborescence de categories
     *
     * @param int $idCategory
     *
     * @return Article[]
     */
    public function findPublishedByCategoryPathOrderByPublishedDate($categoryPath)
    {
    	$categories = array();
    	foreach ($categoryPath as $category) {
    		$categories = array_merge($categories, $this->findPublishedByCategoryOrderByPublishedDate($category->getId()));
        }
        return $categories;
    }
    
    /**
     * Retourne tous les articles liés à un utilisateur
     * 
     * @param int $idUser
     * 
     * @return Article[]
     */
    public function findAllByUser($idUser)
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.user = :idUser')
            ->setParameter('idUser', $idUser)
            ->orderBy('article.publishedAt', 'ASC')
            ->getQuery()
            ->execute();
    }

    /**
     * Recherche tous les articles en fonction d'un terme
     *
     * @param string $term
     *
     * @return Article[]
     */
    public function searchPublished($term)
    {
        return $this->createQueryBuilder('article')
            //cherche via des like dans le titre, le résumer et l'article
            ->orwhere('article.title LIKE :term')
            ->orWhere('article.summary LIKE :term')
            ->orWhere('article.article LIKE :term')
            //il faut que l'article soit publié...
            ->andWhere('article.status = :published')
            ->setParameter('term', '%' . $term . '%')
            ->setParameter('published', Article::PUBLISHED_STATUS)
            ->getQuery()
            ->execute();
    }
}