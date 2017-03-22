<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Cette class permet de tester des fonctionnalités de Symfony
 * 
 * @Route("/test")
 */
class TestController extends Controller
{
	/**
	 * @Route("/workflow", name="test_workflow"))
	 *
	 * @Method({"GET"})
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function workflowAction(Request $request)
	{
		$results = $this->get('app.workflow');
		
		dump($results);
		
		return new Response('');
	}
}