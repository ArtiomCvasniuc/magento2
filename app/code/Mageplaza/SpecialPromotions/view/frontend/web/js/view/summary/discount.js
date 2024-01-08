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
    'Magento_SalesRule/js/view/summary/discount',
    'Magento_Checkout/js/model/totals'
], function (Component, totals) {
    'use strict';

    var enableDiscountDetails = window.checkoutConfig.enableDiscountDetails;

    return Component.extend({
        defaults: {
            template: 'Mageplaza_SpecialPromotions/summary/discount'
        },

        /**
         * @return {Boolean}
         */
        ifShowDetails: function () {
            if (!this.isFullMode()) {
                return false;
            }

            return this.getPureValue() != 0 && enableDiscountDetails && this.getDetails().length > 0;
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
         * @param {*} amount
         * @return {*|String}
         */
        formatPrice: function (amount) {
            return this.getFormattedPrice(amount);
        },
    });
});
