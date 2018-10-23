<?php
/**
 * VZ Address plugin for Craft CMS 3.x
 *
 * A simple address field for Craft.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\vzaddress;

use superbig\vzaddress\services\VzAddressService as VzAddressServiceService;
use superbig\vzaddress\variables\VzAddressVariable;
use superbig\vzaddress\models\Settings;
use superbig\vzaddress\fields\VzAddressField as VzAddressFieldField;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterComponentTypesEvent;

use yii\base\Event;

/**
 * Class VzAddress
 *
 * @author    Superbig
 * @package   VzAddress
 * @since     2.0.0
 *
 * @property  VzAddressServiceService $vzAddressService
 * @method  Settings getSettings()
 */
class VzAddress extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var VzAddress
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '2.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function(RegisterComponentTypesEvent $event) {
                $event->types[] = VzAddressFieldField::class;
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function(Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('vzAddress', VzAddressVariable::class);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function(PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Craft::info(
            Craft::t(
                'vz-address',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'vz-address/settings',
            [
                'settings' => $this->getSettings(),
            ]
        );
    }
}
