<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Article;
use AppBundle\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query;

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
        //on rajoute aussi les éléments de la catégorie en cours
        $categories = array_merge($categories, $this->findPublishedByCategoryOrderByUpdatedDateDesc($category->getId()));
        //on récupère les éléments des catégories enfants
        foreach ($childrenCategory as $childCategory) {
            //on les ajoute dans un tableau qui sera retourné
            $categories = array_merge($categories, $this->findPublishedByCategoryOrderByUpdatedDateDesc($childCategory->getId()));
        }
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
     * Retourne tous les articles brouillons liés à un utilisateur trié par date de mise à jour
     *
     * @param int $idUser
     *
     * @return Article[]
     */
    public function findDraftByUserOrderByUpdatedDateDesc($idUser)
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.user = :idUser')
            ->andWhere('article.status = :published')
            ->setParameter('idUser', $idUser)
            ->setParameter('published', Article::DRAFT_STATUS)
            ->orderBy('article.updatedAt', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * Retourne tous les articles publiés liés à un utilisateur trié par date de mise à jour
     *
     * @param int $idUser
     *
     * @return Article[]
     */
    public function findPublishedByUserOrderByUpdatedDateDesc($idUser)
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.user = :idUser')
            ->andWhere('article.status = :published')
            ->setParameter('idUser', $idUser)
            ->setParameter('published', Article::PUBLISHED_STATUS)
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
    public function searchPublishedOrderByUpdatedDateDesc($term, $locale)
    {
        $query = $this->createQueryBuilder('a')
            //cherche via des like dans le titre, le résumer et l'article
            ->orwhere('a.title LIKE :term')
            ->orWhere('a.summary LIKE :term')
            ->orWhere('a.article LIKE :term')
            //il faut que l'article soit publié...
            ->andWhere('a.status = :published')
            //on ordonne par date de mise à jour 
            ->orderBy('a.updatedAt', 'DESC')
            //on set les paramètres
            ->setParameter('term', '%' . $term . '%')
            ->setParameter('published', Article::PUBLISHED_STATUS)
            ->getQuery();
        //gestion de la locale
        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        $query->setHint(
            \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
            $locale
        );
        $query->setHint(Query::HINT_REFRESH, true);
        return $query->execute();
    }
}