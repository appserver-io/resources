<?php

/**
 * AppserverIo\Resources\SystemLocale
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

use AppserverIo\Lang\Object;
use AppserverIo\Lang\String;
use AppserverIo\Lang\NullPointerException;
use AppserverIo\Collections\ArrayList;
use AppserverIo\Collections\CollectionUtils;
use AppserverIo\Resources\Predicates\SystemLocaleExistsPredicate;

/**
 * A Locale object represents a specific geographical, political, or cultural
 * region. An operation that requires a Locale to perform its task is called
 * locale-sensitive and uses the Locale  to tailor information for the user.
 * For example, displaying a number is a locale-sensitive operation--the number
 * should be formatted according to the customs/conventions of the user's native
 * country, region, or culture.
 *
 * The language argument is a valid ISO Language Code. These codes are the
 * lower-case, two-letter codes as defined by ISO-639. You can find a full list
 * of these codes at a number of sites, such as:
 * http://www.ics.uci.edu/pub/ietf/http/related/iso639.txt
 *
 * The country argument is a valid ISO Country Code. These codes are the
 * upper-case, two-letter codes as defined by ISO-3166. You can find a full list
 * of these codes at a number of sites, such as:
 * http://www.chemie.fu-berlin.de/diverse/doc/ISO_3166.html
 *
 * The variant argument is a vendor or browser-specific code. For example, use
 * WIN for Windows, MAC for Macintosh, and POSIX for POSIX. Where there are two
 * variants, separate them with an underscore, and put the most important one
 * first. For example, a Traditional Spanish collation might construct a locale
 * with parameters for language, country and variant as: "es", "ES",
 * "Traditional_WIN".
 *
 * Because a Locale object is just an identifier for a region, no validity check
 * is performed when you construct a Locale. If you want to see whether
 * particular resources are available for the Locale you construct, you must
 * query those resources. For example, ask the NumberFormat for the locales it
 * supports using its getAvailableLocales method.
 *
 * Note: When you ask for a resource for a particular locale, you get back the
 * best available match, not necessarily precisely what you asked for. For more
 * information, look at ResourceBundle.
 *
 * The Locale class provides a number of convenient constants that you can use
 * to create Locale objects for commonly used locales. For example, the
 * following creates a Locale object for the United States:
 *
 * SystemLocale::create(SystemLocale::US)
 *
 * Once you've created a Locale you can query it for information about itself.
 * Use getCountry to get the ISO Country Code and getLanguage to get the ISO
 * Language Code. You can use getDisplayCountry to get the name of the country
 * suitable for displaying to the user. Similarly, you can use
 * getDisplayLanguage to get the name of the language suitable for displaying
 * to the user. Interestingly, the getDisplayXXX methods are themselves
 * locale-sensitive and have two versions: one that uses the default locale and
 * one that uses the locale specified as an argument.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/resources
 * @link      http://www.appserver.io
 */
class SystemLocale extends Object
{

    /**
     * Holds the constant for the United States locale string.
     *
     * @var string
     */
    const US = 'en_US';

    /**
     * Holds the constant for the United Kingdom locale string.
     *
     * @var string
     */
    const UK = 'en_UK';

    /**
     * Holds the constant for the Germany locale string.
     *
     * @var string
     */
    const GERMANY = 'de_DE';

    /**
     * Holds the language.
     *
     * @var \AppserverIo\Lang\String
     */
    protected $language = null;

    /**
     * Holds the country.
     *
     * @var \AppserverIo\Lang\String
     */
    protected $country = null;

    /**
     * Holds the variant.
     *
     * @var \AppserverIo\Lang\String
     */
    protected $variant = null;

    /**
     * Construct a locale from language, country, variant. NOTE: ISO 639 is not
     * a stable standard; some of the language codes it defines (specifically
     * iw, ji, and in) have changed. This constructor accepts both the old codes
     * (iw, ji, and in) and the new codes (he, yi, and id), but all other API on
     * Locale will return only the OLD codes.
     *
     * @param \AppserverIo\Lang\String $language The lowercase two-letter ISO-639 code
     * @param \AppserverIo\Lang\String $country  The uppercase two-letter ISO-3166 code
     * @param \AppserverIo\Lang\String $variant  The vendor and browser specific code. See class description
     */
    public function __construct(String $language, String $country = null, String $variant = null)
    {

        // initialize the language
        $this->language = $language;

        // initialize the country and the variant with the passed values
        $this->country = new String($country);
        $this->variant = new String($variant);

        // initialize the country and check if at least a language
        // or a country is passed
        if ($this->language->length() == 0 &&
            $this->country->length() == 0) {
            throw new NullPointerException(
                'Either language or country must have a value'
            );
        }
    }

    /**
     * This method tries to create a new Locale instance from
     * the passed string value.
     *
     * The passed string must have the following format: language_country
     *
     * @param string $localeString Holds the locale string to create the locale from
     *
     * @return \AppserverIo\Resources\SystemLocale Holds the initialized locale object
     */
    public static function create($localeString)
    {

        // split the passed string
        $elements = new ArrayList(explode("_", $localeString));

        // if only the language was found
        if ($elements->size() == 1) {
            // initialize a new Locale
            return new SystemLocale(new String($elements->get(0)));
        }

        // if the language and the country was found
        if ($elements->size() == 2) {
            // initialize a new Locale
            return new SystemLocale(
                new String($elements->get(0)),
                new String($elements->get(1))
            );
        }

        // if the language, the country and the variant was found
        if ($elements->size() == 3) {
            // initialize a new Locale
            return new SystemLocale(
                new String($elements->get(0)),
                new String($elements->get(1)),
                new String($elements->get(2))
            );
        }
    }

    /**
     * This method returns an ArrayList with the installed system locales.
     *
     * @return \AppserverIo\Collections\ArrayList Holds an ArrayList with Locale instances installed  on the actual system
     */
    public static function getAvailableLocales()
    {

        // initialize the ArrayList for the system locales
        $locales = new ArrayList();

        // initialize the result array
        $result = array();

        // get the list with locales
        exec('locale -a', $result);

        // initialize the Locale instances and add them the ArrayList
        foreach ($result as $locale) {
            // initialize the array with the found locales
            $locales->add(SystemLocale::create(trim($locale)));
        }

        // return the locales
        return $locales;
    }

    /**
     * Getter for the programmatic name of the entire locale, with the language, country and variant separated by underbars.
     *
     * @return \AppserverIo\Lang\String Holds the entire locale as String object
     * @see \AppserverIo\Resources\SystemLocale::__toString()
     */
    public function toString()
    {
        return new String($this->__toString());
    }

    /**
     * Getter for the programmatic name of the entire locale, with the language, country and variant separated by underbars.
     *
     * @return string Holds the entire locale as string
     * @see \AppserverIo\Resources\SystemLocale::toString()
     */
    public function __toString()
    {
        $string = '';
        if (!$this->language->length() == 0) {
            $string = $this->language->stringValue();
        }
        if (!$this->country->length() == 0) {
            $string .= "_" . $this->country->stringValue();
        }
        if (!$this->variant->length() == 0) {
            $string .= "_" . $this->variant->stringValue();
        }
        return $string;
    }

    /**
     * Sets the default locale, but does not set the system locale.
     *
     * @param \AppserverIo\Resources\SystemLocale $newLocale Holds the new default system locale to use
     *
     * @return void
     * @throws \Exception Is thrown if the passed locale is not installed in the system
     */
    public static function setDefault(SystemLocale $newLocale)
    {

        // check if the passed locale is installed
        if (!CollectionUtils::exists(SystemLocale::getAvailableLocales(), new SystemLocaleExistsPredicate($newLocale))) {
            throw new \Exception('System locale ' . $newLocale . ' is not installed');
        }

        // set the default system locale
        if (!setlocale(LC_ALL, $newLocale)) {
            throw new \Exception('Default locale can\'t be set to ' . $newLocale);
        }
    }

    /**
     * Returns the default system locale.
     *
     * @return \AppserverIo\Resources\SystemLocale Holds the default system locale
     * @throws \Exception If no system locale is set
     */
    public static function getDefault()
    {

        // initialize the variables
        $language = new String();
        $country = new String();
        $variant = new String();

        // get the default system locale
        $systemLocale = setlocale(LC_ALL, "0");

        // explode the parts
        $list = new ArrayList(explode("_", $systemLocale));

        // initialize the language, the country and the variant
        if ($list->size() > 0) {
            if ($list->exists(0)) {
                $language = new String($list->get(0));
            }
            if ($list->exists(1)) {
                $country = new String($list->get(1));
            }
            if ($list->exists(2)) {
                $variant = new String($list->get(2));
            }

        } else {
            throw new \Exception("No system locale set");
        }

        // initialize and return the SystemLocale instance
        return new SystemLocale($language, $country, $variant);
    }

    /**
     * Returns the language.
     *
     * @return String Holds the language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Returns the country.
     *
     * @return String Holds the country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Returns the variant.
     *
     * @return String Holds the variant
     */
    public function getVariant()
    {
        return $this->variant;
    }

    /**
     * Returns true if the passed value is equal.
     *
     * @param \AppserverIo\Lang\Object $val The value to check
     *
     * @return boolean TRUE if the passed object is equal, else FALSE
     */
    public function equals(Object $val)
    {
        return $this->__toString() == $val->__toString();
    }
}
