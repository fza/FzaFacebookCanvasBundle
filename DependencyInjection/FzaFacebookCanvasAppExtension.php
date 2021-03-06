<?php

namespace Fza\FacebookCanvasAppBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class FzaFacebookCanvasAppExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $processor     = new Processor();
        $configuration = new Configuration();
        $config        = $processor->processConfiguration($configuration, $configs);

        foreach (array('listener', 'context', 'api', 'checkChainHandler', 'sessionStorage') as $method) {
            $this->{'add' . ucfirst($method) . 'Service'}($config, $container);
        }

        $container->setParameter('fza_facebook.facebookbase.file', $config['facebook_sdk_base_file']);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function addListenerService(array $config, ContainerBuilder $container)
    {
        $listenerService = $container->findDefinition('fza_facebook.listener');
        $listenerService->replaceArgument(2, $config['facebook_user_entity_namespace']);
        $container->setAlias('fza_facebook.facebook_user.doctrine.entity_manager', 'doctrine.orm.' . $config['facebook_user_entity_manager'] . '_entity_manager');
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function addContextService(array $config, ContainerBuilder $container)
    {
        $contextConfig  = array(
            'page_id'               => $config['page_id'],
            'session_use_request'   => $config['session_use_request'],
            'session_use_query'     => $config['session_use_query'],
            'session_use_parameter' => $config['session_use_parameter'],
            'session_request_key'   => $config['session_request_key'],
            'session_query_key'     => $config['session_query_key'],
            'session_parameter_key' => $config['session_parameter_key'],
            'retrieve_user_proile'  => $config['retrieve_user_profile'],
            'cache_user_profile'    => $config['cache_user_profile'],
        );
        $contextService = $container->findDefinition('fza_facebook.context');
        $contextService->replaceArgument(2, $contextConfig);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function addApiService(array $config, ContainerBuilder $container)
    {
        $apiConfig  = array(
            'appId'  => $config['app_id'],
            'secret' => $config['api_secret']
        );
        $apiService = $container->findDefinition('fza_facebook.api');
        $apiService->replaceArgument(0, $apiConfig);
        $apiService->replaceArgument(2, $config['persistence_prefix']);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     *
     * @throws \InvalidArgumentException
     */
    private function addCheckChainHandlerService(array $config, ContainerBuilder $container)
    {
        foreach ($config['checks'] as $chainKey => $chain) {
            foreach ($chain as $checkKey => $check) {
                $handlerService = $container->findDefinition('fza_facebook.check.handler.' . $check);

                switch ($check) {
                    case 'not_authenticated':
                        $handlerService->replaceArgument(0, $config['authentication_redirect_path']);
                        $handlerService->replaceArgument(1, implode(',', $config['permissions']));
                        break;

                    case 'page_not_liked':
                        if (!isset($config['controllers']['page_not_liked'])) {
                            throw new \InvalidArgumentException('The facebook check "page_not_liked" requires to find a controllers.page_not_liked configuration setting.');
                        }

                        $handlerService->replaceArgument(0, $config['controllers']['page_not_liked']);
                        break;

                    case 'showdown_date_passed':
                        if (!isset($config['controllers']['showdown_date_passed'])) {
                            throw new \InvalidArgumentException('The facebook check "showdown_date_passed" requires to find a controllers.page_not_liked configuration setting.');
                        }

                        $handlerService->replaceArgument(0, $config['controllers']['showdown_date_passed']);
                        break;
                }

                $config['checks'][$chainKey][$checkKey] = 'fza_facebook.check.handler.' . $check;
            }
        }

        $chainHandlerService = $container->findDefinition('fza_facebook.check.chainhandler');
        $chainHandlerService->replaceArgument(0, $config['checks']);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function addSessionStorageService(array $config, ContainerBuilder $container)
    {
        $sessionLifetime = (int) $config['session_lifetime'];
        $sessionLifetime = $sessionLifetime <= 60 ? 3600 : $sessionLifetime;

        if ($config['storage'] == 'fza_facebook.session.storage.doctrine') {
            $gcProbability = (float) $config['doctrine_storage_gc_probability'];
            $gcProbability = $gcProbability >= 0 && $gcProbability <= 1 ? $gcProbability : 0.2;

            $doctrineStorageService = $container->findDefinition('fza_facebook.session.storage.doctrine');
            $doctrineStorageService->replaceArgument(1, $gcProbability);
            $doctrineStorageService->replaceArgument(2, $sessionLifetime);

            $container->setAlias('fza_facebook.session.storage.doctrine.entity_manager', 'doctrine.orm.' . $config['doctrine_storage_entity_manager'] . '_entity_manager');
        }

        $container->setAlias('fza_facebook.session.storage', $config['storage']);
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'fza_fb_canvas';
    }
}
