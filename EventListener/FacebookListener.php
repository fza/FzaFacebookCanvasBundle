<?php

namespace Fza\FacebookCanvasBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Fza\FacebookCanvasAppBundle\Entity\FacebookUserRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class FacebookListener
{
    private $container;
    private $session;
    private $facebookContext;
    private $facebookSession;
    private $facebookCheckChainHandler;
    private $facebookUser;
    private $entityManager;
    private $facebookUserEntityNamespace;

    /**
     * @param ContainerInterface $container
     * @param EntityManager      $em
     * @param string             $facebookUserEntityNamespace
     */
    public function __construct(ContainerInterface $container, EntityManager $em, $facebookUserEntityNamespace)
    {
        $this->container                   = $container;
        $this->session                     = $container->get('session');
        $this->facebookContext             = $container->get('facebook.context');
        $this->facebookSession             = $container->get('facebook.session');
        $this->facebookCheckChainHandler   = $container->get('fza_facebook_canvas_app.check.chainhandler');
        $this->entityManager               = $em;
        $this->facebookUserEntityNamespace = $facebookUserEntityNamespace;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $this->facebookContext->initialize($event->getRequest());

        $this->facebookUser = $this->getFacebookUser();

        if (null !== $this->facebookUser) {
            // Populate facebook user from database or create one if checks passed
            $this->facebookUser->setAccessToken($this->facebookContext->getAccessToken());
            $this->container->set('facebook.user', $this->facebookUser, 'request');

            // Read the user profile
            $this->facebookContext->readUserProfile();
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (null !== ($facebookSessionId = $this->facebookContext->getSessionId())) {
            $this->session->set('_fza_facebookapp_fb_session_id', $facebookSessionId);
        }

        if (null !== $this->facebookUser) {
            $this->entityManager->persist($this->facebookUser);
            $this->entityManager->flush();
        }
    }

    /**
     * @return object
     */
    private function getFacebookUser()
    {
        if (null === ($facebookUserId = $this->facebookContext->getUserId())) {
            return null;
        }

        /** @var FacebookUserRepository $facebookUserRepository */
        $facebookUserRepository = $this->entityManager->getRepository($this->facebookUserEntityNamespace);

        return $facebookUserRepository->findOrCreateUser($facebookUserId);
    }
}
