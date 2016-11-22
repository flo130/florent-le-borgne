<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController extends Controller
{
    const MAX_ARTICLES_PER_PAGE = 6;
    const NB_LAST_ARTICLE = 5;


    /**
     * Permet la construction de la page d'accueil
     * 
     * @Route("/", defaults={"page"=1}, name="homepage"))
     * @Route("/article-page/{page}", name="homepagepaginate")
     * 
     * @param Request $request
     * @param int $page
     * 
     * @return Response || JsonResponse
     */
    public function indexAction(Request $request, $page=1)
    {
        $em = $this->getDoctrine()->getManager();

        $pagination = array(
            'page' => $page,
            'pages_count' => ceil($em->getRepository('AppBundle:Article')->countPublished() / self::MAX_ARTICLES_PER_PAGE),
            'page_name' => 'homepagepaginate',
            'ajax_callback' => true,
        );

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'content' => $this->renderView('AppBundle:blocs:articleTeaserList.html.twig', array(
                    'articles' => $em->getRepository('AppBundle:Article')->findAllPublishedWithPaginatorOrderByPublishedDate($page, self::MAX_ARTICLES_PER_PAGE),
                )),
                'pagination' => $this->renderView('AppBundle:blocs:pagination.html.twig', array(
                    'pagination' => $pagination,
                )),
            ));
        } else {
            return $this->render('AppBundle:pages:homePage.html.twig', array(
                'articles' => $em->getRepository('AppBundle:Article')->findAllPublishedWithPaginatorOrderByPublishedDate($page, self::MAX_ARTICLES_PER_PAGE),
                'articlesPagination' => $pagination,
                'categories' => $em->getRepository('AppBundle:ArticleCategory')->findAllOrderByCreatedDate(),
                'lastArticles' =>  $em->getRepository('AppBundle:Article')->findXPublishedOrderByPublishedDate(self::NB_LAST_ARTICLE),
            ));
        }

    }
}
