<?php
/**
 * VZ Address plugin for Craft CMS 3.x
 *
 * A simple address field for Craft.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\vzaddress\models;

use superbig\vzaddress\VzAddress;

use Craft;
use craft\base\Model;

/**
 * @author    Superbig
 * @package   VzAddress
 * @since     2.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $googleApiKey = '';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['googleApiKey', 'string'],
        ];
    }
}
