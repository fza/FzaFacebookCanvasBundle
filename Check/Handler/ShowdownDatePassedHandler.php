<?php

namespace Fza\FacebookCanvasAppBundle\Check\Handler;

use Symfony\Component\HttpFoundation\Request;

class ShowdownDatePassedHandler extends AbstractHandler
{
    protected $forwardController;

    public function __construct( $forwardController )
    {
        $this->forwardController = $forwardController;
    }

    public function handle( Request $request )
    {
        $facebookContext = $this->getFacebookContext();
        $showdownDate = $facebookContext->getShowdownDate();

        if( null !== $showdownDate && new \DateTime() >= $showdownDate )
        {
            if( null !== ( $logger = $this->getLogger() ) )
            {
                $logger->info( 'Facebook checks: Showdown date passed.' );
            }

            return $this->forward( $this->forwardController );
        }

        return null;
    }
}
