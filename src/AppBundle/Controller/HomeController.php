<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Form\SearchForm;
use AppBundle\Entity\Category;
use AppBundle\AppBundle;

/**
 * Cette class sert à afficher la page d'accueil du site
 */
class HomeController extends Controller
{
    const NB_ARTICLES_PER_PAGE = 15;
    const NB_ARTICLES_IN_CAROUSEL = 5;


    /**
     * Permet la construction de la page d'accueil
     * 
     * @Route("/", defaults={"page"=1}, name="homepage"))
     * @Route("/article-page/{page}", name="homepagepaginate")
     * 
     * @Method({"GET"})
     * 
     * @param Request $request
     * @param int $page
     * 
     * @return Response || JsonResponse
     */
    public function homeAction(Request $request, $page=1)
    {
        $em = $this->getDoctrine()->getManager();
        //en ajax ou pas, on a toujours besoin de la pagination des articles
        $pagination = array(
            'page' => $page,
            'pages_count' => ceil($em->getRepository('AppBundle:Article')->countPublished() / self::NB_ARTICLES_PER_PAGE),
            'page_name' => 'homepagepaginate',
            'ajax_callback' => true,
        );
        //si on appel la home page en ajax, c'est qu'on veut raffraichir la liste des articles seulement
        //sinon c'est qu'on veut toute la page
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'content' => $this->renderView('AppBundle:blocs:articleTeaserList.html.twig', array(
                    'articles' => $em->getRepository('AppBundle:Article')->findAllPublishedWithPaginatorOrderByUpdatedDateDesc($page, self::NB_ARTICLES_PER_PAGE),
                    'printArticleImage' => true,
                )),
                'pagination' => $this->renderView('AppBundle:blocs:pagination.html.twig', array(
                    'pagination' => $pagination,
                )),
            ));
        } else {
            return $this->render('AppBundle:pages:homePage.html.twig', array(
                'isActiveCarousel' => $em->getRepository('AppBundle:Parameter')->findOneByKey('carousel')->getIsActive(),
                'articles' => $em->getRepository('AppBundle:Article')->findAllPublishedWithPaginatorOrderByUpdatedDateDesc($page, self::NB_ARTICLES_PER_PAGE),
                'articlesPagination' => $pagination,
                'lastArticles' =>  $em->getRepository('AppBundle:Article')->findXPublishedOrderByPublishedDateDesc(self::NB_ARTICLES_IN_CAROUSEL),
                'searchForm' => $this->createForm(SearchForm::class)->createView(),
            ));
        }
    }
}
