<?php

declare(strict_types=1);

namespace RocIT\GoogleMapBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('google_map');

        $treeBuilder
            ->getRootNode()
                ->children()
                    ->scalarNode('api_key')
                        ->isRequired()
                        ->cannotBeEmpty()
                        ->info('Google map Api key')
                    ->end()
                    ->arrayNode('default_options')
                        ->prototype('variable')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
