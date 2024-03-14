<?php

declare(strict_types=1);

namespace Twc\MakerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('twc_maker');

        $components = [
            'validator',
            'form',
            'message',
            'messenger_middleware',
            'voter',
            'command',
            'fixtures',
        ];

        $children = $treeBuilder->getRootNode()->children();
        foreach ($components as $component) {
            $children->arrayNode($component)
                       ->arrayPrototype()
                            ->children()
                                ->scalarNode('context')->end()
                                ->scalarNode('target')->end()
                             ->end()
                        ->end()
                       ->end();
        }
        $children->arrayNode('controller')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('context')->end()
                            ->scalarNode('target')->end()
                            ->scalarNode('dir')->defaultNull()->end()
                        ->end()
                    ->end()
                 ->end();

        $children->arrayNode('entity')
                 ->arrayPrototype()
                     ->children()
                        ->scalarNode('context')->end()
                        ->scalarNode('target_entity')->end()
                        ->scalarNode('target_repository')->end()
                     ->end()
                 ->end()
                 ->end();
        $children->end();

        return $treeBuilder;
    }
}
