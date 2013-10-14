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


class TableExistsFixture {
    private $tableName;

    function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    public function getTableName()
    {
        return $this->tableName;
    }
}