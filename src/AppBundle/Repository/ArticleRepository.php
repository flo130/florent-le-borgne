<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Article;
use AppBundle\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ArticleRepository extends EntityRepository
{
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
     * Retourne une requete paginée des articles publiés, triés par date de mise à jour
     *
     * @param int $page
     * @param int $maxResults
     *
     * @return Paginator
     */
    public function findAllPublishedWithPaginatorOrderByUpdatedDateDesc($page=1, $maxResults=10)
    {
        $query = $this->createQueryBuilder('article')
            ->setFirstResult(($page - 1) * $maxResults)
            ->setMaxResults($maxResults)
            ->andWhere('article.status = :published')
            ->setParameter('published', Article::PUBLISHED_STATUS)
            ->orderBy('article.updatedAt', 'DESC');
        $paginator = new Paginator($query);
        return $paginator;
    }

    /**
     * Retourne X articles publiés triés par date de publication
     * 
     * @param int $nb
     * @return Article[]
     */
    public function findXPublishedOrderByPublishedDateDesc($nb)
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
     * Retourne tous les articles publiés, liés à une catégorie, triés par date de mise à jour
     * 
     * @param int $idCategory
     * 
     * @return Article[]
     */
    public function findPublishedByCategoryOrderByUpdatedDateDesc($idCategory)
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.status = :published')
            ->setParameter('published', Article::PUBLISHED_STATUS)
            ->andWhere('article.category = :idCategory')
            ->setParameter('idCategory', $idCategory)
            ->orderBy('article.updatedAt', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * Retourne tous les articles publiés, liés à un path de catégories, triés par date de mise à jour
     *
     * @param array $childrenCategory
     * @param Category $category
     *
     * @return Article[]
     */
    public function findPublishedByCategoriesOrderByUpdatedDateDesc($childrenCategory, Category $category)
    {
        $categories = array();
        //on récupère les éléments des catégories enfants
        foreach ($childrenCategory as $childCategory) {
            //on les ajoute dans un tableau qui sera retourné
            $categories = array_merge($categories, $this->findPublishedByCategoryOrderByUpdatedDateDesc($childCategory->getId()));
        }
        //on rajoute aussi les éléments de la catégorie en cours
        $categories = array_merge($categories, $this->findPublishedByCategoryOrderByUpdatedDateDesc($category->getId()));
        return $categories;
    }
    
    /**
     * Retourne tous les articles liés à un utilisateur trié par date de mise à jour
     * 
     * @param int $idUser
     * 
     * @return Article[]
     */
    public function findAllByUserOrderByUpdatedDateDesc($idUser)
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.user = :idUser')
            ->setParameter('idUser', $idUser)
            ->orderBy('article.updatedAt', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * Recherche tous les articles en fonction d'un terme donné
     *
     * @param string $term
     *
     * @return Article[]
     */
    public function searchPublishedOrderByUpdatedDateDesc($term)
    {
        return $this->createQueryBuilder('article')
            //cherche via des like dans le titre, le résumer et l'article
            ->orwhere('article.title LIKE :term')
            ->orWhere('article.summary LIKE :term')
            ->orWhere('article.article LIKE :term')
            //il faut que l'article soit publié...
            ->andWhere('article.status = :published')
            //on ordonne par date de mise à jour 
            ->orderBy('article.updatedAt', 'DESC')
            //on set les paramètres
            ->setParameter('term', '%' . $term . '%')
            ->setParameter('published', Article::PUBLISHED_STATUS)
            ->getQuery()
            ->execute();
    }
}