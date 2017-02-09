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
    public function ArticleAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:pages/admin:articlePage.html.twig', array(
            'articles' => $em->getRepository('AppBundle:Article')->findAll(),
        ));
    }
}