<?php

namespace Fza\FacebookCanvasAppBundle\Check\Handler;

use Symfony\Component\HttpFoundation\Request;

interface HandlerInterface
{
    public function handle( Request $request );
}
