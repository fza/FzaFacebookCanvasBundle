<?php

namespace Fza\FacebookCanvasAppBundle\Check\Handler;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractHandler extends ContainerAware implements HandlerInterface
{
    protected $logger;

    public function setLogger( LoggerInterface $logger )
    {
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function getFacebookContext()
    {
        return $this->container->get( 'facebook.context' );
    }

    protected function facebookRedirect( $path = '' )
    {
        $request = $this->container->get( 'request' );

        return $this->redirectScript( $request->getScheme() . '://www.facebook.com' . $path );
    }

    protected function redirectScript( $uri )
    {
        if( null !== ( $logger = $this->getLogger() ) )
        {
            $logger->info( sprintf( 'Facebook checks: Redirecting via script to: "%s".', $uri ) );
        }

        return new Response( '<script>top.location.href="' . $uri . '";</script>' );
    }

    protected function redirect( $url, $status = 302 )
    {
        return new RedirectResponse( $url, $status );
    }

    protected function forward( $controller, array $path = array(), array $query = array() )
    {
        return $this->container->get( 'http_kernel' )->forward( $controller, $path, $query );
    }

    abstract public function handle( Request $request );
}
