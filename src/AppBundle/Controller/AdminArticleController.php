<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use AppBundle\Entity\Article;

/**
 * Cette class sert à l'admin des articles
 * 
 * @Security("is_granted('ROLE_ADMIN')")
 * 
 * @Route("/admin/article")
 */
class AdminArticleController extends Controller
{
    /**
     * Page de recherche d'un article
     *
     * @Route("/search", name="admin_article_search"))
     *
     * @Method({"GET", "POST"})
     */
    public function SearchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:pages/admin:searchArticlePage.html.twig', array(
            'articles' => $em->getRepository('AppBundle:Article')->findAll(),
        ));
    }

    /**
     * Page d'édition d'un article
     *
     * @Route("/edit/{id}", name="admin_article_edit"))
     *
     * @Method({"GET", "POST"})
     *
     * @param Article $article
     */
    public function EditAction(Request $request, Article $article)
    {
        die('admin_article_edit');
    }

    /**
     * Page de suppression d'un article
     *
     * @Route("/delete/{id}", name="admin_article_delete"))
     *
     * @Method({"GET"})
     *
     * @param Article $article
     */
    public function DeleteAction(Request $request, Article $article)
    {
        die('admin_article_delete');
    }
}