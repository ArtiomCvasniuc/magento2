/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_SpecialPromotions
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'uiComponent',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils'
], function ($, Component, totals, quote, priceUtils) {
    'use strict';

    return Component.extend({
        defaults: {
            itemId: 0
        },

        /**
         * Extends Component object by storage observable messages.
         */
        initialize: function () {
            this._super();
        },

        getMessage: function(){
            return 'This is debug notice for item ' + this.itemId;
        },

        /**
         * @return {Boolean}
         */
        ifShowDetails: function () {
            if (!this.isFullMode()) {
                return false;
            }

            return this.getPureValue() != 0 && this.getDetails().length > 0;
        },

        /**
         * @return {Array}
         */
        getDetails: function () {
            var discountSegment = totals.getSegment('discount');

            if (discountSegment
                && discountSegment['extension_attributes']
                && discountSegment['extension_attributes']['discount_details']
            ) {
                return discountSegment['extension_attributes']['discount_details'];
            }

            return [];
        },

        /**
         * @param {*} price
         * @return {*|String}
         */
        getFormattedPrice: function (price) {
            return priceUtils.formatPrice(price, quote.getPriceFormat());
        }
    });
});
