<?php

 /*
 * This file is part of the ExcelToDoctrineMigratorBundle package.
 *
 * (c) Dmitry Bykov <dmitry.bykov@sibers.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sibers\ExcelToDoctrineMigrationBundle\Exception;

/**
 * ValueProviderNotFoundException
 * 
 * throws if value provider does not exist
 */
class ValueProviderNotFoundException extends \InvalidArgumentException {

    /**
     * Constructor
     *
     * @param string $valueProviderName value provider name
     * @param array $availableProviders available value providers
     */
    public function __construct($valueProviderName, array $availableProviders = array())
    {
        $message = sprintf(
            'Value provider "%s" was not found. Available providers are: %s',
            $valueProviderName,
            json_encode($availableProviders)
        );
        
        parent::__construct($message);
    }
}