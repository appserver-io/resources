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
use AppserverIo\Collections\ArrayList;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use AppserverIo\Resources\Exceptions\ResourcesException;
use AppserverIo\Resources\Exceptions\ResourcesKeyException;

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
class PropertyResources extends AbstractResources
{

    /**
     * Holds the data directory with the path to the resource files to export.
     *
     * @var \AppserverIo\Lang\String
     */
    protected $config = null;

    /**
     * Create and return a new resources instance with the specified logical name, after calling its init() method and
     * delegating the relevant properties. Concrete subclasses MUST implement this method.
     *
     * @param \AppserverIo\Lang\String $name   Holds the logical name of the resources to create
     * @param \AppserverIo\Lang\String $config Holds the optional string to the configuration
     *
     * @return void
     */
    public function __construct(String $name, String $config = null)
    {

        // initialize the superclass
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

        // if no Locale is passed, use the property resources default one
        if ($systemLocale == null) {
            $systemLocale = $this->getDefaultSystemLocale();
        }

        // check if the property resources bundle has already been loaded
        if (!$this->exists($systemLocale)) {
            $this->add(PropertyResourceBundle::getBundle($this->config, $systemLocale));
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
     * Initializes the resource bundles for all installed locales.
     *
     * @return void
     */
    protected function read()
    {

        // get all installed locales
        $locales = SystemLocale::getAvailableLocles();

        // load the default system locale
        $systemLocale = $this->getDefaultSystemLocale();

        // iterate over the installed locales and instanciate the resource bundles therefore
        for ($i = 0; $i < $locales->size(); $i ++) {
            $this->add(PropertyResourceBundle::getBundle($this->config, $systemLocale));
        }
    }

    /**
     * This method creates an Excelsheet and adds for each locale a column
     * with the values for it's key.
     *
     * After adding all resource strings it sends a header with the download
     * information of the generated Excelsheet.
     *
     * @return void
     */
    public function export()
    {

        // read and initialize the resource files
        $this->read();

        // get the default bundle
        $defaultBundle = $this->bundles->get(AbstractResources::getDefaultSystemLocale()->__toString());

        // creating a workbook
        $spreadsheet = new Spreadsheet();

        // load the active worksheet
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setCellValueByColumnAndRow(0, 0, "keys");

        // get the locale keys from the default resource bundle
        $line = 1;

        // initialize the array for the keys
        $keys = array();

        // write the keys to the worksheet
        foreach ($defaultBundle as $key => $value) {
            // add the keys to the key array
            $keys[] = $key;

            // write the keys
            $worksheet->setCellValueByColumnAndRow($line++, 0, $key);
        }

        // initialize the column counter
        $column = 1;

        // iterate over the resource bundles and add the values
        foreach ($this->bundles as $systemLocale => $resources) {
            // write the columen with the localee
            $worksheet->setCellValueByColumnAndRow(0, $column, $systemLocale);

            // initialize the counter for the number of lines
            $line = 1;

            // iterate over the the keys and add the values from the resource bundle
            foreach ($keys as $key) {
                // get the value for the key
                $value = $resources->find($key);

                // the actual data
                $worksheet->setCellValueByColumnAndRow($line, $column, $value);
                $line ++;
            }
            $column ++;
        }

        // finally save the file
        $writer = new Xlsx($spreadsheet);
        $writer->save($this->dataDir . DIRECTORY_SEPARATOR . $defaultBundle->getName()->__toString() . ".xlsx");
    }

    /**
     * This method imports the resource string from the file with the name specified as parameter.
     *
     * @param string $fileToImport Holds the name of the file to import the resource strings from
     *
     * @return void
     */
    public function import($fileToImport)
    {

        // read and initialize the resource files
        $this->read();

        // initialize the array with the locales
        $systemLocales = array();

        // open the file to import the resource strings from
        $handle = @fopen($fileToImport, "r");

        // throw an exception if the file with the resources to import can't be opened
        if (!$handle) {
            throw new ResourcesException('Can\'t open ' . $fileToImport . ' with resources to import');
        }

        // initialize the counter for the number of lines
        $lines = 0;

        // separate the content in lines
        while ($row = fgetcsv($handle, 4096)) {
            $key = "";
            for ($i = 0; $i <= $this->bundles->size(); $i ++) {
                if ($i == 0) {
                    $key = $row[$i];
                } elseif ($lines == 0 && $i > 0) {
                    $systemLocales[$i] = $row[$i];
                } else {
                    $resources = $this->bundles->get($systemLocales[$i]);
                    $resources->replace($key, $row[$i]);
                }
            }
            $lines ++;
        }
    }
}
