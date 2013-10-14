<?php

namespace Sibers\ExcelToDoctrineMigrationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sibers\ExcelToDoctrineMigrationBundle\DependencyInjection\Compiler\ValueProviderPass;

class SibersExcelToDoctrineMigrationBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ValueProviderPass());
    }
}
