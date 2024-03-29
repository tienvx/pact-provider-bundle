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
                        ->scalarNode('url')
                            ->defaultValue('/pact-change-state')
                        ->end()
                        ->booleanNode('body')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('messages_url')
                    ->defaultValue('/pact-messages')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
