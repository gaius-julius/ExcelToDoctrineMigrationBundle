<?php

 /*
 * This file is part of the ExcelToDoctrineMigratorBundle package.
 *
 * (c) Dmitry Bykov <dmitry.bykov@sibers.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sibers\ExcelToDoctrineMigrationBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * TableColumnExists
 *
 * validates if table column exists
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class TableColumnExists extends Constraint  {
    /**
     * @var string table name
     */
    public $table;

    /**
     * @var string column name
     */
    public $column;

    /**
     * @var string violation message
     */
    public $message = 'Column {{ column }} does not exist in {{ table }}. Available columns are: {{ columns }}';

    /**
     * {@inheritDoc}
     */
    public function validatedBy()
    {
        return 'sibers.validator.table_column_exists';
    }
}