<?php
namespace Sibers\ExcelToDoctrineMigrationBundle\ValueProvider;

use Sibers\ExcelToDoctrineMigrationBundle\Migration\Mapping;

/**
 * Value provider interface
 * 
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
interface IValueProvider
{
    /**
     * Returns value
     * 
     * @param \PHPExcel_Worksheet $wSheet active sheet
     * @param integer $row row
     * @param \Sibers\ExcelToDoctrineMigrationBundle\Migration\Mapping $mapping mapping
     */
    public function getValue(\PHPExcel_Worksheet $wSheet, $row, Mapping $mapping);
}
