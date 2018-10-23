<?php
/**
 * VZ Address plugin for Craft CMS 3.x
 *
 * A simple address field for Craft.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\vzaddress\assetbundles\vzaddressfieldfield;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Superbig
 * @package   VzAddress
 * @since     2.0.0
 */
class VzAddressFieldFieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@superbig/vzaddress/assetbundles/vzaddressfieldfield/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/VzAddressField.js',
        ];

        $this->css = [
            'css/VzAddressField.css',
        ];

        parent::init();
    }
}
