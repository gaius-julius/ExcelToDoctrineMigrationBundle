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

/**
 * ITablenameProvider
 *
 * provides tablename
 */
interface ITablenameProvider {

    /**
     * Provides tablename
     *
     * @return string
     */
    public function getTablename();
}