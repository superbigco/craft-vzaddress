<?php
/**
 * VZ Address plugin for Craft CMS 3.x
 *
 * A simple address field for Craft.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\vzaddress\variables;

use superbig\vzaddress\VzAddress;

use Craft;

/**
 * @author    Superbig
 * @package   VzAddress
 * @since     2.0.0
 */
class VzAddressVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Return an array of country ids and names
     *
     * @return  array   Countries indexed by ID
     */
    public function countries()
    {
        return VzAddress::$plugin->vzAddressService->getCountries();
    }
}
