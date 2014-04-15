<?php

namespace Fza\FacebookCanvasAppBundle\Check\Handler;

use Symfony\Component\HttpFoundation\Request;

class FacebookErrorHandler extends AbstractHandler
{
    /**
     * @inheritdoc
     */
    public function handle(Request $request)
    {
        if ($request->query->has('error_reason') || $request->query->has('error_code')) {
            if (null !== ($logger = $this->getLogger())) {
                $logger->warning(sprintf('There was an error derived from facebook: %s', $request->query->get('error_msg', $request->query->get('error_description', '(no error message available)'))));
            }

            return $this->facebookRedirect('/' . $this->getFacebookContext()->getPageId());
        }

        return null;
    }
}
