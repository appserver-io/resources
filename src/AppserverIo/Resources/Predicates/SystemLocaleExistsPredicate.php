<?php

/**
 * AppserverIo\Resources\Predicates\SystemLocaleExistsPredicate
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

namespace AppserverIo\Resources\Predicates;

use AppserverIo\Resources\SystemLocale;
use AppserverIo\Collections\PredicateInterface;

/**
 * This class is the predicate for checking the passed SystemLocale to be in the ArrayList with the
 * installed system locales.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/resources
 * @link      http://www.appserver.io
 */
class SystemLocaleExistsPredicate implements PredicateInterface
{

    /**
     * Holds the SystemLocale the check the system locales for.
     *
     * @var \AppserverIo\Resources\SystemLocale
     */
    protected $locale = null;

    /**
     * Constructor that initializes the internal member
     * with the value passed as parameter.
     *
     * @param \AppserverIo\Resources\SystemLocale $locale Holds the locale to check the system locales for
     *
     * @return void
     */
    public function __construct(SystemLocale $locale)
    {
        $this->locale = $locale;
    }

    /**
     * This method evaluates the objects passed as parameter against
     * the internal member and returns true if the locales are equal.
     *
     * @param \AppserverIo\Resources\SystemLocale $object Holds the object for the evaluation
     *
     * @return boolean Returns TRUE if the passed Locale equals to the internal one
     */
    public function evaluate($object)
    {

        //  return TRUE, if the passed SystemLocale are equal
        if ($this->locale->__toString() === $object->__toString()) {
            return true;
        }

        // if not, return FALSE
        return false;
    }
}
