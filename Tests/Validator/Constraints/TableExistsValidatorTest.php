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
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\ClassMetadataFactoryInterface;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Sibers\ExcelToDoctrineMigrationBundle\Tests\Fixtures\TableExistsFixture;
use Sibers\ExcelToDoctrineMigrationBundle\Validator\Constraints\TableExists;
use Sibers\ExcelToDoctrineMigrationBundle\Validator\Constraints\TableExistsValidator;


class TableExistsValidatorTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject conn
     */
    private $conn;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject sm
     */
    private $sm;

    protected function setUp()
    {
        $this->conn = $this->createConnectionMock();
        $this->sm   = $this->createSchemaManagerMock();
        $this->conn->expects($this->any())->method('getSchemaManager')->will($this->returnValue($this->sm));
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
                    ->setMethods(array('listTableNames'))
                    ->getMockForAbstractClass();
    }


    /**
     * Creates metadata factory mock
     *
     * @param $validatedClassName validated class name
     * @param Constraint $constraint constraint obj
     * @param string|null $property constraint property
     * @param string|null $getter constraint getter
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createMetadataFactoryMock($validatedClassName, Constraint $constraint, $property = null, $getter = null)
    {
        $targets  = (array) $constraint->getTargets();
        $metadata = new ClassMetadata($validatedClassName);
        if (false !== in_array(Constraint::PROPERTY_CONSTRAINT,$targets)) {
            if (null !== $property) {
                $metadata->addPropertyConstraint($property, $constraint);
            }

            if (null !== $getter) {
                $metadata->addGetterConstraint($getter, $constraint);
            }
        }

        if (false !== in_array(Constraint::CLASS_CONSTRAINT, $targets)) {
            $metadata->addConstraint($constraint);
        }

        $metadataFactory = $this->getMock('Symfony\Component\Validator\Mapping\ClassMetadataFactoryInterface');
        $metadataFactory->expects($this->any())
            ->method('getClassMetadata')
            ->with($this->equalTo($metadata->name))
            ->will($this->returnValue($metadata));

        return $metadataFactory;
    }

    /**
     * Creates validator factory mock
     *
     * @param $validator ConstraintValidator tested validator obj
     * @param $constraintClassName Constraint class name
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createValidatorFactoryMock(ConstraintValidator $validator, $constraintClassName)
    {
        $validatorFactory = $this->getMock('Symfony\Component\Validator\ConstraintValidatorFactoryInterface');
        $validatorFactory->expects($this->any())
            ->method('getInstance')
            ->with($this->isInstanceOf($constraintClassName))
            ->will($this->returnValue($validator));

        return $validatorFactory;
    }

    /**
     * Creates Validator
     *
     * @param ClassMetadataFactoryInterface $metadataFactory        metadata factory
     * @param ConstraintValidatorFactoryInterface $validatorFactory validator factory
     *
     * @return Validator
     */
    private function createValidator(ClassMetadataFactoryInterface $metadataFactory, ConstraintValidatorFactoryInterface $validatorFactory)
    {
        return new Validator($metadataFactory, $validatorFactory);
    }

    /**
     * Table exist
     */
    public function testIsValid()
    {
        $tableName      = 'tableName';
        $validatedObj = new TableExistsFixture($tableName);
        $constraint     = new TableExists();
        $this->sm->expects($this->once())->method('listTableNames')->will($this->returnValue(array($tableName)));

        $constraintValidator = new TableExistsValidator($this->conn);
        $metadataFactory     = $this->createMetadataFactoryMock(get_class($validatedObj), $constraint, 'tableName');
        $validatorFactory    = $this->createValidatorFactoryMock($constraintValidator, get_class($constraint));

        $validator = $this->createValidator($metadataFactory, $validatorFactory);
        $this->assertEquals(0, count($validator->validate($validatedObj)));
    }

    /**
     * Table does not exist
     */
    public function testInvalid()
    {
        $tableName      = 'tableName';
        $validatedObj = new TableExistsFixture($tableName);
        $constraint     = new TableExists();
        $this->sm->expects($this->once())->method('listTableNames')->will($this->returnValue(array()));

        $constraintValidator = new TableExistsValidator($this->conn);
        $metadataFactory     = $this->createMetadataFactoryMock(get_class($validatedObj), $constraint, 'tableName');
        $validatorFactory    = $this->createValidatorFactoryMock($constraintValidator, get_class($constraint));

        $validator = $this->createValidator($metadataFactory, $validatorFactory);
        $this->assertEquals(1, count($validator->validate($validatedObj)));
    }
}