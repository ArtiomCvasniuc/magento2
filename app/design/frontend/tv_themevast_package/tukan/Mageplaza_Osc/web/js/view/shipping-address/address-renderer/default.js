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
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'Magento_Checkout/js/view/shipping-address/address-renderer/default',
    'Magento_Checkout/js/model/shipping-rate-service',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/model/quote'
], function (Component, shippingRateService, rateRegistry, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Mageplaza_Osc/container/address/shipping/address-renderer/default'
        },

        /** Set selected customer shipping address  */
        selectAddress: function () {

            if (!this.isSelected()) {
                this._super();

                if (quote.shippingAddress().getType == 'customer-address') {
                    rateRegistry.set(quote.shippingAddress().getKey(), null);
                } else {
                    rateRegistry.set(quote.shippingAddress().getCacheKey(), null);
                }

                shippingRateService.isAddressChange = true;
                shippingRateService.estimateShippingMethod();
            }

            var interval = setInterval(myFunction, 1000);
            function myFunction() {
                if(document.getElementById("checkout-step-shipping_method")) {
                    var customer_id = document.getElementById("customer-id").value;
                    var seller_ids = document.getElementById("seller-ids").value;
                    var selected_address = document.getElementsByClassName("shipping-address-item selected-item")[0].innerText;
                    selected_address = replaceDiacritics(selected_address);
                    jQuery("#checkout-step-shipping_method").load("/checkoutShippingMethods.php", {address:selected_address, seller_ids:seller_ids, customer_id:customer_id});
                }
                if(document.getElementById("checkout-step-shipping_method").innerText != '') {
                    clearInterval(interval);
                }
            }

            function replaceDiacritics(s) {
                var s;

                var diacritics =[
                    /[\300-\306]/g, /[\340-\346]/g,  // A, a
                    /[\310-\313]/g, /[\350-\353]/g,  // E, e
                    /[\314-\317]/g, /[\354-\357]/g,  // I, i
                    /[\322-\330]/g, /[\362-\370]/g,  // O, o
                    /[\331-\334]/g, /[\371-\374]/g,  // U, u
                    /[\321]/g, /[\361]/g, // N, n
                    /[\307]/g, /[\347]/g, // C, c
                ];

                var chars = ['A','a','E','e','I','i','O','o','U','u','N','n','C','c'];

                for (var i = 0; i < diacritics.length; i++) {
                    s = s.replace(diacritics[i],chars[i]);
                }

                return s;
            }
        }
    });
});