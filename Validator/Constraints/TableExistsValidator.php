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

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\DBAL\Connection;

class TableExistsValidator extends ConstraintValidator {

    /**
     * @var AbstractSchemaManager conn
     */
    protected $schemaManager;

    /**
     * Constructor
     *
     * @param Connection $conn dbal connection
     */
    public function __construct(Connection $conn)
    {
        $this->schemaManager = $conn->getSchemaManager();
    }

    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint)
    {
        $tables = $this->schemaManager->listTableNames();

        if (false === in_array($value, $tables)) {
            $this->context->addViolation($constraint->message, array(
                '{{ table }}'  => $value,
                '{{ tables }}' => json_encode($tables)
            ));
        }
    }

}