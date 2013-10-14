<?php

 /*
 * This file is part of the ExcelToDoctrineMigratorBundle package.
 *
 * (c) Dmitry Bykov <dmitry.bykov@sibers.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sibers\ExcelToDoctrineMigrationBundle\Tests\Fixtures;

class ValidatedClass {
    protected $column;

    function __construct($column)
    {
        $this->column = $column;
    }

    public function getColumn()
    {
        return $this->column;
    }
}