<?php

namespace Fza\FacebookCanvasAppBundle\Check\Handler;

use Symfony\Component\HttpFoundation\Request;

class NoSignedRequestHandler extends AbstractHandler
{
    /**
     * @inheritdoc
     */
    public function handle(Request $request)
    {
        $facebookContext = $this->getFacebookContext();

        if (false === $facebookContext->isSignedRequest() || null === $facebookContext->isPageLiked()) {
            if (null !== ($logger = $this->getLogger())) {
                $logger->info('Facebook checks: No signed request.');
            }

            return $this->facebookRedirect('/' . $facebookContext->getPageId() . '?sk=app_' . $facebookContext->getAppId());
        }

        return null;
    }
}
