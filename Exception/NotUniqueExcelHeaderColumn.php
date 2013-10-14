<?php

 /*
 * This file is part of the ExcelToDoctrineMigratorBundle package.
 *
 * (c) Dmitry Bykov <dmitry.bykov@sibers.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sibers\ExcelToDoctrineMigrationBundle\Exception;

/**
 * NotUniqueExcelHeaderColumn throws if header column is not unique
 *
 * @package Sibers\ExcelToDoctrineMigrationBundle\Exception
 */
class NotUniqueExcelHeaderColumn extends \InvalidArgumentException {

    /**
     * Constructor
     *
     * @param string $headerCol column value in header row
     */
    public function __construct($headerCol)
    {
        $message = sprintf('Header "%s" is not unique', $headerCol);
        parent::__construct($message);
    }
}