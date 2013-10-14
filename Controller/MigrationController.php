<?php

 /*
 * This file is part of the ExcelToDoctrineMigratorBundle package.
 *
 * (c) Dmitry Bykov <dmitry.bykov@sibers.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sibers\ExcelToDoctrineMigrationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Sibers\ExcelToDoctrineMigrationBundle\Form\Type\ConfigType;

class ConfigController extends  Controller {

    /**
     * List of configs
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(new ConfigType());

        if ($request->request->has($form->getName())) {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $file = $data['config'];

            }
        }
    }

    public function deleteAction($filename)
    {

    }

    public function downloadAction($filename)
    {

    }
}