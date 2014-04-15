<?php

namespace Fza\FacebookCanvasAppBundle\Check\Handler;

use Symfony\Component\HttpFoundation\Request;

class NotAuthenticatedHandler extends AbstractHandler
{
    protected $authenticationRedirectPath;

    protected $permissions;

    public function __construct($authenticationRedirectPath, $permissions = '')
    {
        $this->authenticationRedirectPath = $authenticationRedirectPath;
        $this->permissions                = $permissions;
    }

    /**
     * @inheritdoc
     */
    public function handle(Request $request)
    {
        $facebookContext = $this->getFacebookContext();

        if (null === $facebookContext->getUserId()) {
            if (null !== ($logger = $this->getLogger())) {
                $logger->info('Facebook checks: Not authenticated.');
            }

            return $this->facebookRedirect('/dialog/oauth?client_id=' . $facebookContext->getAppId() . '&redirect_uri=' . urlencode($request->getUriForPath($this->authenticationRedirectPath)) . (!empty($this->permissions) ? '&scope=' . $this->permissions : ''));
        }

        return null;
    }
}
