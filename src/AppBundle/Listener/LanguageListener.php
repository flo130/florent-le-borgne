<?php
namespace AppBundle\Listener;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class LanguageListener
{
    /**
     * @var string
     */
    private $defaultLocale;


    /**
     * @param string $defaultLocale
     */
    public function __construct($defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * Set la locale de l'utilisateur
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        //MASTER_REQUEST est une requete qui vient directement du client
        //Les autres (SUB_REQUEST) viennent d'appel en interne (forward()...)
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        } else {
            $request->setLocale(
                $request->getSession()->get(
                    '_locale', 
                    $request->getPreferredLanguage(array(
                        'en', 
                        'fr',
                    ))
                )
            );
        }
    }
}