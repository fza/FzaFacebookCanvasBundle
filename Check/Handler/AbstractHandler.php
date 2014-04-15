<?php

namespace Fza\FacebookCanvasAppBundle\Check\Handler;

use Fza\FacebookCanvasAppBundle\Facebook\FacebookContext;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;

abstract class AbstractHandler extends ContainerAware implements HandlerInterface
{
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return FacebookContext
     */
    public function getFacebookContext()
    {
        return $this->container->get('facebook.context');
    }

    /**
     * @param string $path
     *
     * @return Response
     */
    protected function facebookRedirect($path = '')
    {
        $request = $this->container->get('request');

        return $this->redirectScript($request->getScheme() . '://www.facebook.com' . $path);
    }

    /**
     * @param $uri
     *
     * @return Response
     */
    protected function redirectScript($uri)
    {
        if (null !== ($logger = $this->getLogger())) {
            $logger->info(sprintf('Facebook checks: Redirecting via script to: "%s".', $uri));
        }

        return new Response('<script>top.location.href="' . $uri . '";</script>');
    }

    /**
     * @param     $url
     * @param int $status
     *
     * @return RedirectResponse
     */
    protected function redirect($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * @param       $controller
     * @param array $path
     * @param array $query
     *
     * @return mixed
     */
    protected function forward($controller, array $path = array(), array $query = array())
    {
        return $this->container->get('http_kernel')->forward($controller, $path, $query);
    }

    /**
     * @inheritdoc
     */
    abstract public function handle(Request $request);
}
