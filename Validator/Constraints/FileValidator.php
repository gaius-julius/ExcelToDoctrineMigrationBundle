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

use Symfony\Component\Validator\Constraints\FileValidator as BaseValidator;
use Symfony\Component\HttpFoundation\File\File as FileObject;
use Symfony\Component\Validator\Constraint;

/**
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class FileValidator extends BaseValidator
{
    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint)
    {
        parent::validate($value, $constraint);
        
        if ($constraint->extensions) {
            if (!$value instanceof FileObject) {
                $value = new FileObject($value);
            }

            $extensions = (array) $constraint->extensions;
            $extension = $value->getExtension();

            if (false === in_array($extension, $extensions)) {
                $path = $value->getPathname();
                $this->context->addViolation($constraint->extensionsMessage, array(
                    '{{ extension }}'  => '"'.$extension.'"',
                    '{{ extensions }}' => '"'.implode('", "', $extensions) .'"',
                    '{{ file }}'       => $path,
                ));
            }
        }
    }
}
