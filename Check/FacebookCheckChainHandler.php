<?php

namespace Fza\FacebookCanvasAppBundle\Check;

use Fza\FacebookCanvasAppBundle\Check\Handler\HandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class FacebookCheckChainHandler implements FacebookCheckChainHandlerInterface
{
    protected $chains;

    /**
     * @param array              $chains
     * @param ContainerInterface $container
     */
    public function __construct(array $chains, ContainerInterface $container)
    {
        $this->chains    = $chains;
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function handle(GetResponseEvent $event)
    {
        $chain = $this->parseChain($event->getRequest()->attributes->get(FacebookCheckChainHandlerInterface::CHECK_PARAMETER_KEY, ''));

        if (null !== $chain) {
            foreach ($chain as $handlerService) {
                $handler = $this->container->get($handlerService);

                if (!$handler instanceof HandlerInterface) {
                    throw new \RuntimeException(sprintf('The check handler "%s" must implement Fza\\FacebookCanvasAppBundle\\Check\\Handler\\HandlerInterface.', $handlerService));
                }

                if (null !== $response = $handler->handle($event->getRequest())) {
                    $event->setResponse($response);

                    return;
                }
            }
        }
    }

    /**
     * @param $chain
     *
     * @return null|array[HandlerInterface]
     * @throws \RuntimeException
     */
    private function parseChain($chain)
    {
        $chain = strtolower(trim($chain));

        if (!empty($chain)) {
            if (!isset($this->chains[$chain])) {
                throw new \RuntimeException(sprintf('The chain "%s" is not defined.', $chain));
            }

            return $this->chains[$chain];
        }

        return null;
    }
}
