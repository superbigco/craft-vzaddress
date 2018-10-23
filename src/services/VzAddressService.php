<?php
/**
 * VZ Address plugin for Craft CMS 3.x
 *
 * A simple address field for Craft.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\vzaddress\services;

use superbig\vzaddress\VzAddress;

use Craft;
use craft\base\Component;

/**
 * @author    Superbig
 * @package   VzAddress
 * @since     2.0.0
 */
class VzAddressService extends Component
{
    // Public Methods
    // =========================================================================

    private $_countries;

    public function getCountries()
    {
        if (!$this->_countries) {
            $path = \dirname(__DIR__) . '/data/countries.json';
            $data = \json_decode(\file_get_contents($path), true);

            foreach ($data as $country) {
                $this->_countries[ $country['alpha-2'] ] = $country['name'];
            }
        }

        return $this->_countries;
    }
}
