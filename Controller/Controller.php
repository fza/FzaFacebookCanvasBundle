<?php

namespace Fza\FacebookCanvasAppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    /**
     * @return Response
     */
    public function redirectToApp()
    {
        return new Response('<script>top.location.href="' . $this->getAppUri() . '";</script>');
    }

    /**
     * @param bool $withScheme
     *
     * @return string
     */
    public function getAppUri($withScheme = true)
    {
        $facebookContext = $this->get('facebook.context');

        $uri = $withScheme ? $this->get('request')->getScheme() . '://' : '';
        $uri .= 'www.facebook.com/' . $facebookContext->getPageId() . '?sk=app_' . $facebookContext->getAppId();

        return $uri;
    }

    /**
     * @return object
     */
    public function getFacebookUser()
    {
        return $this->get('facebook.user');
    }
}
