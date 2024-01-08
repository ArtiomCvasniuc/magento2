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
    'Mageplaza_SpecialPromotions/js/view/summary/discount'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Mageplaza_SpecialPromotions/cart/totals/discount'
        },

        /**
         * @override
         *
         * @returns {Boolean}
         */
        isDisplayed: function () {
            return this.getPureValue() != 0; //eslint-disable-line eqeqeq
        }
    });
});
