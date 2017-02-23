<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
	 * @Method({"GET"})
	 */
	public function parameterAction(Request $request)
	{
		die('ffffffffff');
	}
}