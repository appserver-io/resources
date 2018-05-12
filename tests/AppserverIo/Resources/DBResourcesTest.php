<?php

/**
 * AppserverIo\Resources\PropertyResourcesTest
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

/**
 * This is the test for the database resources.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/resources
 * @link      http://www.appserver.io
 */
class DBResourcesTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The properties to use for DB connection/configuration.
     *
     * @var string
     */
    protected $properties;

    /**
     * The path to the DBResources configuration file.
     *
     * @var string
     */
    protected $configPath = __DIR__ . '/dbresources';

    /**
     * Return's the default properties.
     *
     * @return void
     */
    protected function initializeProperties()
    {

        // load and merge the properties
        $this->properties = new Properties();
        $this->properties->load(sprintf('%s.properties', $this->configPath));
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {

        // initialize the properties
        $this->initializeProperties();

        // initialize the database connection
        $this->db = new \PDO(
            $this->properties->getProperty(DBResourceBundle::DB_CONNECT_DSN),
            $this->properties->getProperty(DBResourceBundle::DB_CONNECT_USER),
            $this->properties->getProperty(DBResourceBundle::DB_CONNECT_PASSWORD)
        );

        // we want exceptions if connection can not be established
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // create the table for the resources
        $this->db->exec(
            "CREATE TABLE `resources` (
                `msg_key` varchar(255) NOT NULL,
                `locale` varchar(5) NOT NULL,
                `val` varchar(255) NOT NULL,
                PRIMARY KEY (`msg_key`,`locale`)
            )"
        );

        // pre-initialize the table with some resources
        $this->db->exec(
            "INSERT INTO `resources` (`msg_key`, `locale`, `val`) VALUES
                ('test.key', 'de_DE', 'Testwert'),
                ('test.key', 'en_US', 'Testvalue');"
        );
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        $this->db->query('DROP TABLE `resources`');
    }

    /**
     * This tests the find() method of the PropertyResources instance.
     *
     * @return void
     */
    public function testPropertyResourcesFind()
    {

        // initialize the factory
        $factory = new DBResourcesFactory();

        // load the resources
        $dbResources = $factory->getResources(
            new String('DBResources'),
            new String($this->configPath)
        );

        // check the correct values to load
        $this->assertEquals('Testwert', $dbResources->find('test.key', SystemLocale::create(SystemLocale::GERMANY)));
        $this->assertEquals('Testvalue', $dbResources->find('test.key', SystemLocale::create(SystemLocale::US)));

        // release the resources
        $factory->release();
    }

    /**
     * This tests the getKeys() method to return all keys for a DBResources instance.
     *
     * @return void
     */
    public function testPropertyResourcesGetKeys()
    {

        // initialize the factory
        $factory = new DBResourcesFactory();

        // load the resources
        $dbResources = $factory->getResources(
            new String('DBResources'),
            new String($this->configPath)
        );

        // iterate over the keys and check the values
        foreach ($dbResources->getKeys() as $key) {
            // check the correct values to load
            $this->assertEquals("test.key", $key);
        }

        // release the resources
        $factory->release();
    }

    /**
     * This tests the attach() method of the DBResourceBundle instance.
     *
     * @return void
     */
    public function testPropertyResourcesAttach()
    {

        // load the ResourceBundle
        $resourceBundle = DBResourceBundle::getBundle(
            new String($this->configPath),
            SystemLocale::create(SystemLocale::GERMANY)
        );

        // iterate over the keys and check the values
        $resourceBundle->attach('test.key.new', $value = 'neuer Testeintrag');

        // check the correct values to load
        $this->assertEquals($value, $resourceBundle->find('test.key.new'));
    }

    /**
     * This tests the replace() method of the DBResourceBundle instance.
     *
     * @return void
     */
    public function testPropertyResourcesReplace()
    {

        // load the ResourceBundle
        $resourceBundle = DBResourceBundle::getBundle(
            new String($this->configPath),
            SystemLocale::create(SystemLocale::GERMANY)
        );

        // iterate over the keys and check the values
        $resourceBundle->attach('test.key.newest', 'Testeintrag');

        // check the correct values to load
        $this->assertEquals('Testeintrag', $resourceBundle->find('test.key.newest'));

        // iterate over the keys and check the values
        $resourceBundle->replace('test.key.newest', $value = 'neuester Testeintrag');

        // check the correct values to load
        $this->assertEquals($value, $resourceBundle->find('test.key.newest'));
    }
}
