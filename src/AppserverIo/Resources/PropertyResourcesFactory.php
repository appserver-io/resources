<?php

/**
 * AppserverIo\Resources\Interfaces\Resources
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/resources
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Resources;

use AppserverIo\Lang\String;

/**
 * Factory for all property based resources.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/resources
 * @link      http://www.appserver.io
 */
class PropertyResourcesFactory extends AbstractResourcesFactory
{

    /**
     * Create and return a new resources instance with the specified logical name, after calling its init()
     * method and delegating the relevant properties. Concrete subclasses MUST implement this method.
     *
     * @param \AppserverIo\Lang\String $name   Holds the logical name of the Resources to create
     * @param \AppserverIo\Lang\String $config Holds the optional string to the configuration
     *
     * @return \AppserverIo\Resources\Interfaces\ResourcesInterface Holds the initialized resources
     * @see \AppserverIo\Resources\AbstractResourcesFactory::createResources();
     */
    protected function createResources(String $name, String $config = null)
    {

        // initialize the new Resources
        $resources = new PropertyResources($name, $config);
        $resources->initialize();
        $resources->setReturnNull($this->isReturnNull());

        // return the resources
        return $resources;
    }
}
