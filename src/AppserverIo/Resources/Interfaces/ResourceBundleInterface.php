<?php

/**
 * AppserverIo\Resources\Interfaces\ResourceBundleInterface
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

use AppserverIo\Collections\ArrayList;

/**
 * This is the interface for all resource bundles.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/resources
 * @link      http://www.appserver.io
 */
interface ResourceBundleInterface
{

    /**
     * Create (if necessary) and return a Resources instance for the specified logical name, with a
     * configuration based onvthe specified configuration String.
     *
     * @return void
     * @throws \AppserverIo\Resources\Exceptions\ResourcesException Is thrown if an exception occurs when the Resources are initialized
     */
    public function initialize();

    /**
     * Release any internal references to Resources instances that have been returned previously, after
     * calling the destroy() method on each such instance.
     *
     * @return void
     */
    public function destroy();

    /**
     * This method returns the system locale instance.
     *
     * @return \AppserverIo\Resources\SystemLocale The system locale
     */
    public function getSystemLocale();

    /**
     * Returns the keys of this resource bundle instance
     * as an ArrayList.
     *
     * @return \AppserverIo\Collections\ArrayList Holds the keys of this resource bundle instance
     */
    public function getKeys();

    /**
     * This method searches in the container for the resource with the key passed as parameter.
     *
     * @param string                             $name      Holds the key of the requested resource
     * @param \AppserverIo\Collections\ArrayList $parameter Holds an ArrayList with parameters with replacements for the placeholders in the resource string
     *
     * @return string Holds the requested resource value
     * @throws \AppserverIo\Resources\Exceptions\ResourcesException Is thrown if an error occurs retrieving or returning the requested content
     * @throws \AppserverIo\Resources\Exceptions\ResourcesKeyException Is thrown if the no value for the specified key was found, and isReturnNull() returns FALSE
     */
    public function find($name, ArrayList $parameter = null);
}
