<?php

 /*
 * This file is part of the ExcelToDoctrineMigratorBundle package.
 *
 * (c) Dmitry Bykov <dmitry.bykov@sibers.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sibers\ExcelToDoctrineMigrationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Sibers\ExcelToDoctrineMigrationBundle\Validator\Constraints\File;

class ConfigType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('config', 'file', array(
            'constraints' => array(
                new File(array('extensions' => array('xls', 'xlsx')))
            )
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'config';
    }
}