<?php

namespace Tienvx\Bundle\PactProviderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('pact_provider');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('state_change')
                    ->children()
                        ->scalarNode('url')->defaultValue('/change-state')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
