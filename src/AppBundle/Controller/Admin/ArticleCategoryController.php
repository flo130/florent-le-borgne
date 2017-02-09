<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\ArticleCategory;

/**
 * Cette class sert à l'admin des categories
 * 
 * @Security("is_granted('ROLE_ADMIN')")
 * 
 * @Route("/admin/category")
 */
class ArticleCategoryController extends Controller
{
    /**
     * Page de recherche d'une categorie
     *
     * @Route("/", name="admin_category"))
     *
     * @Method({"GET"})
     */
    public function CategoryAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:pages/admin:articleCategoryPage.html.twig', array(
            'articleCategories' => $em->getRepository('AppBundle:ArticleCategory')->findAll(),
        ));
    }
}