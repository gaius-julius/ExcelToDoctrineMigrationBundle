<?php

 /*
 * This file is part of the ExcelToDoctrineMigratorBundle package.
 *
 * (c) Dmitry Bykov <dmitry.bykov@sibers.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sibers\ExcelToDoctrineMigrationBundle\Tests\Validator\Constraints;

use Symfony\Component\Validator\Validator;
use Sibers\ExcelToDoctrineMigrationBundle\Validator\Constraints\TableColumnExistsValidator;
use Sibers\ExcelToDoctrineMigrationBundle\Validator\Constraints\TableColumnExists;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Sibers\ExcelToDoctrineMigrationBundle\Tests\Fixtures\ValidatedClass;
use Sibers\ExcelToDoctrineMigrationBundle\Tests\Fixtures\ValidatedTableProviderClass;

class TableColumnExistsValidatorTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject connection mock
     */
    private $conn;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject schemaManager mock
     */
    private $schemaManager;

    public function setUp()
    {
        $this->conn          = $this->createConnectionMock();
        $this->schemaManager = $this->createSchemaManagerMock();

    }

    /**
     * Creates validator factory mock
     *
     * @param $columnExistsValidator
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createValidatorFactoryMock($columnExistsValidator)
    {
        $validatorFactory = $this->getMock('Symfony\Component\Validator\ConstraintValidatorFactoryInterface');
        $validatorFactory->expects($this->any())
                         ->method('getInstance')
                         ->with($this->isInstanceOf('Sibers\ExcelToDoctrineMigrationBundle\Validator\Constraints\TableColumnExists'))
                         ->will($this->returnValue($columnExistsValidator));

        return $validatorFactory;
    }

    /**
     * Creates validator
     *
     * @param $metadataFactory
     *
     * @return Validator
     */
    private function createValidator($metadataFactory)
    {
        $columnExistsValidator = new TableColumnExistsValidator($this->conn);
        $validatorFactory = $this->createValidatorFactoryMock($columnExistsValidator);

        return  new Validator($metadataFactory, $validatorFactory);
    }

    /**
     * Creates dbal connection mock
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createConnectionMock()
    {
        return $this->getMockBuilder('Doctrine\DBAL\Connection')
                     ->disableOriginalConstructor()
                     ->getMock();
    }

    /**
     * Creates schema manager mock
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createSchemaManagerMock()
    {
        return $this->getMockBuilder('Doctrine\DBAL\Schema\AbstractSchemaManager')
                    ->disableOriginalConstructor()
                    ->setMethods(array('listTableColumns'))
                    ->getMockForAbstractClass();
    }

    /**
     * Creates metadata factory mock
     *
     * @param $validatedClassName
     * @param null $table
     * @param string $columnProperty
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createMetadataFactoryMock($validatedClassName, $table = null, $columnProperty = 'column')
    {
        $metadata = new ClassMetadata($validatedClassName);
        $constraint = new TableColumnExists();
        $constraint->table = $table;

        $metadata->addPropertyConstraint($columnProperty, $constraint);

        $metadataFactory = $this->getMock('Symfony\Component\Validator\Mapping\ClassMetadataFactoryInterface');
        $metadataFactory->expects($this->any())
                        ->method('getClassMetadata')
                        ->with($this->equalTo($metadata->name))
                        ->will($this->returnValue($metadata));

        return $metadataFactory;
    }


    private function getColumnMock($columnName)
    {
        $mock = $this->getMockBuilder('Doctrine\DBAL\Schema\Column')
                    ->disableOriginalConstructor()
                    ->setMethods(array('getName'))
                    ->getMock();

        $mock->expects($this->once())
             ->method('getName')
             ->will($this->returnValue($columnName));

        return $mock;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTableNameIsNotProvideException()
    {

        $validatedClass = new ValidatedClass('column');
        $metadataFactory = $this->createMetadataFactoryMock(
            get_class($validatedClass)
        );
        $validator = $this->createValidator($metadataFactory);
        $validator->validate($validatedClass);
    }

    public function testSuccessValidation()
    {
        $columnName = 'column';
        $tableName  = 'tableName';

        $validatedClass = new ValidatedClass($columnName);
        $metadataFactory = $this->createMetadataFactoryMock(
            get_class($validatedClass),
            $tableName
        );


        $this->conn->expects($this->once())
                   ->method('getSchemaManager')
                   ->will($this->returnValue($this->schemaManager));

        $column = $this->getColumnMock($columnName);

        $this->schemaManager->expects($this->once())
                            ->method('listTableColumns')
                            ->with($tableName)
                            ->will($this->returnValue(array($column)));
        $validator = $this->createValidator($metadataFactory);
        $v = $validator->validate($validatedClass);
        $this->assertEquals(0, count($v));
    }

    public function testValidationFailed()
    {
        $columnName = 'column';
        $tableName  = 'tableName';

        $validatedClass = new ValidatedClass($columnName);
        $metadataFactory = $this->createMetadataFactoryMock(
            get_class($validatedClass),
            $tableName
        );


        $this->conn->expects($this->once())
            ->method('getSchemaManager')
            ->will($this->returnValue($this->schemaManager));

        $this->schemaManager->expects($this->once())
            ->method('listTableColumns')
            ->with($tableName)
            ->will($this->returnValue(array()));

        $validator = $this->createValidator($metadataFactory);
        $v = $validator->validate($validatedClass);
        $this->assertEquals(1, count($v));
    }


    public function testITablenameProviderUsage()
    {
        $columnName = 'column';

        $validatedClass = new ValidatedTableProviderClass($columnName);
        $tableName  = $validatedClass->getTablename();

        $metadataFactory = $this->createMetadataFactoryMock(
            get_class($validatedClass)
        );

        $this->conn->expects($this->once())
            ->method('getSchemaManager')
            ->will($this->returnValue($this->schemaManager));

        $column = $this->getColumnMock($columnName);

        $this->schemaManager->expects($this->once())
            ->method('listTableColumns')
            ->with($tableName)
            ->will($this->returnValue(array($column)));

        $validator = $this->createValidator($metadataFactory);
        $v = $validator->validate($validatedClass);
        $this->assertEquals(0, count($v));
    }
}