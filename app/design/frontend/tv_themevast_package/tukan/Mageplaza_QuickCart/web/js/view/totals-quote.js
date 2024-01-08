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
    'Magento_Checkout/js/view/summary/abstract-total',
    'Mageplaza_QuickCart/js/model/config',
    'Magento_Checkout/js/model/totals',
    'mage/translate'
], function (Component, config, totals, $t) {
    'use strict';

    return Component.extend({
        getTotals: function () {
            var data = totals.totals();

            if (config.getMpConfig('showFull')) {
                return data.total_segments;
            }

            return [{
                'title': $t('Subtotal'),
                'value': data.subtotal,
                'code': 'subtotal'
            }];
        },

        getValue: function (value) {
            return this.getFormattedPrice(value);
        }
    });
});
