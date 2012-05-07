<?php

namespace Fza\FacebookCanvasAppBundle\Check\Handler;

use Symfony\Component\HttpFoundation\Request;

class PageNotLikedHandler extends AbstractHandler
{
    protected $forwardController;

    public function __construct( $forwardController )
    {
        $this->forwardController = $forwardController;
    }

    public function handle( Request $request )
    {
        $facebookContext = $this->getFacebookContext();

        if( false === $facebookContext->isPageLiked() )
        {
            if( null !== ( $logger = $this->getLogger() ) )
            {
                $logger->info( 'Facebook checks: Page not liked.' );
            }

            return $this->forward( $this->forwardController );
        }

        return null;
    }
}
