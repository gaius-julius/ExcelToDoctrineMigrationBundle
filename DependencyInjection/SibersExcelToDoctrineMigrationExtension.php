<?php

namespace Sibers\ExcelToDoctrineMigrationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SibersExcelToDoctrineMigrationExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        
        $schemaConfigs = array();
        if (!empty($config['configs'])) {
            $schemaConfigs = $config['configs'];
        }
        $container->setParameter(
            'sibers_excel_to_doctrine_migration.configs',
            $schemaConfigs
        );
        
        $commands = array();
        if (isset($config['before_migration']['tasks'])) {
            $commands = $config['before_migration']['tasks'];
        }
        $container->setParameter(
            'sibers_excel_to_doctrine_migration.before_migration_tasks',
            $commands
        );
    }
}
