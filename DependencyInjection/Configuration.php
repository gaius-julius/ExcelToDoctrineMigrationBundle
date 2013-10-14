<?php

namespace Sibers\ExcelToDoctrineMigrationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sibers_excel_to_doctrine_migration');

        $rootNode->children()
                    ->arrayNode('configs')
                        ->useAttributeAsKey('title')
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('before_migration')
                        ->children()
                            ->arrayNode('tasks')
                                ->requiresAtLeastOneElement()
                                ->prototype('array')
                                ->children()
                                    ->scalarNode('command')
                                        ->cannotBeEmpty()
                                    ->end()
                                    ->arrayNode('parameters')
                                        ->prototype('array')
                                            ->children()
                                                ->scalarNode('key')->isRequired()->end()
                                                ->scalarNode('value')->isRequired()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                 ->end();
        
        return $treeBuilder;
    }
}
