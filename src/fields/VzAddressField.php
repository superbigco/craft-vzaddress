<?php
/**
 * VZ Address plugin for Craft CMS 3.x
 *
 * A simple address field for Craft.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\vzaddress\fields;

use superbig\vzaddress\models\VzAddressModel;
use superbig\vzaddress\VzAddress;
use superbig\vzaddress\assetbundles\vzaddressfieldfield\VzAddressFieldFieldAsset;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Db;
use yii\db\Schema;
use craft\helpers\Json;

/**
 * @author    Superbig
 * @package   VzAddress
 * @since     2.0.0
 */
class VzAddressField extends Field
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $defaultCountry = null;

    /**
     * @var boolean
     */
    public $showName = true;

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('vz-address', 'VzAddress');
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            ['showName', 'boolean'],
            ['defaultCountry', 'string'],
            ['showName', 'default', 'value' => true],
        ]);

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if ($value) {
            $value = Json::decodeIfJson($value);
        }

        unset($value['__model__']);

        if (empty($value)) {
            return new VzAddressModel();
        }

        return new VzAddressModel($value);
    }

    /**
     * @inheritdoc
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        return parent::serializeValue($value, $element);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'vz-address/_components/fields/VzAddressField_settings',
            [
                'field'     => $this,
                'countries' => VzAddress::$plugin->vzAddressService->getCountries(),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $view = Craft::$app->getView();

        if (!$value) {
            $value = new VzAddressModel();
        }

        if (empty($value->country)) {
            $value->country = $this->defaultCountry;
        }

        // Register our asset bundle
        $view->registerAssetBundle(VzAddressFieldFieldAsset::class);

        // Get our id and namespace
        $id           = $view->formatInputId($this->handle);
        $namespacedId = $view->namespaceInputId($id);

        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = [
            'id'        => $id,
            'name'      => $this->handle,
            'namespace' => $namespacedId,
            'prefix'    => $view->namespaceInputId(''),
        ];
        $jsonVars = Json::encode($jsonVars);

        $view->registerJs("$('#{$namespacedId}-field').vzAddress();");

        // Render the input template
        return $view->renderTemplate(
            'vz-address/_components/fields/VzAddressField_input',
            [
                'name'         => $this->handle,
                'values'        => $value,
                'field'        => $this,
                'id'           => $id,
                'namespacedId' => $namespacedId,
                'countries'    => VzAddress::$plugin->vzAddressService->getCountries(),
            ]
        );
    }

    public function getTableAttributeHtml($value, ElementInterface $element): string
    {
        if ($value) {
            return $value->html();
        }
    }
}
