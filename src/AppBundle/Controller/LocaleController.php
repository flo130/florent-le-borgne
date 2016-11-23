<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Cette class sert à gérer le passage entre les différentes locales (en / fr)
 *
 * @Route("/locale")
 */
class LocaleController extends Controller
{
    /**
     * Change la locale en session
     *
     * @Route("/{locale}", name="locale_change"))
     * 
     * @Method({"GET"})
     *
     * @param Request $request
     * @param string $locale
     * 
     * @return Response || JsonResponse
     */
    public function changeAction(Request $request, $locale) 
    {
        $request->getSession()->set('_locale', $locale);
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'locale' => $locale
            ), 200);
        } else {
            return $this->redirect($this->generateUrl('homepage'));
        }
    }
}