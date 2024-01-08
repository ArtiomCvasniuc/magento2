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
    'underscore',
    'ko',
    'mageUtils',
    'Magento_Checkout/js/view/payment/list',
    'Magento_Checkout/js/model/payment/method-list',
    'Magento_Checkout/js/model/payment/renderer-list',
    'uiLayout',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'mage/translate',
    'uiRegistry'
], function (_, ko, utils, Component, paymentMethods, rendererList, layout, checkoutDataResolver, $t, registry) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Mageplaza_Osc/payment-methods/list',
        },

        /**
         * Create renderer.
         *
         * @param {Object} paymentMethodData
         */
        createRenderer: function (paymentMethodData) {
            if (paymentMethodData.method === 'paypal_express') {
                var isRendererForMethod = false,
                    currentGroup;
                var isContextCheckout = window.checkoutConfig.payment.paypalExpress.isContextCheckout;
                if(isContextCheckout) {
                    var paypalExpress = 'Mageplaza_Osc/js/view/payment/method-renderer/in-context/checkout-express';
                }

                registry.get(this.configDefaultGroup.name, function (defaultGroup) {
                    _.each(rendererList(), function (renderer) {

                        if (renderer.hasOwnProperty('typeComparatorCallback') &&
                            typeof renderer.typeComparatorCallback == 'function'
                        ) {
                            isRendererForMethod = renderer.typeComparatorCallback(renderer.type, paymentMethodData.method);
                        } else {
                            isRendererForMethod = renderer.type === paymentMethodData.method;
                        }

                        if (isRendererForMethod) {
                            currentGroup = renderer.group ? renderer.group : defaultGroup;

                            this.collectPaymentGroups(currentGroup);

                            layout([
                                this.createComponent(
                                    {
                                        config: renderer.config,
                                        component: paypalExpress,
                                        name: renderer.type,
                                        method: paymentMethodData.method,
                                        item: paymentMethodData,
                                        displayArea: currentGroup.displayArea
                                    }
                                )]);
                        }
                    }.bind(this));
                }.bind(this));
            }
        },
    });
});

var interval = setTimeout(check, 5000);
function check() {
    if(document.getElementsByClassName("payment-method _active")[0]) {
        document.getElementsByClassName("payment-method _active")[0].classList.remove("_active");
    }
}

var interval = setTimeout(checkPaymentMethod, 5000);
function checkPaymentMethod() {
    if(document.getElementById("checkout-payment-method-load")) {
        var parent = document.getElementById("checkout-payment-method-load");
        var nodesSameClass = parent.getElementsByClassName("payment-method");
        for(var i=0; i<nodesSameClass.length; i++) {
            nodesSameClass[i].setAttribute("id", "payment-method-"+i);
            document.getElementById("payment-method-"+i).setAttribute("onclick", "setActivePaymentMethod("+i+")");
        }
    }
}

function setActivePaymentMethod(k) {
    if(k == 0) {
        document.getElementById("payment-method-0").removeAttribute("data-bind");
        document.getElementById("payment-method-0").removeAttribute("class");
        document.getElementById("payment-method-0").setAttribute("class", "payment-method _active");
        document.getElementById("payment-method-1").classList.remove("_active");
    }
    if(k == 1) {
        document.getElementById("payment-method-1").removeAttribute("data-bind");
        document.getElementById("payment-method-1").removeAttribute("class");
        document.getElementById("payment-method-1").setAttribute("class", "payment-method _active");
        document.getElementById("payment-method-0").classList.remove("_active");
    }
}