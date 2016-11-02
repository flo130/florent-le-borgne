<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    const MAX_ARTICLES_PER_PAGE = 6;
    const NB_LAST_ARTICLE = 5;


    /**
     * @Route("/", defaults={"page"=1}, name="homepage"))
     * @Route("/{page}", name="homepagepaginate")
     */
    public function indexAction(Request $request, $page=1)
    {
        $em = $this->getDoctrine()->getManager();

        $articlesPagination = array(
            'page' => $page,
            'pages_count' => ceil($em->getRepository('AppBundle:Article')->countPublished() / self::MAX_ARTICLES_PER_PAGE),
            'page_name' => 'homepagepaginate',
        );

        return $this->render('AppBundle:pages:homePage.html.twig', array(
            'articles' => $em->getRepository('AppBundle:Article')->findAllPublishedWithPaginatorOrderByCreatedDate($page, self::MAX_ARTICLES_PER_PAGE),
            'articlesPagination' => $articlesPagination,
            'categories' => $em->getRepository('AppBundle:ArticleCategory')->findAllOrderByCreatedDate(),
            'lastArticles' =>  $em->getRepository('AppBundle:Article')->findXPublishedOrderByPublishedDate(self::NB_LAST_ARTICLE),
        ));
    }
}
