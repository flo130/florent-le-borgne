<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
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
		if ($form->isSubmitted()) {
			$isValid = $form->isValid();
			if ($isValid) {
				//recherche les articles
				$data = $form->getData();
				$em = $this->getDoctrine()->getManager();
				$results = $em->getRepository('AppBundle:Article')->searchPublished($data['search']);
			}
		}
		return $this->render('AppBundle:pages:searchPage.html.twig', array(
			'searchForm' => $form->createView(),
			'searchResults' => $results,
		));
	}
}