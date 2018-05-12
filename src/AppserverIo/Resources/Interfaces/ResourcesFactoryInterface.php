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

namespace AppserverIo\Resources\Interfaces;

use AppserverIo\Lang\String;

/**
 * This class bundles several resource files to a so called resource bundle.
 *
 * It also provides several functions to handle resource files, like an import/export functionality.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/resources
 * @link      http://www.appserver.io
 */
interface ResourcesFactoryInterface
{

    /**
     * Create (if necessary) and return a Resources instance for the specified logical name, with a
     * configuration based on the specified configuration string.
     *
     * @param \AppserverIo\Lang\String $name   The name of the resources
     * @param \AppserverIo\Lang\String $config The path to the configuration file
     *
     * @return \AppserverIo\Resources\Interfaces\ResourcesInterface The Resources instance
     * @throws \AppserverIo\Resources\Exceptions\ResourcesException Is thrown if an exception occurs when the Resources are initialized
     */
    public function getResources(String $name, String $config = null);

    /**
     * Return the returnNull property value that will be configured on Resources instances created
     * by this factory.
     *
     * @return boolean TRUE if null is returned for invalid key values.
     */
    public function isReturnNull();

    /**
     * Set the returnNull property value that will be configured on Resources instances created
     * by this factory.
     *
     * @param boolean $returnNull The new value to delegate
     *
     * @return void
     */
    public function setReturnNull($returnNull);

    /**
     * Release any internal references to Resources instances that have been returned previously,
     * after calling the destroy() method on each such instance.
     *
     * @return void
     */
    public function release();
}
