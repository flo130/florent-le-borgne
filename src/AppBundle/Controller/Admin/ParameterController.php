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
        $em = $this->getDoctrine()->getManager();
        //soumission d'un formulaire de parametre
        if ($request->isMethod('POST')) {
            $values = $request->get('parameter_form', null);
            $parameter = $em->getRepository('AppBundle:Parameter')->find($values['id']);
            $parameter->setIsActive(isset($values['isActive'])?1:0);
            $em->persist($parameter);
            $em->flush();
            $this->addFlash('success', ucfirst(strtolower($this->get('translator')->trans('app.update_success'))));
            return $this->redirect($this->generateUrl('admin_parameter'));
        }
        //affichage de la page contenant les formulaires des parametres
        if ($request->isMethod('GET')) {
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
    }
}