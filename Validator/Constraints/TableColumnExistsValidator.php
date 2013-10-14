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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\DBAL\Schema\Column;
use Sibers\ExcelToDoctrineMigrationBundle\Migration\Mapping;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * TableColumnExistsValidator
 *
 * validates if column exists
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class TableColumnExistsValidator extends ConstraintValidator {

    /**
     * @var AbstractSchemaManager dbal schema manager
     */
    protected $schemaManager;

    /**
     * @var array columns of tables
     */
    protected $tables = array();

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
        $table = $constraint->table;
        if (null === $table) {
            //mapping obj
            $tablenameProvider = $this->context->getRoot();
            if (!$tablenameProvider instanceof ITablenameProvider) {
                throw new \InvalidArgumentException(
                    'You must set "table" poperty for constraint or implement Sibers\ExcelToDoctrineMigrationBundle\Validator\Constraints\ITablenameProvider'
                );
            }

            $table = $tablenameProvider->getTablename();
        }

        if (!isset($tables[$table])) {
            $columns = $this->schemaManager->listTableColumns($table);

            $this->tables[$table] = array_map(function(Column $column) {
                return $column->getName();
            }, $columns);
        }

        if (!in_array($value, $this->tables[$table])) {
            $this->context->addViolation($constraint->message, array(
                '{{ column }}'  => $value,
                '{{ table }}'   => $table,
                '{{ columns }}' => implode(', ', $this->tables[$table])
            ));
        }
    }
}