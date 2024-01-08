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
 * @package     Mageplaza_QuickCart
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'uiComponent',
    'Mageplaza_QuickCart/js/model/config',
    'Magento_Customer/js/customer-data',
    'mage/translate'
], function (Component, config, customerData, $t) {
    'use strict';

    return Component.extend({
        getTotals: function () {
            var cartData = customerData.get('cart')();

            if (cartData.hasOwnProperty('mpquickcart') && config.getMpConfig('showFull')) {
                return cartData.mpquickcart.totals;
            }

            return [{
                'title': $t('Subtotal'),
                'value': cartData.subtotal,
                'code': 'subtotal'
            }];
        },

        getValue: function (value) {
            return value;
        }
    });
});
