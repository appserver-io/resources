<?PHP

/**
 * AppserverIo\Resources\DBResources
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
use AppserverIo\Collections\ArrayList;
use AppserverIo\Resources\Exceptions\ResourcesKeyException;

/**
 * This class acts as a container resources stored in a database.
 *
 * Properties for the database connection are:
 *
 * db.connect.dsn = sqlite:/tmp/my-database.sqlite
 * db.connect.user =
 * db.connect.password =
 * db.sql.table = resources
 * db.sql.locale.column = locale
 * db.sql.key.column = msgKey
 * db.sql.val.column = val
 * resource.cache = true
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/resources
 * @link      http://www.appserver.io
 */
class DBResources extends AbstractResources
{

    /**
     * Holds the data directory with the path to the resource files to export.
     *
     * @var \AppserverIo\Lang\String
     */
    protected $config = null;

    /**
     * The constructor initializes the resources with the database connection to load the resources from.
     *
     * @param \AppserverIo\Lang\String $name   Holds the logical name of the Resources to create
     * @param \AppserverIo\Lang\String $config Holds the optional string to the configuration
     */
    public function __construct(String $name, String $config = null)
    {

        // initialize the name
        parent::__construct($name);

        // initialize the members with the passed values
        $this->config = $config;
    }

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
     * @see \AppserverIo\Resources\Interfaces\ResourcesInterface::find()
     */
    public function find($name, SystemLocale $systemLocale = null, ArrayList $parameter = null)
    {

        // if no system locale is passed, use the default one
        if ($systemLocale == null) {
            $systemLocale = $this->getDefaultSystemLocale();
        }

        // check if the property resources bundle has already been loaded
        if (!$this->exists($systemLocale)) {
            // load the resource bundle and return the value
            $this->add(DBResourceBundle::getBundle($this->config, $systemLocale));
        }

        // return the requested resource value
        $value = $this->get($systemLocale)->find($name, $parameter);

        // check if an exception should be thrown if the requested value is null
        if (($value == null) && ($this->isReturnNull() == false)) {
            throw new ResourcesKeyException('Found no value for requested resource ' . $name);
        }

        // return the requested value
        return $value;
    }

    /**
     * This method returns the first key found for
     * the passed value.
     *
     * @param string $value Holds the resource value to return the key for
     * @return string Holds the resource key for the passed value
     */
    public function findKeyByValue($value)
    {
        // @TODO Still to implement
    }

    /**
     * This method returns the number of resources in the container.
     *
     * @return integer Number of resources in the container
     */
    public function count()
    {
        // @TODO Still to implement
    }
}
