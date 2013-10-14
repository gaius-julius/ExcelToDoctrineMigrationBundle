<?php
namespace Sibers\ExcelToDoctrineMigrationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Adds value providers
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class ValueProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sibers.excel_migrator')) {
            return;
        }
        
        $migratorDefinition = $container->getDefinition('sibers.excel_migrator');
        $taggedServices = $container->findTaggedServiceIds('sibers_excel_to_doctrine_migration.value_provider');
        
        foreach($taggedServices as $serviceId => $attributes) {
            if (!isset($attributes[0]['providerName'])) {
                throw new \InvalidArgumentException('providerName attribute must be edfined');
            }
            $migratorDefinition->addMethodCall(
                'addValueProvider',
                array($attributes[0]['providerName'], new Reference($serviceId))
            );
        }

    }
}
