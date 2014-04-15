<?php

namespace Fza\FacebookCanvasAppBundle\Check\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface HandlerInterface
{
    /**
     * @param Request $request
     *
     * @return null|Response
     */
    public function handle(Request $request);
}
