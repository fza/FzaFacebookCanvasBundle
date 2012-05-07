<?php

namespace Fza\FacebookCanvasAppBundle\Check;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

interface FacebookCheckChainHandlerInterface
{
    const CHECK_PARAMETER_KEY = '_fb_check';

    public function handle( GetResponseEvent $event );
}
