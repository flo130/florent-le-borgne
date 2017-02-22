<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Article;

/**
 * Cette class sert à l'admin des articles
 * 
 * @Security("is_granted('ROLE_ADMIN')")
 * 
 * @Route("/admin/article")
 */
class ArticleController extends Controller
{
    /**
     * Page de recherche d'un article
     *
     * @Route("/", name="admin_article"))
     *
     * @Method({"GET"})
     */
    public function articleAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:pages/admin:articlePage.html.twig', array(
            'articles' => $em->getRepository('AppBundle:Article')->findAll(),
        ));
    }

    /**
     * Page de suppression d'un article
     *
     * @Route("/delete/{slug}", name="admin_article_delete"))
     *
     * @Method({"GET"})
     *
     * @param Request $request
     * @param Article $article
     *
     * @return Response
     */
    public function deleteAction(Request $request, Article $article)
    {
        //vérifie qu'un utilisateur a le droit d'éditer l'article ("Voter" Symfony)
        /** @see AppBundle\Security\ArticleVoter */
        $this->denyAccessUnlessGranted('delete', $article);
        $em = $this->getDoctrine()->getManager();
        $this->get('logger')->notice('Article suppression', array('title' => $article->getTitle()));
        $em->remove($article);
        $em->flush();
        $this->addFlash('success', ucfirst(strtolower($this->get('translator')->trans('app.delete_success'))));
        return $this->redirect($this->generateUrl('admin_article'));
    }
}