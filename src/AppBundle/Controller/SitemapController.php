<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Cette class gere le sitemap
 */
class SitemapController extends Controller
{
    /**
     * Retourne le sitemap
     * Cette route est configurée dans le fichier de routing de l'application
     * 
     * @Method({"GET"})
     *
     * @return Response
     */
    public function sitemapAction()
    {
        $this->get('logger')->notice('Accès au sitemap');
        $urls = [];
        $categories = $this->getCategoriesUrl();
        $articles = $this->getArticlesUrl();
        return $this->render('AppBundle:sitemap:sitemap.xml.twig', array(
            'urls' => array_merge($articles, $categories),
        ));
    }

    /**
     * Retourne un tableau contenant les infos des catégories utiles pour générer le fichier du sitemap
     *
     * @return array
     */
    private function getCategoriesUrl()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('AppBundle:Category')->findAll();
        $urls = [];
        foreach ($categories as $categorie) {
            $sitemap = [];
            $sitemap['loc'] =  $this->generateUrl('article_by_category', array('slug' => $categorie->getSlug()), UrlGeneratorInterface::ABSOLUTE_URL);
            $sitemap['lastmod'] = $categorie->getCreatedAt()->format('Y-m-d');
            $sitemap['changefreq'] = 'monthly';
            $sitemap['priority'] = '0.8';
            $urls[] = $sitemap;
        }
        return $urls;
    }

    /**
     * Retourne un tableau contenant les infos des articles utiles pour générer le fichier du sitemap
     * 
     * @return array
     */
    private function getArticlesUrl()
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('AppBundle:Article')->findAll();
        $urls = [];
        foreach ($articles as $article) {
            //génération de l'url de l'article en Français (langue par défaut)
            $sitemap = [];
            $sitemap['loc'] =  $this->generateUrl('article_show', array('slug' => $article->getSlug()), UrlGeneratorInterface::ABSOLUTE_URL);
            $sitemap['lastmod'] = $article->getUpdatedAt()->format('Y-m-d');
            $sitemap['changefreq'] = 'weekly';
            $sitemap['priority'] = '0.5';
            $urls[] = $sitemap;
            //génération de l'url de l'article en Anglais (renseigne la locale)
            $sitemap = [];
            $sitemap['loc'] =  $this->generateUrl('article_show', array(
                'slug' => $article->getSlug(),
                '_locale' => 'en',
            ),
            UrlGeneratorInterface::ABSOLUTE_URL);
            $sitemap['lastmod'] = $article->getUpdatedAt()->format('Y-m-d');
            $sitemap['changefreq'] = 'weekly';
            $sitemap['priority'] = '0.5';
            $urls[] = $sitemap;
        }
        return $urls;
    }
}