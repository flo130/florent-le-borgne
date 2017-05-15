<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\SearchForm;
use AppBundle\Entity\Search;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Cette class gère la partie recherche du site
 * 
 * @Route("/search")
 */
class SearchController extends Controller
{
	/**
	 * Permet d'afficher la page de recherche
	 *
	 * @Route("/", name="search"))
	 *
	 * @Method({"GET", "POST"})
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function searchAction(Request $request)
	{
		$form = $this->createForm(SearchForm::class);
		$form->handleRequest($request);
		$results = null;
		$term = null;
		if ($form->isSubmitted()) {
			if ($form->isValid()) {
				/** @var AppBundle\Entity\Search **/
				$search = $form->getData();
				return $this->redirect($this->generateUrl('search_with_term', array(
					'term' => urlencode($search->getTerm()),
				), UrlGeneratorInterface::ABSOLUTE_URL));
			}
		}
		return $this->render('AppBundle:pages:searchPage.html.twig', array(
			'searchResults' => null,
			'term' => $term,
		));
	}

	/**
	 * Permet d'afficher la page de recherche avec un terme dans l'URL
	 *
	 * @Route("/{term}", name="search_with_term"))
	 *
	 * @Method({"GET"})
	 *
	 * @param Request $request
	 * @param string $term
	 *
	 * @return Response
	 */
	public function searchWithTermAction(Request $request, $term)
	{
		return $this->render('AppBundle:pages:searchPage.html.twig', array(
			'searchResults' => $this->getDoctrine()
				->getManager()
				->getRepository('AppBundle:Article')
				->searchPublishedOrderByUpdatedDateDesc(urldecode($term), $request->getLocale()),
			'term' => $term,
		));
	}

	/**
	 * Retourne le HTML du formulaire de recherche.
	 * Cette action n'a pas de route, elle n'est donc pas visible depuis l'extérieur, elle 
	 * sert juste a être appelée dans les templates
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function getFormAction(Request $request)
	{
		return $this->render('AppBundle:forms:searchForm.html.twig', array(
			'form' => $this->createForm(SearchForm::class)->createView(),
		));
	}
}