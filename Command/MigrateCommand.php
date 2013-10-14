<?php

 /*
 * This file is part of the ExcelToDoctrineMigratorBundle package.
 *
 * (c) Dmitry Bykov <dmitry.bykov@sibers.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sibers\ExcelToDoctrineMigrationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Imports data from excel to dbal
 */
class MigrateCommand extends ContainerAwareCommand {
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('sibers:excel:import')
             ->setDescription('Imports data from excel o dbal')
             ->addArgument('filename', InputOption::VALUE_REQUIRED, 'excel filename')
             ->addArgument('config', InputOption::VALUE_REQUIRED, 'config filename')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $dialog    = $this->getHelperSet()->get('dialog');

        $filename = $input->getArgument('filename');
        while(!file_exists($filename) || !is_readable($filename)) {
            $filename = $dialog->ask(
                $output,
                sprintf(
                    "File %s does not exist or is not readable, please, provide new:\n",
                    $filename
                )
            );
        }

        $config = $input->getArgument('config');
        $configs = $container->getParameter('sibers_excel_to_doctrine_migration.configs');
        if (isset($configs[$config])) {
            $config = $configs[$config];
        }
        
        while(!file_exists($config) || !is_readable($config)) {
            $config = $dialog->ask(
                $output,
                sprintf(
                    "File %s does not exist or is not readable, please, provide new:\n",
                    $config
                )
            );
        }
        
        $serializer = $container->get('jms_serializer');
        $configObj = $serializer->deserialize(
            file_get_contents($config),
            'Sibers\ExcelToDoctrineMigrationBundle\Migration\Config',
            'xml'
        );
        
        $configObj->setFilename($filename);
        
        $validator = $container->get('validator');
        $errors = $validator->validate($configObj);
        
        if (count($errors) > 0) {
            foreach($errors as $error) {
                $output->writeln('<error>' . $error . '</error>');
            }
            exit();
        }
        $migrator = $container->get('sibers.excel_migrator');
        
        $this->beforeMigrate($output);
        
        $migrator->setOutput($output);
        $migrator->migrate($configObj);
    }
    
    /**
     * Runs before migration tasks
     * 
     * @param \Symfony\Component\Console\Output\OutputInterface $output output
     */
    private function beforeMigrate(OutputInterface $output)
    {
        $container = $this->getContainer();
        $commands  = $container->getParameter(
            'sibers_excel_to_doctrine_migration.before_migration_tasks'
        );
        
        if ($commands) {
            $app = $this->getApplication();
            foreach($commands as $command) {
                $parameters = array($command['command']);
                
                if (isset($command['parameters'])) {
                    foreach($command['parameters'] as $parameter) {
                        $parameters[$parameter['key']] = $parameter['value'];
                    }
                }
                
                $input = new ArrayInput($parameters);
                $app->doRun($input, $output);
            }
        }
    }
}