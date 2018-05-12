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

/**
 * This is the test for the property resources.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/resources
 * @link      http://www.appserver.io
 */
class PropertyResourcesTest extends \PHPUnit_Framework_TestCase
{

    /**
     * This tests the find() method of the PropertyResources instance.
     *
     * @return void
     */
    public function testPropertyResourcesFind()
    {

        // initialize the factory
        $factory = new PropertyResourcesFactory();

        // load the resources
        $propertyResources = $factory->getResources(
            new String('TestResources'),
            new String(__DIR__ . '/data/testresources')
        );

        // check the correct values to load
        $this->assertEquals("Testwert", $propertyResources->find("test.key", SystemLocale::create(SystemLocale::GERMANY)));
        $this->assertEquals("Testvalue", $propertyResources->find("test.key", SystemLocale::create(SystemLocale::US)));

        // release the resources
        $factory->release();
    }

    /**
     * This tests the getKeys() method to return all keys for a PropertyResources instance.
     *
     * @return void
     */
    public function testPropertyResourcesGetKeys()
    {
        // initialize the factory
        $factory = new PropertyResourcesFactory();

        // load the resources
        $propertyResources = $factory->getResources(new String("TestResources"), new String("resources/data/testresources"));

        // iterate over the keys and check the values
        foreach ($propertyResources->getKeys() as $key) {
            // check the correct values to load
            $this->assertEquals("test.key", $key);
        }

        // release the resources
        $factory->release();
    }

    /**
     * This tests the attach() method of the PropertyResourceBundle instance.
     *
     * @return void
     */
    public function testPropertyResourcesAttach()
    {

        // load the ResourceBundle
        $resourceBundle = PropertyResourceBundle::getBundle(
            new String(__DIR__ . '/data/testresources'),
            SystemLocale::create(SystemLocale::GERMANY)
        );

        // iterate over the keys and check the values
        $resourceBundle->attach("test.key.new", $value = "neuer Testeintrag");

        // check the correct values to load
        $this->assertEquals($value, $resourceBundle->find("test.key.new"));
    }

    /**
     * This tests the replace() method of the PropertyResourceBundle instance.
     *
     * @return void
     */
    public function testPropertyResourcesReplace()
    {

        // load the ResourceBundle
        $resourceBundle = PropertyResourceBundle::getBundle(
            new String(__DIR__ . '/data/testresources'),
            SystemLocale::create(SystemLocale::GERMANY)
        );

        // iterate over the keys and check the values
        $resourceBundle->replace("test.key", $value = "neuester Testeintrag");

        // check the correct values to load
        $this->assertEquals($value, $resourceBundle->find("test.key"));
    }
}
