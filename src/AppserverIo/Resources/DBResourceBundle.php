<?PHP

/**
 * AppserverIo\Resources\DBResourceBundle
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
class DBResourceBundle extends AbstractResourceBundle
{

    /**
     * Holds the the DSN used to connect to the database.
     *
     * @var string
     */
    const DB_CONNECT_DSN = 'db.connect.dsn';

    /**
     * Holds the the name of the key with the database user to use.
     *
     * @var string
     */
    const DB_CONNECT_USER = 'db.connect.user';

    /**
     * Holds the the name of the key with the database password to use.
     *
     * @var string
     */
    const DB_CONNECT_PASSWORD = 'db.connect.password';

    /**
     * Holds the name of the database table where the properties are stored.
     *
     * @var string
     */
    const DB_SQL_TABLE = "db.sql.table";

    /**
     * Holds the name of the column with the locale stored.
     *
     * @var string
     */
    const DB_SQL_LOCALE_COLUMN = "db.sql.locale.column";

    /**
     * Holds the name of the column with the key stored.
     *
     * @var string
     */
    const DB_SQL_KEY_COLUMN = "db.sql.key.column";

    /**
     * Holds the name of the column with the value stored.
     *
     * @var string
     */
    const DB_SQL_VAL_COLUMN = "db.sql.val.column";

    /**
     * Holds the cache flag to cache the alread loaded properties.
     *
     * @var string
     */
    const RESOURCE_CACHE = "resource.cache";

    /**
     * Holds the Properties necessary to initialize the database connection.
     *
     * @var Properties
     */
    protected $properties = null;

    /**
     * Holds the database connection to request the resources from.
     *
     * @var \PDO
     */
    protected $db = null;

    /**
     * Holds the flag that resources should be cached or not.
     *
     * @var boolean
     */
    protected $cacheResources = true;

    /**
     * Holds the path to the database configuration file.
     *
     * @var \AppserverIo\Lang\String
     */
    protected $config = null;

    /**
     * Initializes the resource bundle with the configuration and the locale to use.
     *
     * @param \AppserverIo\Lang\String            $config       The configuration for the database connection
     * @param \AppserverIo\Resources\SystemLocale $systemLocale The system locale to use
     *
     * @return void
     */
    protected function __construct(String $config, SystemLocale $systemLocale)
    {
        parent::__construct($systemLocale);
        $this->config = $config;
    }

    /**
     * This method initializes the property resource bundle for the propertees with the
     * passed configuration file name and locale.
     *
     * @param \AppserverIo\Lang\String            $config       Holds the name of the property file to load the configuration from
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
        $bundle = new DBResourceBundle($config, $systemLocale);
        $bundle->initialize();
        return $bundle;
    }

    /**
     *
     * /**
     * This method initializes the database connection
     * and returns id.
     *
     * @return \PDO Holds the initialized database connection
     * @throws \AppserverIo\Resources\Exceptions\ResourcesException Is thrown if an error while conneting to the database occurs
     */
    public function initialize()
    {

        // load the properties
        $this->properties = new Properties();
        $this->properties->load($this->config . ".properties");

        // initialize the flag to cache the resources
        $this->cacheResources = (boolean) $this->properties->getProperty(DBResourceBundle::RESOURCE_CACHE);

        // initialize the database connection
        $this->db = new \PDO(
            $this->properties->getProperty(DBResourceBundle::DB_CONNECT_DSN),
            $this->properties->getProperty(DBResourceBundle::DB_CONNECT_USER),
            $this->properties->getProperty(DBResourceBundle::DB_CONNECT_PASSWORD)
        );

        // we want exceptions if connection can not be established
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * This method disconnects from the database and frees the memory.
     *
     * @return void
     */
    public function destroy()
    {
        $this->db = null;
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

        // initialize the SQL statement
        $stmt = sprintf(
            "SELECT `%s` FROM `%s` WHERE `%s` = ? AND `%s` = ?",
            $this->properties->getProperty(DBResourceBundle::DB_SQL_VAL_COLUMN),
            $this->properties->getProperty(DBResourceBundle::DB_SQL_TABLE),
            $this->properties->getProperty(DBResourceBundle::DB_SQL_KEY_COLUMN),
            $this->properties->getProperty(DBResourceBundle::DB_SQL_LOCALE_COLUMN)
        );

        // prepare and execute the statement to load the requested resource string from the database
        $statement = $this->db->prepare($stmt);

        // get the result with the values
        $statement->execute(array($name, $this->getSystemLocale()->__toString()));

        // load the found row
        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $resource = $row[$this->properties->getProperty(DBResourceBundle::DB_SQL_VAL_COLUMN)];
        }

        // check if parameter for replacement are passed
        if ($parameter != null) {
            // replace the placeholders with the passed parameter
            foreach ($parameter as $key => $value) {
                $resource = str_replace($key . "?", $value, $resource);
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

        // initialize the SQL statement
        $stmt = sprintf(
            "UPDATE `%s` SET `%s` = ?, `%s` = ? WHERE `%s` = ?",
            $this->properties->getProperty(DBResourceBundle::DB_SQL_TABLE),
            $this->properties->getProperty(DBResourceBundle::DB_SQL_VAL_COLUMN),
            $this->properties->getProperty(DBResourceBundle::DB_SQL_LOCALE_COLUMN),
            $this->properties->getProperty(DBResourceBundle::DB_SQL_KEY_COLUMN)
        );

        // prepare and execute the statement to load the keys
        $statement = $this->db->prepare($stmt);

        // replace the value for the resource with the passed name
        $statement->execute(array($value, $this->getSystemLocale()->__toString(), $name));
    }

    /**
     * This method attaches the resource string with the passed key in the resource file.
     *
     * If the resource string already exists, the old one will be kept and the function
     * returns FALSE, else it returns TRUE
     *
     * @param string $name  Holds the name of the resource string to replace
     * @param string $value Holds the value to replace the original one with
     *
     * @return void
     */
    public function attach($name, $value)
    {

        // initialize the SQL statement
        $stmt = sprintf(
            "INSERT INTO `%s` (`%s`, `%s`, `%s`) VALUES (?, ?, ?)",
            $this->properties->getProperty(DBResourceBundle::DB_SQL_TABLE),
            $this->properties->getProperty(DBResourceBundle::DB_SQL_KEY_COLUMN),
            $this->properties->getProperty(DBResourceBundle::DB_SQL_VAL_COLUMN),
            $this->properties->getProperty(DBResourceBundle::DB_SQL_LOCALE_COLUMN)
        );

        // prepare and execute the statement to attach the resources
        $statement = $this->db->prepare($stmt);

        // try to excecute the statement
        $statement->execute(array($name, $value, $this->getSystemLocale()->__toString()));
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

        // initialize the SQL statement
        $stmt = sprintf(
            "SELECT `%s` FROM `%s` WHERE `%s` = ? AND `%s` = ?",
            $this->properties->getProperty(DBResourceBundle::DB_SQL_KEY_COLUMN),
            $this->properties->getProperty(DBResourceBundle::DB_SQL_TABLE),
            $this->properties->getProperty(DBResourceBundle::DB_SQL_VAL_COLUMN),
            $this->properties->getProperty(DBResourceBundle::DB_SQL_LOCALE_COLUMN)
        );

        // prepare the statement to load the requested resource key from the database
        $statement = $this->db->prepare($stmt);

        // execute the statement
        $statement->execute(array($value, $this->getSystemLocale()->__toString()));

        // iterate over the result
        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $resource = $row[$this->properties->getProperty(DBResourceBundle::DB_SQL_KEY_COLUMN)];
        }

        // return the resource string
        return $resource;
    }

    /**
     * This method returns the number of resources in the container.
     *
     * @return integer Number of resources in the container
     */
    public function count()
    {

        // initialize the SQL statement
        $stmt = sprintf(
            "SELECT COUNT(`%s`) AS size FROM `%s` WHERE `%s` = ? GROUP BY `%s`",
            $this->properties->getProperty(DBResourceBundle::DB_SQL_KEY_COLUMN),
            $this->properties->getProperty(DBResourceBundle::DB_SQL_TABLE),
            $this->properties->getProperty(DBResourceBundle::DB_SQL_LOCALE_COLUMN),
            $this->properties->getProperty(DBResourceBundle::DB_SQL_KEY_COLUMN)
        );

        // prepare the statement
        $statement = $this->db->prepare($stmt);

        // execute the statement count the resources
        $statement->execute(array($this->getSystemLocale()->__toString()));

        // return the size of the found resources
        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            return $row["size"];
        }
    }

    /**
     * This method saves all resources back to
     * the database.
     *
     * @return void
     */
    public function save()
    {
        // Nothing to do here, because all values are saved immediately
    }

    /**
     * Returns the keys of this ResourceBundle instance as an ArrayList.
     *
     * @return \AppserverIo\Collections\ArrayList Holds the keys of this ResourceBundle instance
     */
    public function getKeys()
    {

        // initialize the ArrayList with the keys
        $list = new ArrayList();

        // initialize the SQL statement
        $stmt = sprintf(
            "SELECT `%s` FROM `%s` WHERE `%s` = ? GROUP BY `%s`",
            $this->properties->getProperty(DBResourceBundle::DB_SQL_KEY_COLUMN),
            $this->properties->getProperty(DBResourceBundle::DB_SQL_TABLE),
            $this->properties->getProperty(DBResourceBundle::DB_SQL_LOCALE_COLUMN),
            $this->properties->getProperty(DBResourceBundle::DB_SQL_KEY_COLUMN)
        );

        // prepare the statement to load the keys
        $statement = $this->db->prepare($stmt);

        // execute the statement
        $statement->execute(array($this->getSystemLocale()->__toString()));

        // build the ArrayList
        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $list->add($row[$this->properties->getProperty(DBResourceBundle::DB_SQL_KEY_COLUMN)]);
        }

        // return the ArrayList with the keys
        return $list;
    }
}
