<?php

namespace Fza\FacebookCanvasAppBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root( 'fza_fb_canvas' );

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode( 'app_id')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode( 'api_secret')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode( 'page_id' )->isRequired()->cannotBeEmpty()->end()

                ->booleanNode( 'session_use_request' )->defaultValue( false )->end()
                ->booleanNode( 'session_use_query' )->defaultValue( false )->end()
                ->booleanNode( 'session_use_parameter' )->defaultValue( true )->end()

                ->scalarNode( 'session_request_key' )->defaultValue( 'fbsid' )->end()
                ->scalarNode( 'session_query_key' )->defaultValue( 'fbsid' )->end()
                ->scalarNode( 'session_parameter_key' )->defaultValue( 'fb_session_id' )->end()
                ->scalarNode( 'session_lifetime' )->defaultValue( '3600' )->end()

                ->scalarNode( 'authentication_redirect_path' )->defaultValue( '/' )->end()
                ->scalarNode( 'showdown_date' )->end()

                ->scalarNode( 'persistence_prefix' )->defaultValue( '_fza_facebook' )->end()
                ->scalarNode( 'session_storage' )->defaultValue( 'fza_facebook.session.storage.filesystem' )->end()
                ->scalarNode( 'doctrine_storage_entity_manager' )->defaultValue( 'default' )->end()
                ->scalarNode( 'doctrine_storage_gc_probability' )->defaultValue( '0.2' )->end()

                ->booleanNode( 'retrieve_user_profile' )->defaultValue( true )->end()
                ->booleanNode( 'cache_user_profile' )->defaultValue( true )->end()

                ->scalarNode( 'facebook_user_entity_manager' )->defaultValue( 'default' )->end()
                ->scalarNode( 'facebook_user_entity_namespace' )->isRequired()->cannotBeEmpty()->end()

                ->scalarNode( 'facebook_sdk_base_file' )->defaultValue( '%kernel.root_dir%/../vendor/facebook/src/base_facebook.php' )->end()

                ->arrayNode( 'permissions' )
                    ->addDefaultsIfNotSet()
                    ->defaultValue( array() )
                    ->prototype( 'scalar' )->end()
                ->end()

                ->arrayNode( 'checks' )
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey( 'name' )
                    ->prototype( 'array' )
                        ->prototype('scalar')->end()
                    ->end()
                ->end()

                ->arrayNode( 'controllers' )
                    ->useAttributeAsKey( 'name' )
                    ->prototype( 'scalar' )->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
