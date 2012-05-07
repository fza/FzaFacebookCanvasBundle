<?php

namespace Fza\FacebookCanvasAppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class ChannelController extends Controller
{
    public function channelAction()
    {
        $response = new Response( '<script src="//connect.facebook.net/en_US/all.js"></script>' );

        $expires = new \DateTime( 'now' );
        $expires->modify( '+1 year' );
        $response->setExpires( $expires );

        $response->setPublic();
        $response->setMaxAge( 60 * 60 * 24 * 365 );

        return $response;
    }
}
