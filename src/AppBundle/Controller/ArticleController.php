<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ArticleCommentFormType;
use AppBundle\Entity\Article;
use AppBundle\Entity\ArticleCategory;
use AppBundle\Entity\ArticleSubCategory;

/**
 * @Route("/article")
 */
class ArticleController extends Controller
{
    /**
     * @Route("/{id}", name="article"))
     */
    public function ArticleAction(Article $article)
    {
        return $this->render('AppBundle:pages:articlePage.html.twig', array(
            'commentForm' => $form = $this->createForm(ArticleCommentFormType::class)->createView(),
            'article' => $article,
        ));
    }

    /**
     * Récupère tous les articles appartenants à une catégorie
     * 
     * @Route("/category/{id}", name="article_by_category"))
     */
    public function ArticleByCategoryAction(ArticleCategory $articleCategory)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:pages:articleCategoryListPage.html.twig', array(
            'articleCategory' => $articleCategory,
            'articles' => $em->getRepository('AppBundle:Article')->findPublishedByCategoryOrderByPublishedDate($articleCategory->getId()),
            'categories' => $em->getRepository('AppBundle:ArticleCategory')->findAllOrderByCreatedDate(),
        ));
    }

    /**
     * Récupère tous les articles appartenants à une sous-catégorie
     *
     * @Route("/sub-category/{id}", name="article_by_sub_category"))
     */
    public function ArticleBySubCategoryAction(ArticleSubCategory $articleSubCategory)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:pages:articleSubCategoryListPage.html.twig', array(
            'articleSubCategory' => $articleSubCategory,
            'articles' => $em->getRepository('AppBundle:Article')->findPublishedBySubCategoryOrderByPublishedDate($articleSubCategory->getId()),
            'categories' => $em->getRepository('AppBundle:ArticleCategory')->findAllOrderByCreatedDate(),
        ));
    }

    /**
     * @Route("/{id}/edit", name="article_edit"))
     */
    public function ArticleEditAction(Request $request, Article $article)
    {
        die(dump($article));
    }
}