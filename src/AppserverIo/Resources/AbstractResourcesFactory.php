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
use AppserverIo\Collections\HashMap;
use AppserverIo\Resources\Interfaces\ResourcesFactoryInterface;

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
abstract class AbstractResourcesFactory implements ResourcesFactoryInterface
{

    /**
     * Return the returnNull property value that will be configured on Resources instances created by this factory.
     *
     * @var boolean
     */
    protected $returnNull = true;

    /**
     * This HashMap holds the initialized Resources instances.
     *
     * @var \AppserverIo\Collections\HashMap
     */
    protected $resources = null;

    /**
     * The constructor initializes the internal members necessary for handling the initialized resources.
     *
     * @return void
     */
    public function __construct()
    {
        $this->resources = new HashMap();
    }

    /**
     * Create and return a new resources instance with the specified logical name, after calling its init()
     * method and delegating the relevant properties. Concrete subclasses MUST implement this method.
     *
     * @param \AppserverIo\Lang\String $name   Holds the logical name of the Resources to create
     * @param \AppserverIo\Lang\String $config Holds the optional string to the configuration
     *
     * @return \AppserverIo\Resources\Interfaces\ResourcesInterface Holds the initialized resources
     */
    abstract protected function createResources(String $name, String $config = null);

    /**
     * Create (if necessary) and return a Resources instance for the specified logical name, with a
     * configuration based on the specified configuration string.
     *
     * @param \AppserverIo\Lang\String $name   The name of the resources
     * @param \AppserverIo\Lang\String $config The path to the configuration file
     *
     * @return \AppserverIo\Resources\Interfaces\ResourcesInterface The Resources instance
     * @throws \AppserverIo\Resources\Exceptions\ResourcesException Is thrown if an exception occurs when the Resources are initialized
     * @see \AppserverIo\Resources\Interfaces\ResourcesFactoryInterface::getResources()
     */
    public function getResources(String $name, String $config = null)
    {

        // check if the resources already exists
        if (!$this->resources->exists($name)) {
            // if not, initialize them
            $this->resources->add($name, $this->createResources($name, $config));
        }

        // return the requested resources
        return $this->resources->get($name);
    }

    /**
     * Return the returnNull property value that will be configured on Resources instances created
     * by this factory.
     *
     * @return boolean TRUE if null is returned for invalid key values.
     * @see \AppserverIo\Resources\Interfaces\ResourcesFactoryInterface::isReturnNull()
     */
    public function isReturnNull()
    {
        return $this->returnNull;
    }

    /**
     * Set the returnNull property value that will be configured on Resources instances created
     * by this factory.
     *
     * @param boolean $returnNull The new value to delegate
     *
     * @return void
     * @see \AppserverIo\Resources\Interfaces\ResourcesFactoryInterface::setReturnNull( $returnNull)
     */
    public function setReturnNull($returnNull)
    {
        $this->returnNull = $returnNull;
    }

    /**
     * Release any internal references to Resources instances that have been returned previously,
     * after calling the destroy() method on each such instance.
     *
     * @return void
     * @see \AppserverIo\Resources\Interfaces\ResourcesFactoryInterface::release()
     */
    public function release()
    {

        // invoke the destroy method on each instace and delete it from the internal array
        foreach ($this->resources as $name => $resources) {
            // invoke the destroy method of the resources
            $resources->destroy();
            // remove the resources from the internal HashMap
            $this->resources->remove($name);
        }
    }
}
