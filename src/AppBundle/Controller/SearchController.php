<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\SearchForm;

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
	 * @param string $term
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
				$term = $search->getTerm();
				$results = $this->getDoctrine()
					->getManager()
					->getRepository('AppBundle:Article')
					->searchPublishedOrderByUpdatedDateDesc($term);
			}
		}
		return $this->render('AppBundle:pages:searchPage.html.twig', array(
			'searchResults' => $results,
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