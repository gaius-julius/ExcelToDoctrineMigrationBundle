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

use Sibers\ExcelToDoctrineMigrationBundle\Validator\Constraints\ITablenameProvider;
use JMS\Serializer\Annotation as JMS;

/**
 * Mapping config of excel column to db column
 *
 * @package Sibers\ExcelToDoctrineMigrationBundle\Migration
 */
class Mapping implements ITablenameProvider
{
    /**
     * Scalar value
     */
    const TYPE_SCALAR = 'scalar';

    /**
     * Excel formula
     */
    const TYPE_EXCEL_FORMULA = 'formula';

    /**
     * Ignore on failure
     */
    const ON_FAILURE_IGNORE = 'ignore';

    /**
     * Throws exception on failure
     */
    const ON_FAILURE_EXCEPTION = 'exception';

    /**
     * @var Config config needs for column exists validator
     * @JMS\Exclude
     */
    protected $config;

    /**
     * @var string from source column name
     * @JMS\Type("array<string>")
     * @JMS\XmlList(inline=false, entry="column")
     */
    protected $source;

    /**
     * @var string to destination db column name
     * @JMS\Type("string")
     */
    protected $destination;

    /**
     * @var array excelColumns
     * @JMS\Type("array")
     */
    protected $excelColumns;

    /**
     * @var string type type of excel column
     * @JMS\Type("string")
     */
    protected $type = self::TYPE_SCALAR;

    /**
     * @var string on Failure strategy
     * @JMS\Type("string")
     */
    protected $onFailure = self::ON_FAILURE_EXCEPTION;
    
    /**
     * @var string value provider name
     * @JMS\Type("string")
     * @JMS\SerializedName("valueProviderName")
     */
    protected $valueProviderName;
    
    /**
     * @var array value provider options
     * @JMS\Type("array<string, string>")
     * @JMS\XmlMap(keyAttribute="name", entry="option")
     * @JMS\SerializedName("valueProviderOptions")
     */
    protected $valueProviderOptions;

    /**
     * Sets destination
     *
     * @param string $destination
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
    }

    /**
     * Sets source
     *
     * @param array $source
     */
    public function setSource(array $source)
    {
        $this->source = $source;
    }

    /**
     * Sets type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }


    /**
     * Returns destination
     *
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Returns source
     *
     * @return array
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Returns type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets onFailure
     *
     * @param string $onFailure
     */
    public function setOnFailure($onFailure)
    {
        $this->onFailure = $onFailure;
    }

    /**
     * Returns onFailure
     *
     * @return string
     */
    public function getOnFailure()
    {
        return $this->onFailure;
    }

    /**
     * Returns available column types
     *
     * @return array
     */
    public static function getColumnTypes()
    {
        return array(
            self::TYPE_SCALAR,
            self::TYPE_EXCEL_FORMULA,
        );
    }

    /**
     * Returns available onFailureTypes
     *
     * @return array
     */
    public static function getOnFailureTypes()
    {
        return array(
            self::ON_FAILURE_EXCEPTION,
            self::ON_FAILURE_IGNORE
        );
    }

    /**
     * Sets config
     *
     * @param \Sibers\ExcelToDoctrineMigrationBundle\Migration\Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Provides tablename
     *
     * @return string
     */
    public function getTablename()
    {
        return $this->config->getTable();
    }

    /**
     * Sets excelColumn
     *
     * @param array $excelColumn
     */
    public function setExcelColumns(array $excelColumns)
    {
        $this->excelColumns = $excelColumns;
    }

    /**
     * Returns excelColumn
     *
     * @return array
     */
    public function getExcelColumns()
    {
        return $this->excelColumns;
    }
    
    /**
     * Returns value provider name
     * 
     * @return string
     */
    public function getValueProviderName()
    {
        return $this->valueProviderName;
    }

    /**
     * Sets value provider
     * 
     * @param string $valueProvider value provider name
     */
    public function setValueProviderName($valueProvider)
    {
        $this->valueProviderName = $valueProvider;
    }
    
    /**
     * Returns value provider options
     * 
     * @return array
     */
    public function getValueProviderOptions()
    {
        return $this->valueProviderOptions;
    }

    /**
     * Sets value provider options
     * 
     * @param array $valueProviderOptions value provider options
     */
    public function setValueProviderOptions($valueProviderOptions)
    {
        $this->valueProviderOptions = $valueProviderOptions;
    }
}