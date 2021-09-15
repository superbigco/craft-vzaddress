<?php
/**
 * VZ Address plugin for Craft CMS 3.x
 *
 * A simple address field for Craft.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\vzaddress\assetbundles\vzaddress;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Superbig
 * @package   VzAddress
 * @since     2.0.0
 */
class VzAddressAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@superbig/vzaddress/assetbundles/vzaddress/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/VzAddress.js',
        ];

        $this->css = [
            'css/VzAddress.css',
        ];

        parent::init();
    }
}
