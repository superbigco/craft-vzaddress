/**
 * VZ Address plugin for Craft CMS
 *
 * VzAddressField Field JS
 *
 * @author    Superbig
 * @copyright Copyright (c) 2018 Superbig
 * @link      https://superbig.co
 * @package   VzAddress
 * @since     2.0.0VzAddressVzAddressField
 */

;(function ( $, window, document, undefined ) {
    var pluginName = "vzAddress";
    var defaults = {};

    // Plugin constructor
    function Plugin( element, options ) {
        this.element = element;
        this.options = $.extend( {}, defaults, options) ;
        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }

    Plugin.prototype = {
        wideMode: 0,

        init: function() {
            var vzUrl = this;
            var $field = $(this.element);

            $(window).on('resize', function() {
                wideMode = $field.width() > 500;
                if (wideMode !== vzUrl.wideMode) {
                    vzUrl.wideMode = wideMode;
                    $field.toggleClass('wide', vzUrl.wideMode);
                    $field.toggleClass('narrow', !vzUrl.wideMode);
                }
            });
        }
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                    new Plugin( this, options ));
            }
        });
    };

})( jQuery, window, document );
