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

use AppserverIo\Resources\Interfaces\ResourceBundleInterface;

/**
 * Abstract class of all resource bundles.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/resources
 * @link      http://www.appserver.io
 */
abstract class AbstractResourceBundle implements ResourceBundleInterface
{

    /**
     * Holds the system locale to use
     *
     * @var \AppserverIo\Resources\SystemLocale
     */
    protected $systemLocale = null;

    /**
     * The constructor initializes the Resources with the
     * system locale to use.
     *
     * @param \AppserverIo\Resources\SystemLocale $systemLocale Holds the system locale instance to load the resources for
     *
     * @return void
     */
    protected function __construct(SystemLocale $systemLocale)
    {
        $this->setSystemLocale($systemLocale);
    }

    /**
     * This method returns the system locale instance.
     *
     * @return \AppserverIo\Resources\SystemLocale The system locale
     * @see \AppserverIo\Resources\Interfaces\ResourceBundleInterface::getSystemLocale()
     */
    public function getSystemLocale()
    {
        return $this->systemLocale;
    }

    /**
     * This sets the passed system locale.
     *
     * @param \AppserverIo\Resources\SystemLocale $newLocale The new system locale
     *
     * @return void
     */
    public function setSystemLocale(SystemLocale $newLocale)
    {
        $this->systemLocale = $newLocale;
    }
}
