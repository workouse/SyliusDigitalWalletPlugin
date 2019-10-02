<?php

declare(strict_types=1);

namespace Workouse\DigitalWalletPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('workouse_digital_wallet');
        if (\method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('workouse_digital_wallet');
        }

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('referrer')
                    ->children()
                        ->scalarNode('action')->end()
                        ->scalarNode('amount')->end()
                        ->scalarNode('currency_code')->end()
                    ->end()
                ->end() // referrer
                ->arrayNode('invitee')
                    ->children()
                        ->scalarNode('action')->end()
                        ->scalarNode('amount')->end()
                        ->scalarNode('currency_code')->end()
                    ->end()
                ->end() // invitee
            ->end();

        return $treeBuilder;
    }
}
