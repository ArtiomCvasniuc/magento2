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
    'Mageplaza_QuickCart/js/view/totals-quote',
    'Mageplaza_RewardPoints/js/model/points'
], function (Component, points) {
    'use strict';

    return Component.extend({
        getValue: function (value, code) {
            var codes = ['mp_reward_spent', 'mp_reward_earn'];

            if (codes.indexOf(code) !== -1) {
                return '<b>' + points.format(value) + '</b>';
            }

            return this.getFormattedPrice(value);
        }
    });
});
