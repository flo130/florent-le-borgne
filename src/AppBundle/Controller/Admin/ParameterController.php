<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ParameterForm;

/**
 * Cette class sert à l'admin des parametres du site (CMS)
 * 
 * @Security("is_granted('ROLE_ADMIN')")
 * 
 * @Route("/admin/parameter")
 */
class ParameterController extends Controller
{
	/**
	 * Page des parametres
	 *
	 * @Route("/", name="admin_parameter"))
	 *
	 * @Method({"GET", "POST"})
	 */
	public function parameterAction(Request $request)
	{
		if ($request->isMethod('GET')) {
			$em = $this->getDoctrine()->getManager();
			$params = $em->getRepository('AppBundle:Parameter')->findAll();
			
			$paramsFormsTab = array();
			foreach ($params as $param) {
				$form = $this->createForm(ParameterForm::class, $param);
				$paramsFormsTab[] = $this->renderView('AppBundle:forms:parameterForm.html.twig', array(
					'form' => $form->createView(),
					'param' => $param,
				));
			}
			return $this->render('AppBundle:pages/admin:parameterPage.html.twig', array(
				'paramsFormsTab' => $paramsFormsTab,
			));
		}

		if ($request->isMethod('POST')) {
			
		}
	}
}