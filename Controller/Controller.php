<?php

namespace Fza\FacebookCanvasAppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Controller extends BaseController
{
    public function redirectToApp()
    {
        return new Response( '<script>top.location.href="' . $this->getAppUri() . '";</script>' );
    }

    public function getAppUri( $withScheme = true )
    {
        $facebookContext = $this->get( 'facebook.context' );

        $uri  = $withScheme ? $this->getRequest()->getScheme() . '://' : '';
        $uri .= 'www.facebook.com/' . $facebookContext->getPageId() . '?sk=app_' . $facebookContext->getAppId();

        return $uri;
    }

    public function getFacebookUser()
    {
        return $this->get( 'facebook.user' );
    }
}
