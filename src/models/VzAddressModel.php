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

use craft\helpers\Template;
use craft\web\View;
use superbig\vzaddress\VzAddress;

use Craft;
use craft\base\Model;

/**
 * @author    Superbig
 * @package   VzAddress
 * @since     2.0.0
 */
class VzAddressModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $name;
    public $street;
    public $street2;
    public $city;
    public $region;
    public $postalCode;
    public $country;
    public $countryName;

    // Public Methods
    // =========================================================================

    public function __toString()
    {
        return $this->text(true);
    }

    /*public function toArray()
    {
        $address            = array_filter($this->getAttributes());
        $address['country'] = $this->countryName;

        return $address;
    }*/

    public function text($formatted = false)
    {
        $view = Craft::$app->getView();

        if ($formatted) {
            $output = $this->renderTemplate('text', [
                'address' => $this,
            ]);
        }
        else {
            $output = implode(', ', $this->toArray());
        }

        return $output;
    }

    public function html($format = "plain")
    {
        if (in_array($format, ['schema', 'microformat', 'rdfa'])) {
            $output = $this->renderTemplate($format, [
                'address' => $this,
            ]);
        }
        else {
            $output = str_replace("\n", '<br>', $this->text(true));
        }

        return Template::raw($output);
    }

    public function mapUrl($source = 'google', $params = [])
    {
        $params = count($params) ? '&' . http_build_query($params) : '';
        $output = '';
        // Create the url-encoded address
        $query = urlencode(implode(', ', $this->toArray()));
        switch ($source) {
            case 'yahoo':
                $output = "https://maps.yahoo.com/#q={$query}{$params}";
                break;
            case 'bing':
                $output = "https://www.bing.com/maps/?v=2&where1={$query}{$params}";
                break;
            case 'mapquest':
                $output = "https://mapq.st/map?q={$query}{$params}";
                break;
            case 'google':
            default:
                $output = "https://maps.google.com/maps?q={$query}{$params}";
                break;
        }

        return $output;
    }

    public function staticMapUrl($params = [])
    {
        $settings = VzAddress::$plugin->getSettings();
        $source   = isset($params['source']) ? strtolower($params['source']) : 'google';
        $width    = isset($params['width']) ? strtolower($params['width']) : '400';
        $height   = isset($params['height']) ? strtolower($params['height']) : '200';
        $scale    = isset($params['scale']) ? strtolower($params['scale']) : '1';
        $zoom     = isset($params['zoom']) ? strtolower($params['zoom']) : '14';
        $format   = isset($params['format']) ? strtolower($params['format']) : 'png';
        $type     = isset($params['type']) ? strtolower($params['type']) : 'roadmap';
        $size     = isset($params['markerSize']) ? strtolower($params['markerSize']) : false;
        $label    = isset($params['markerLabel']) ? strtoupper($params['markerLabel']) : false;
        $color    = isset($params['markerColor']) ? strtolower($params['markerColor']) : false;
        $style    = isset($params['styles']) ? $this->_styleString($params['styles']) : false;
        $key      = false;

        if (!$style && isset($params['style'])) {
            $style = $this->_styleString($params['style']);
        }

        if (isset($params['key'])) {
            $key = $params['key'];
        }
        elseif (!empty($settings->googleApiKey)) {
            $key = $settings->googleApiKey;
        }

        // Normalize the color parameter
        $color = $this->_normalizeColor($color);

        // Create the url-encoded address
        $address = urlencode(implode(', ', $this->toArray()));
        $output  = '';
        $marker  = '';
        switch ($source) {
            case 'yahoo':
                // TODO
            case 'bing':
                // TODO
            case 'mapquest':
                // TODO
            case 'google':
            default:
                $marker .= $size ? 'size:' . $size . '|' : '';
                $marker .= $color ? 'color:' . $color . '|' : '';
                $marker .= $label ? 'label:' . $label . '|' : '';
                $output .= "https://maps.googleapis.com/maps/api/staticmap?zoom={$zoom}&size={$width}x{$height}&scale={$scale}&format={$format}&maptype={$type}&markers={$marker}{$address}&sensor=false{$style}";
                $output = $key ? $output . '&key=' . $key : $output;
                break;
        }

        return $output;
    }

    public function staticMap($params = [])
    {
        $width   = isset($params['width']) ? strtolower($params['width']) : '400';
        $height  = isset($params['height']) ? strtolower($params['height']) : '200';
        $map_url = $this->staticMapUrl($params);
        $address = htmlspecialchars($this->text());
        $output  = '<img src="' . $map_url . '" alt="' . $address . '" width="' . $width . '" height="' . $height . '">';

        return Template::raw($output);
    }

    /**
     * Generate a dynamic map using the Google Maps Javascript API
     *
     * @var array $params An array of MapOptions for the Google Map object
     * @var array $icon   An array of configuration options for the Marker icon
     *
     * @see https://developers.google.com/maps/documentation/javascript/3.exp/reference#MapOptions
     *
     * @return \Twig_Markup The markup string wrapped in a Twig_Markup object
     */
    public function dynamicMap($params = [], $icon = [])
    {
        if (isset($params['key'])) {
            $key = $params['key'];
        }
        else {
            // fetch our plugin settings so we can use the api key
            $settings = VzAddress::$plugin->getSettings();
            $key      = $settings->googleApiKey;
        }
        if (empty($key)) {
            return 'A Google API key is required.';
        }

        // include the javascript api from google's cdn using our api key
        Craft::$app->getView()->includeJsFile("https://maps.googleapis.com/maps/api/js?key={$key}");
        // geocode our address into coordinates
        $address = $this->toArray();
        // remove the name from the address as it throws the geocoder off
        unset($address['name']);
        $coords = $this->_geocodeAddress(implode($address, ' '), $key);
        $width  = isset($params['width']) ? strtolower($params['width']) : '400';
        unset($params['width']);
        $height = isset($params['height']) ? strtolower($params['height']) : '200';
        unset($params['height']);
        // these mirror MapOptions object - https://developers.google.com/maps/documentation/javascript/3.exp/reference#MapOptions
        $defaults = [
            'zoom'   => 14,
            'center' => $coords,
        ];
        // merge the given parameters with our defaults to create the options array
        $options = array_merge($defaults, $params);
        // assemble the config array
        $config = [
            'id'     => uniqid('map-', true),
            'width'  => $width,
            'height' => $height,
        ];

        // get the rendered template as a string
        $output = $this->renderTemplate('googlemap_dynamic', [
            'options' => $options,
            'icon'    => $icon,
            'config'  => $config,
        ]);

        return Template::raw($output);
    }

    /**
     * Virtual Attributes
     */
    public function getCountryName()
    {
        return Craft::$app->getI18n()->getLocaleById($this->country)->getDisplayName();
    }

    /**
     * Method to geocode the given address string into a lat/lng coordinate pair
     *
     * @var string $address The address string
     * @return array The lat/lng pair in an associative array
     */
    private function _geocodeAddress($address, $key)
    {
        $address = urlencode($address);
        $url     = "https://maps.google.com/maps/api/geocode/json?address={$address}&key={$key}";
        // get the json response
        $response = \json_decode(file_get_contents($url), true);

        // Response status will be 'OK' if able to geocode given address
        if ($response['status'] == 'OK') {
            $lat = $response['results'][0]['geometry']['location']['lat'];
            $lng = $response['results'][0]['geometry']['location']['lng'];
            // verify if data is complete
            if ($lat && $lng) {
                return [
                    'lat' => $lat,
                    'lng' => $lng,
                ];
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    /**
     * Method to normalise the given color string e.g. transform #ffffff to 0xffffff
     *
     * @var string $color The color string to be transformed
     * @return string The transformed color stirng
     */
    private function _normalizeColor($color)
    {
        return str_replace('#', '0x', $color);
    }

    /**
     * Method to parse the given style array and return a formatted string
     *
     * @var array $style A multidimensional array structured according to Google's Styled Maps configuration e.g.
     *
     * [
     *     {
     *         'featureType': String
     *         'elementType': String
     *         'stylers': [
     *             {
     *                 String: String
     *             }
     *         ]
     *     }
     *     ...
     * ]
     *
     * @see https://developers.google.com/maps/documentation/javascript/styling Documentation of styling Google Maps
     *
     * @return string A style string formatted for use with the Google Static Maps API
     */
    private function _styleString($style)
    {
        $output = "";
        foreach ($style as $elem) {
            $declaration = [];
            if (array_key_exists('featureType', $elem)) {
                $declaration[] = "feature:{$elem['featureType']}";
            }

            if (array_key_exists('elementType', $elem)) {
                $declaration[] = "element:{$elem['elementType']}";
            }

            foreach ($elem['stylers'] as $styler) {
                foreach ($styler as $key => $value) {
                    if ($key == 'color') {
                        $value = $this->_normalizeColor($value);
                    }
                    $declaration[] .= "{$key}:{$value}";
                }
            }
            $output .= '&style=' . implode($declaration, '|');
        }

        return $output;
    }

    /**
     * @param       $template
     * @param array $data
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function renderTemplate($template, $data = []): string
    {
        $view    = Craft::$app->getView();
        $oldMode = $view->getTemplateMode();

        $view->setTemplateMode(View::TEMPLATE_MODE_CP);

        $html = $view->renderTemplate('vz-address/_frontend/' . $template, $data);

        $view->setTemplateMode($oldMode);

        return $html;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'name',
                    'street',
                    'street2',
                    'city',
                    'region',
                    'postalCode',
                    'country',
                ], 'string',
            ],
            //['someAttribute', 'default', 'value' => 'Some Default'],
        ];
    }
}
