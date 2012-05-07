<?php

namespace Fza\FacebookCanvasAppBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\Container;

class FzaFacebookCanvasAppExtension extends \Twig_Extension
{
    public function __construct( Container $container )
    {
        $this->container = $container;
    }

    public function getGlobals()
    {
        $facebookContext = $this->container->get( 'facebook.context' );
        $router = $this->container->get( 'router' );
        $request = $this->container->get( 'request' );

        return array(
            'facebook' => array(
                'sessionId'  => $facebookContext->getSessionId(),
                'appId'      => $facebookContext->getAppId(),
                'apiSecret'  => $facebookContext->getApiSecret(),
                'pageId'     => $facebookContext->getPageId(),
                'userId'     => $facebookContext->getUserId(),
                'channelUrl' => $router->generate( '_fb_channel', array(), true ),
                'appUrl'     => $request->getScheme() . '://www.facebook.com/' . $facebookContext->getPageId() . '?sk=app_' . $facebookContext->getAppId(),
            )
        );
    }

    public function getName()
    {
        return 'fza_facebook_canvas_app';
    }
}
