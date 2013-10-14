<?php
namespace Sibers\ExcelToDoctrineMigrationBundle\ValueProvider;

use Sibers\ExcelToDoctrineMigrationBundle\Migration\Mapping;

/**
 * Date value Provider
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class DateProvider extends AbstractValueProvider
{
    /**
     * @var default options 
     */
    protected $options = array(
        'dateFormat' => 'd.m.Y'
    );
    
    /**
     * @inheritDoc
     */
    public function getValue(\PHPExcel_Worksheet $wSheet, $row, Mapping $mapping)
    {
        $excelColumns = $mapping->getExcelColumns();
        $column       = $excelColumns[0] . $row;
        $cell         = $wSheet->getCell($column);
        
        if (\PHPExcel_Shared_Date::isDateTime($cell)) {
            $value = \PHPExcel_Shared_Date::ExcelToPHPObject($value);
        } else {
            $options = $this->getOptions($mapping);
            $value = \DateTime::createFromFormat(
                $options['dateFormat'],
                trim($value)
            );
        }
        
        return $value;
    }
}
