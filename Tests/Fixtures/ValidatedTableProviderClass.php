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
use Sibers\ExcelToDoctrineMigrationBundle\Validator\Constraints\ITablenameProvider;

class ValidatedTableProviderClass extends ValidatedClass implements ITablenameProvider {

    public function getTablename()
    {
        return 'table_provider';
    }
}