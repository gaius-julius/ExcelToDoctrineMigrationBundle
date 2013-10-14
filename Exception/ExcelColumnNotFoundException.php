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
 * ExcelColumnNotFoundException if source column is not found in excel work sheet
 *
 * @package Sibers\ExcelToDoctrineMigrationBundle\Exception
 */
class ExcelColumnNotFoundException extends \InvalidArgumentException {

    /**
     * Constructor
     *
     * @param string $source column name
     */
    public function __construct($source)
    {
        $message = sprintf('Source column "%s" was not found', $source);
        parent::__construct($message);
    }
}