<?php

 /*
 * This file is part of the ExcelToDoctrineMigratorBundle package.
 *
 * (c) Dmitry Bykov <dmitry.bykov@sibers.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sibers\ExcelToDoctrineMigrationBundle\Migration;

use Doctrine\DBAL\Connection;
use Sibers\ExcelToDoctrineMigrationBundle\Exception\NotUniqueExcelHeaderColumn;
use Sibers\ExcelToDoctrineMigrationBundle\Exception\ExcelColumnNotFoundException;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\DBALException;
use Sibers\ExcelToDoctrineMigrationBundle\ValueProvider\IValueProvider;
use Sibers\ExcelToDoctrineMigrationBundle\ValueProvider\ScalarProvider;
use Symfony\Component\Security\Core\Exception\ProviderNotFoundException;

class Migrator {

    /**
     * @var Connection conn dbal connection
     */
    protected $conn;

    /**
     * @var string idHashSeparator
     */
    protected $idHashSeparator = '--';
    
    /**
     * @var OutputInterface output 
     */
    protected $output;
    
    /**
     * @var array available value providers 
     */
    protected $valueProviders = array();

    /**
     * Constructor
     *
     * @param Connection $conn dbal connection
     */
    function __construct($conn)
    {
        $this->conn = $conn;
    }
    
    /**
     * Adds value provider
     * 
     * @param string $name value provider name
     * @param \Sibers\ExcelToDoctrineMigrationBundle\ValueProvider\IValueProvider $valueProvider value provider instance
     */
    public function addValueProvider($name, IValueProvider $valueProvider)
    {
        $this->valueProviders[$name] = $valueProvider;
    }
    
    /**
     * Sets output
     * 
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function setOutPut(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Migrates data from excel to dbal
     *
     * @param Config $config config
     */
    public function migrate(Config $config)
    {
        $reader       = $this->getReader($config->getFilename());
        $dbTableName  = $config->getTable();
        $pkNames      = $this->getPkNames($dbTableName);
        $existingIds  = $this->getIds($pkNames, $dbTableName);
        $mappings     = $config->getMappings();

        foreach($config->getSheets() as $sheetIndex) {
            $wSheet = $reader->setActiveSheetIndex($sheetIndex - 1);
            if (null !== $config->getHeaderRow()) {
                $excelMapping = $this->getExcelMapping($wSheet, $config);
                $this->setExcelColumns($excelMapping, $mappings);
            }

            $highestRow = $wSheet->getHighestRow();
            
            for ($row = $config->getHeaderRow() + 1; $row <= $highestRow; $row++) {
                
                if ($this->output) {
                    $this->output->writeln('<info>Migrating ' . $row . ' row...</info>');
                }
                
                $dbRowValues = $this->getValues($wSheet, $row, $mappings);
                $rowHash = $this->getRowHash($dbRowValues, $pkNames);

                if(!$this->dbRowExists($rowHash, $existingIds)) {
                    $this->insert($dbTableName, $dbRowValues);
                    if (false !==$rowHash) {
                        $existingIds[$rowHash] = true;
                    }
                } else {
                    $pkValues    = array_intersect_key($dbRowValues, array_flip($pkNames));
                    $dbRowValues = array_diff_key($dbRowValues, $pkValues);
                    $this->update($dbTableName, $dbRowValues, $pkValues);
                }
            }
        }
    }

    /**
     * Returns reader
     *
     * @param $filename string excel filename
     *
     * @return \PHPExcel
     */
    private function getReader($filename)
    {
        $reader = \PHPExcel_IOFactory::createReaderForFile($filename);
        $reader->setReadDataOnly(true);

        return $reader->load($filename);
    }

    /**
     * Sets excel columns by header
     *
     * @param array $excelMapping excel column mapping by header
     * @param array $mappings mapping collection
     *
     * @throws ExcelColumnNotFoundException if source column is not found in excel work sheet
     */
    private function setExcelColumns(array $excelMapping, array $mappings)
    {
        foreach($mappings as $mapping) {
            if ($mapping->getExcelColumns()) {
                continue;
            }

            $source           = $mapping->getSource();
            $excelColumnNames = array();
            
            foreach($source as $header) {
                if (isset($excelMapping[$header])) {
                    $excelColumnNames[] = $excelMapping[$header];
                } elseif (Mapping::ON_FAILURE_EXCEPTION === $mapping->getOnFailure()) {
                    throw new ExcelColumnNotFoundException($header);
                }
            }
            
            $mapping->setExcelColumns($excelColumnNames);
        }
    }

    /**
     * Returns excel mapping by header row
     *
     * header => columnName
     *
     * @param \PHPExcel_Worksheet $wSheet work sheet
     * @param Config $config configuration
     *
     * @return array
     *
     * @throws NotUniqueExcelHeaderColumn if header value is not unique
     */
    private function getExcelMapping(\PHPExcel_Worksheet $wSheet, Config $config)
    {
        $headerRow     = $config->getHeaderRow();
        $highestColumn = $wSheet->getHighestColumn();

        $mapping = array();
        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $value = $wSheet->getCell($col . $headerRow)->getCalculatedValue();

            if (!empty($value)) {
                if (isset($mapping[$value])) {
                    throw new NotUniqueExcelHeaderColumn($value);
                }

                $mapping[$value] = $col;
            }

        }
        
        return $mapping;
    }

    /**
     * Returns values for row
     *
     * @param $wSheet \PHPExcel_Worksheet work sheet
     * @param $row integer current row
     * @param $mappings array mapping collection
     *
     * @throws \Sibers\ExcelToDoctrineMigrationBundle\Exception\ExcelColumnNotFoundException
     *
     * @return array
     */
    private function getValues($wSheet, $row, array $mappings)
    {
        $values = array();
        foreach($mappings as $mapping) {

            $values[$mapping->getDestination()] = $this->getValueProvider($mapping)
                                                       ->getValue($wSheet, $row, $mapping);
        }
        
        return $values;
    }
    
    /**
     * Returns value provider
     * 
     * @param \Sibers\ExcelToDoctrineMigrationBundle\Migration\Mapping $mapping mapping
     * @return IValueProvider
     * 
     * @throws ProviderNotFoundException
     */
    private function getValueProvider(Mapping $mapping)
    {
        $valueProviderName = ScalarProvider::NAME;
        if ($mapping->getValueProviderName()) {
            $valueProviderName = $mapping->getValueProviderName();
        }
        
        if (!isset($this->valueProviders[$valueProviderName])) {
            throw new ProviderNotFoundException(
                $valueProviderName,
                array_keys($this->valueProviders)
            );
        }
        
        return $this->valueProviders[$valueProviderName];
    }
    
    /**
     * Executes update query
     *
     * @param string $tableName table name
     * @param array $values values to be updated
     * @param array $pks primary keys values
     *
     * @return integer
     */
    private function update($tableName, array $values, array $pks)
    {
        try {
            return $this->conn->update($tableName, $values, $pks);
        } catch(DBALException $e) {
            if ($this->output) {
                $this->output->writeln('<error>' . $e->getMessage() . '</error>');
            }
        }
    }

    /**
     * Inserts row
     *
     * @param string $tableName table name
     * @param array $values values
     *
     * @return int
     */
    private function insert($tableName, array $values)
    {
        try {
            return $this->conn->insert($tableName, $values);
        } catch(DBALException $e) {
            if ($this->output) {
                $this->output->writeln('<error>' . $e->getMessage() . '</error>');
            }
        }
    }

    /**
     * Checks if db row exists by primary keys
     * @param $existingIds array existing ids
     *
     *
     * @return bool
     */
    private function dbRowExists($hash, $existingIds)
    {
        return isset($existingIds[$hash]);
    }
    
    /**
     * Returns row hash by primary keys
     * 
     * @param $dbRowValues array db row values
     * @param $pkNames array primary keys names
     * 
     * @return string
     */
    private function getRowHash($dbRowValues, $pkNames)
    {
        $hash = '';
        foreach($pkNames as $pkName) {
            if (!isset($dbRowValues[$pkName])) {
                return false;
            }

            $hash .= $dbRowValues[$pkName] . $this->idHashSeparator;
        }

        return trim($hash, $this->idHashSeparator);
    }

    /**
     * Returns exists ids
     *
     * @param array $primaryKeys pk names
     * @param $tableName string table name
     *
     * @return array
     */
    private function getIds(array $primaryKeys, $tableName)
    {
        $ids = $this->conn->createQueryBuilder()
                          ->select($primaryKeys)
                          ->from($tableName, '')
                          ->execute()
                          ->fetchAll();

        $idHashSeparator = $this->idHashSeparator;
        
        return array_flip(array_map(function(array $pk) use ($idHashSeparator) {
            return implode($idHashSeparator, $pk);
        }, $ids));
    }

    /**
     * Returns pk names
     *
     * @param $tableName string tablename
     *
     * @return array
     */
    private function getPkNames($tableName)
    {
        $indexes = $this->conn->getSchemaManager()->listTableIndexes($tableName);
        $primaryKeys = array();
        foreach ($indexes as $index) {
            if($index->isPrimary()) {
                $primaryKeys = array_merge($primaryKeys, $index->getColumns());
            }
        }
        
        sort($primaryKeys);
        return $primaryKeys;
    }
}