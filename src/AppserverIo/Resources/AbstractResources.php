<?PHP

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
use AppserverIo\Collections\ArrayList;
use AppserverIo\Resources\Interfaces\ResourcesInterface;
use AppserverIo\Resources\Interfaces\ResourceBundleInterface;
use AppserverIo\Resources\Exceptions\SystemLocaleNotExistsException;

/**
 * Abstract class of all resources.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/resources
 * @link      http://www.appserver.io
 */
abstract class AbstractResources implements ResourcesInterface
{

    /**
     * Holds the flag to return null if a requested propert does not exists.
     *
     * @var boolean
     */
    protected $returnNull = true;

    /**
     * Holds the default system locale of this resources.
     *
     * @var \AppserverIo\Resources\SystemLocale
     */
    protected $defaultSystemLocale = null;

    /**
     * Holds the name of the resource bundle
     *
     * @var \AppserverIo\Lang\String
     */
    protected $name = null;

    /**
     * Holds the HashMap with the initialized ResourceBundle instances.
     *
     * @var \AppserverIo\Collections\HashMap
     */
    protected $bundles = null;

    /**
     * The constructor initializes the resources with the database connection to
     * load the resources from.
     *
     * @param \AppserverIo\Lang\String $name Holds the logical name of the Resources to create
     *
     * @return void
     */
    public function __construct(String $name)
    {
        $this->setName($name);
    }

    /**
     * This method sets the name of the resource bundle.
     *
     * @param \AppserverIo\Lang\String $name The name of the resource bundle
     *
     * @return void
     */
    public function setName(String $name)
    {
        $this->name = $name;
    }

    /**
     * This method returns the name of the resource bundle.
     *
     * @return \AppserverIo\Lang\String The name of the resource bundle
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return TRUE if resource getter methods will return null instead of throwing an exception on invalid
     * key values.
     *
     * @return boolean TRUE if null is returned for invalid key values.
     * @see \AppserverIo\Resources\Interfaces\ResourcesInterface::isReturnNull()
     */
    public function isReturnNull()
    {
        return $this->returnNull;
    }

    /**
     * Set a flag determining whether resource getter methods should return null instead of throwing an
     * exception on invalid key values.
     *
     * @param boolean $returnNull The new flag value
     *
     * @return void
     * @see \AppserverIo\Resources\Interfaces\ResourcesInterface::setReturnNull($returnNull)
     */
    public function setReturnNull($returnNull)
    {
        $this->returnNull = $returnNull;
    }

    /**
     * Sets the default system locale for this resources.
     *
     * @param \AppserverIo\Resources\SystemLocale $systemLocale The default system locale for this resources
     *
     * @return void
     */
    public function setDefaultSystemLocale(SystemLocale $systemLocale)
    {
        $this->defaultSystemLocale = $systemLocale;
    }

    /**
     * Returns the default system locale for this resources.
     *
     * @return \AppserverIo\Resources\SystemLocale The default system locale for this resources
     */
    public function getDefaultSystemLocale()
    {
        return $this->defaultSystemLocale;
    }

    /**
     * Create (if necessary) and return a Resources instance for the specified logical name, with a
     * configuration based on the specified configuration string.
     *
     * @return void
     * @throws \AppserverIo\Resources\Exceptions\ResourcesException Is thrown if an exception occurs when the Resources are initialized
     * @see \AppserverIo\Resources\Interfaces\ResourcesInterface::initialize()
     */
    public function initialize()
    {
        $this->bundles = new HashMap();
    }

    /**
     * Release any internal references to Resources instances that have been returned previously, after
     * calling the destroy() method on each such instance.
     *
     * @return void
     * @see \AppserverIo\Resources\Interfaces\ResourcesInterface::destroy()
     */
    public function destroy()
    {

        // invoke the destroy method of the resource bundle instances
        foreach ($this->bundles as $bundle) {
            $bundle->destroy();
        }

        // reset the HashMap
        $this->bundles = new HashMap();
    }

    /**
     * This method saves all resource files back to the file system.
     *
     * @return void
     */
    public function save()
    {

        // iterate over all resources and save them
        foreach ($this->bundles as $resourcesBundle) {
            $resourcesBundle->save();
        }
    }

    /**
     * Returns the keys of this Resources instance
     * as an ArrayList.
     *
     * @return \AppserverIo\Collections\ArrayList Holds the keys of this Resources instance
     * @see \AppserverIo\Resources\Interfaces\ResourcesInterface::getKeys()
     */
    public function getKeys()
    {

        // initialize the array with the keys
        $keys = array();

        // iterate over all bundles and the keys of the bundles
        foreach ($this->bundles as $bundle) {
            foreach ($bundle->getKeys() as $key) {
                if (!in_array($key, $keys)) {
                    $keys[] = $key;
                }
            }
        }

        // return the ArrayList with the unique keys
        return new ArrayList($keys);
    }

    /**
     * This method adds the passed resource bundle instance to the HashMap with the initialized bundles.
     *
     * If resources with the same system locale as the system locale of the passed resource bundle instance
     * exists, they will be replaced by the passed one.
     *
     * @param \AppserverIo\Resources\Interfaces\ResourceBundleInterface $newBundle Holds the resource bundle to add
     *
     * @return void
     */
    protected function add(ResourceBundleInterface $newBundle)
    {
        $this->bundles->add($newBundle->getSystemLocale()->__toString(), $newBundle);
    }

    /**
     * This method returns the resources for the passed system locale.
     *
     * If the requested resources does not exist an exception is thrown.
     *
     * @param \AppserverIo\Resources\SystemLocale $systemLocale Holds the system locale to return the resources for
     *
     * @return \AppserverIo\Resources\Interfaces\ResourceBundleInterface Holds the initialized resource bundle instance
     * @throws \AppserverIo\Resources\Exceptions\SystemLocaleNotExistsException Is throw if the requested resources is not available
     */
    protected function get(SystemLocale $systemLocale)
    {

        // check if resources for the passed system locale exists
        if (!$this->exists($systemLocale)) {
            throw new SystemLocaleNotExistsException('ResourceBundle for system locale ' . $systemLocale->__toString() . ' is not available');
        }

        // returns the requested resources
        return $this->bundles->get($systemLocale->__toString());
    }

    /**
     * This method checks if resources for the passed system locale already exists in the bundle or not.
     *
     * @param \AppserverIo\Resources\SystemLocale $systemLocale Holds the system locale to check for
     *
     * @return boolean TRUE if resources for the passed system locale exists, else FALSE
     */
    protected function exists(SystemLocale $systemLocale)
    {
        return $this->bundles->exists($systemLocale->__toString());
    }
}
