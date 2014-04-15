<?php

namespace Fza\FacebookCanvasAppBundle\Check;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

interface FacebookCheckChainHandlerInterface
{
    const CHECK_PARAMETER_KEY = '_fb_check';

    /**
     * @param GetResponseEvent $event
     *
     * @return void
     */
    public function handle(GetResponseEvent $event);
}
