<?php

/**
 * AppserverIo\ResourcesInterface\Interfaces\ResourcesInterface
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
use AppserverIo\Resources\SystemLocale;

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
interface ResourcesInterface
{

    /**
     * Create (if necessary) and return a ResourcesInterface instance for the specified logical name, with a
     * configuration based on the specified configuration string.
     *
     * @return void
     * @throws \AppserverIo\Resources\Exceptions\ResourcesException Is thrown if an exception occurs when the ResourcesInterface are initialized
     */
    public function initialize();

    /**
     * Release any internal references to ResourcesInterface instances that have been returned previously, after
     * calling the destroy() method on each such instance.
     *
     * @return void
     */
    public function destroy();

    /**
     * Return TRUE if resource getter methods will return null instead of throwing an exception on invalid
     * key values.
     *
     * @return boolean TRUE if null is returned for invalid key values.
     */
    public function isReturnNull();

    /**
     * Set a flag determining whether resource getter methods should return null instead of throwing an
     * exception on invalid key values.
     *
     * @param boolean $returnNull The new flag value
     * @return void
     */
    public function setReturnNull($returnNull);

    /**
     * Return the logical name of this ResourcesInterface instance.
     *
     * @return \AppserverIo\Lang\String Holds the logical name of this ResourcesInterface instance
     */
    public function getName();

    /**
     * Returns the keys of this ResourcesInterface instance
     * as an ArrayList.
     *
     * @return \AppserverIo\Collections\ArrayList Holds the keys of this ResourcesInterface instance
     */
    public function getKeys();

    /**
     * This method searches in the container for the resource with the key passed as parameter.
     *
     * @param string                              $name         Holds the key of the requested resource
     * @param \AppserverIo\Resources\SystemLocale $systemLocale Holds the SystemLocale with which to localize retrieval, or null for the default SystemLocale
     * @param \AppserverIo\Collections\ArrayList  $parameter    Holds an ArrayList with parameters with replacements for the placeholders in the resource string
     *
     * @return string Holds the requested resource value
     * @throws \AppserverIo\Resources\Exceptions\ResourcesException Is thrown if an error occurs retrieving or returning the requested content
     * @throws \AppserverIo\Resources\Exceptions\ResourcesKeyException Is thrown if the no value for the specified key was found, and isReturnNull() returns false
     */
    public function find($name, SystemLocale $systemLocale = null, ArrayList $parameter = null);
}
