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

use Symfony\Component\Validator\Constraints\File as BaseFile;

class File extends BaseFile {
    /**
     * @var array extensions
     */
    public $extensions;

    /**
     * @var string wrong extension message
     */
    public $extensionsMessage = "The extension of the file is invalid ({{ extension }}). Allowed extensions are {{ extensions }}.";
}