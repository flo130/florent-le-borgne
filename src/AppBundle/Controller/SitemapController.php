<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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
    	
    	
    	/*
    	loc          URL (avec http://)
    	lastmod      AAAA-MM-JJ
    	changefreq   weekly (article)   monthly (categories)
    	priority     0.5 (article) 0.8 (categories)
    	*/
    	
    	
        return $this->render('AppBundle:sitemap:sitemap.xml.twig', array(
            'urls' => $url,
        ));
    }
}