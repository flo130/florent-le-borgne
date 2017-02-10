<?php 
namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Firewall\ExceptionListener as BaseExceptionListener;

class ExceptionListener extends BaseExceptionListener
{
	/**
	 * {@inheritDoc}
	 * @see \Symfony\Component\Security\Http\Firewall\ExceptionListener::setTargetPath()
	 */
    protected function setTargetPath(Request $request)
    {
        //cette methode permet de conserver une url demander pour la réutiliser 
        //si jamais une action a empeché le chargement de la page

        //ex : je tente d'accéder à une page ou il faut etre authentifié, mais je ne le suis pas
        //     alors on me redirige vers le form de login et suite à mon login, je suis redirigé vers cette derniere page

        //ici on dit que si on est en ajax, on va ignorer le comportement par 
        //défaut de Symfony : il faudra cabler le comportement dans le controlleur

        //on dit donc de ne pas rediriger l'utilisateur si on est en ajax

        if ($request->isXmlHttpRequest()) {
            return;
        }

        parent::setTargetPath($request);
    }
}