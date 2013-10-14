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

use JMS\Serializer\Annotation as JMS;

/**
 * Migration Config
 *
 * configuration of migration
 *
 * @package Sibers\ExcelToDoctrineMigrationBundle\Migration
 * @JMS\XmlRoot("config")
 */
class Config {
    /**
     * @var string fileName excel source filename
     * @JMS\Type("string")
     */
    protected $filename;

    /**
     * @var array mapping of migration
     * @JMS\XmlList(entry = "mapping")
     * @JMS\Type("array<Sibers\ExcelToDoctrineMigrationBundle\Migration\Mapping>")
     */
    protected $mappings;

    /**
     * @var string destination table
     * @JMS\Type("string")
     */
    protected $table;

    /**
     * @var array excel sheets
     * @JMS\XmlList(entry="sheet")
     * @JMS\Type("array<integer>")
     */
    protected $sheets;

    /**
     * @var excel header row
     * @JMS\Type("integer")
     * @JMS\SerializedName("headerRow")
     */
    protected $headerRow;

    /**
     * Sets filename
     *
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Returns filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Sets headerRow
     *
     * @param \Sibers\ExcelToDoctrineMigrationBundle\Migration\excel $headerRow
     */
    public function setHeaderRow($headerRow)
    {
        $this->headerRow = $headerRow;
    }

    /**
     * Returns headerRow
     *
     * @return integer
     */
    public function getHeaderRow()
    {
        return $this->headerRow;
    }

    /**
     * Sets mapping
     *
     * @param array $mapping
     */
    public function setMappings($mapping)
    {
        $this->mappings = $mapping;
    }

    /**
     * Returns mapping
     *
     * @return array
     */
    public function getMappings()
    {
        return $this->mappings;
    }

    /**
     * Sets sheets
     *
     * @param array $sheets
     */
    public function setSheets($sheets)
    {
        $this->sheets = $sheets;
    }

    /**
     * Returns sheets
     *
     * @return array
     */
    public function getSheets()
    {
        return $this->sheets;
    }

    /**
     * Sets table
     *
     * @param string $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * Returns table
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }
}