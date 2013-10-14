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
 * TableExists constraint
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class TableExists extends Constraint {
    /**
     * {@inheritDoc}
     */
    public $message = 'Table {{ table }} does not exists. Available tables are {{ tables }}';

    /**
     * {@inheritDoc}
     */
    public function validatedBy()
    {
        return 'sibers.validator.table_exists';
    }
}