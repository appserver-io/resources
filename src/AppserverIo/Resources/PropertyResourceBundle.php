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
use AppserverIo\Properties\Properties;
use AppserverIo\Collections\ArrayList;

/**
 * This class is a container for the resources and provides methods for handling them.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/resources
 * @link      http://www.appserver.io
 */
class PropertyResourceBundle extends AbstractResourceBundle
{

    /**
     * Holds the optional separator for the filename and the locale key.
     *
     * @var string
     */
    const SEPARATOR = '_';

    /**
     * Path to the property file with the resource properties.
     *
     * @var \AppserverIo\Lang\String
     */
    protected $config = null;

    /**
     * The initialized resource properties,
     *
     * @var \AppserverIo\Properties\Properties
     */
    protected $properties = null;

    /**
     * Initializes the resource bundle with the property resources from the passed file and the locale to use.
     *
     * @param \AppserverIo\Lang\String            $config       The path to the property file with the resources
     * @param \AppserverIo\Resources\SystemLocale $systemLocale The system locale to use
     */
    protected function __construct(String $config, SystemLocale $systemLocale)
    {

        // initialize the configuration
        $this->config = $config;

        // invoke the parent method
        parent::__construct($systemLocale);
    }

    /**
     * This method initializes the property resource bundle for the properties with the passed name and locale.
     *
     * @param \AppserverIo\Lang\String            $config       Holds the name of the property file to load the resources from
     * @param \AppserverIo\Resources\SystemLocale $systemLocale Holds the system locale of the property resource bundle to load
     *
     * @return \AppserverIo\Resources\PropertyResourceBundle Holds the initialized property resource bundle
     */
    public static function getBundle(String $config, SystemLocale $systemLocale = null)
    {

        // use the default system locale if no locale has been passed
        if ($systemLocale == null) {
            $systemLocale = SystemLocale::getDefault();
        }

        // initialize and return the resource bundle
        $bundle = new PropertyResourceBundle($config, $systemLocale);
        $bundle->initialize();
        return $bundle;
    }

    /**
     * This method parses the resource file depending on the actual locale and sets the values in the internal array.
     *
     * The separator default value is . but can be changed to some other value by passing it as paramter to this method.
     *
     * For example the filename for an application could be 'applicationresources.en_US.properties'.
     *
     * @return void
     */
    public function initialize()
    {

        // add the Locale as string
        $this->config .= PropertyResourceBundle::SEPARATOR . $this->getSystemLocale()->__toString() . ".properties";

        // initialize and load the Properties
        $this->properties = new Properties();
        $this->properties->load($this->config);
    }

    /**
     * This method disconnects from the
     * database and frees the memory.
     *
     * @return void
     */
    public function destroy()
    {
        // @TODO Still to implement
    }

    /**
     * This method searches in the container for the resource with the key passed as parameter.
     *
     * @param string                             $name      Holds the key of the requested resource
     * @param \AppserverIo\Collections\ArrayList $parameter Holds an ArrayList with parameters with replacements for the placeholders in the resource string
     *
     * @return string Holds the requested resource value
     */
    public function find($name, ArrayList $parameter = null)
    {

        // initialize an find the resource string
        $resource = "";

        // get the value for the property with the passed key
        if (($property = $this->properties->getProperty($name)) != null) {
            $resource = $property;
        }

        // check if parameter for replacement are passed
        if ($parameter != null) {
            // replace the placeholders with the passed parameter
            foreach ($parameter as $key => $value) {
                $resource = str_replace('{' . $key . '}', $value, $resource);
            }
        }

        // return the resource string
        return $resource;
    }

    /**
     * This method replaces the resource string with the passed key in the resource file.
     *
     * If the resource string does not yes exist, the value will be attached
     *
     * @param string $name  Holds the name of the resource string to replace
     * @param string $value Holds the value to replace the original one with
     *
     * @return void
     */
    public function replace($name, $value)
    {
        $this->properties->add($name, $value);
    }

    /**
     * This method attaches the resource string with the passed key in the resource file.
     *
     * If the resource string already exists, the old one will be kept and the function returns FALSE, else it returns TRUE
     *
     * @param string $name  Holds the name of the resource string to replace
     * @param string $value Holds the value to replace the original one with
     *
     * @return boolean FALSE if a resource string with the passed name already exists
     */
    public function attach($name, $value)
    {

        // query whether or not the value with the passed key already exits
        if ($this->properties->exists($name)) {
            return false;
        }

        // if not, add the passed resource to the bundle
        $this->properties->add($name, $value);
        return true;
    }

    /**
     * This method returns the first key found for the passed value.
     *
     * @param string $value Holds the resource value to return the key for
     *
     * @return string Holds the resource key for the passed value
     */
    public function findKeyByValue($value)
    {

        // try to find the key for the passed value
        $key = array_search($value, $this->properties->toIndexedArray());

        // check if the value exists in the resource bundle
        if ($key === false) {
            return;
        }

        // return the found key
        return $key;
    }

    /**
     * This method returns the number of resources in the container.
     *
     * @return integer Number of resources in the container
     */
    public function count()
    {
        return $this->properties->size();
    }

    /**
     * This method saves the resource string back to the resource file.
     *
     * @return void
     */
    public function save()
    {
        $this->properties->store($this->config);
    }

    /**
     * Returns the keys of this resource bundle instance as an ArrayList.
     *
     * @return \AppserverIo\Collections\ArrayList Holds the keys of this resource bundle instance
     */
    public function getKeys()
    {
        return new ArrayList($this->properties->getKeys());
    }
}
