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
    const MAX_ARTICLES_PER_PAGE = 6;
    const NB_LAST_ARTICLE = 5;


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
            'pages_count' => ceil($em->getRepository('AppBundle:Article')->countPublished() / self::MAX_ARTICLES_PER_PAGE),
            'page_name' => 'homepagepaginate',
            'ajax_callback' => true,
        );

        //génère un arbre des catégories 
        //on construit les options : 
        //    "rootOpen / rootClose" : va encadrer tous les enfants d'un parents
        //    "nodeDecorator" : permet de personnaliser l'élément
        //https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/tree.md
        //https://jsfiddle.net/ann7tctp/
        $repo = $em->getRepository('AppBundle:Category');
        $parent = 0;
        $options = array(
            'decorate' => true,
            'rootOpen' => function($tree) use (&$parent) { 
                if (count($tree) && ($tree[0]['lvl'] != 0)) {
                    return '<div class="list-group collapse" id="item-' . $parent . '">';
                }
            },
            'rootClose' => function($child) { 
                if (count($child) && ($child[0]['lvl'] != 0)) {
                    return '</div>';
                }
            },
            'childOpen' => '',
            'childClose' => '',
            'nodeDecorator' => function($node) use (&$parent) {
                //cherche les élements qui ont des enfants pour leur appliquer un comportement particulier
                $nbChilds = count($node['__children']);
                if ($nbChilds > 0) {
                    $parent = $node['id'];
                    return '<a href="#item-' . $parent . '" class="list-group-item" data-toggle="collapse"><i class="glyphicon glyphicon-chevron-right"></i>' . ucfirst($node['title']) . '<span class="badge">' . $nbChilds . '</span></a>';
                } else {
                   return '<a href="' . $this->generateUrl('article_by_category', array('slug' => $node['slug'])) . '" class="list-group-item">' . ucfirst($node['title']) . '</a>';
                }
            },
        );
        $htmlTree = $repo->childrenHierarchy(
            null,
            false,
            $options
        );

        //si on appel la home page en ajax, c'est qu'on veut raffraichir la liste des articles seulement
        //sinon c'est qu'on veut toute la page
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'content' => $this->renderView('AppBundle:blocs:articleTeaserList.html.twig', array(
                    'articles' => $em->getRepository('AppBundle:Article')->findAllPublishedWithPaginatorOrderByPublishedDate($page, self::MAX_ARTICLES_PER_PAGE),
                    'printArticleImage' => true,
                )),
                'pagination' => $this->renderView('AppBundle:blocs:pagination.html.twig', array(
                    'pagination' => $pagination,
                )),
            ));
        } else {
            return $this->render('AppBundle:pages:homePage.html.twig', array(
                'articles' => $em->getRepository('AppBundle:Article')->findAllPublishedWithPaginatorOrderByPublishedDate($page, self::MAX_ARTICLES_PER_PAGE),
                'articlesPagination' => $pagination,
                'categoriesTree' => $htmlTree,
                'lastArticles' =>  $em->getRepository('AppBundle:Article')->findXPublishedOrderByPublishedDate(self::NB_LAST_ARTICLE),
                'searchForm' => $this->createForm(SearchForm::class)->createView(),
            ));
        }
    }
}
