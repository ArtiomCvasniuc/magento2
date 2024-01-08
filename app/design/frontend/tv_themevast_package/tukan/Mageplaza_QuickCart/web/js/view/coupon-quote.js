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
    'Mageplaza_QuickCart/js/view/coupon',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_SalesRule/js/view/payment/discount',
    'Magento_Customer/js/customer-data',
    'mage/translate'
], function (Component, getPaymentInformationAction, discount, customerData, $t) {
    'use strict';

    return Component.extend({
        handleSuccessApply: function () {
            this.handleSuccessResponse(this.couponCode(), $t('Your coupon was successfully applied.'));
        },

        handleSuccessCancel: function () {
            this.handleSuccessResponse('', $t('Your coupon was successfully removed.'));
        },

        handleSuccessResponse: function (coupon, message) {
            var self = this;

            discount().couponCode(coupon);
            discount().isApplied(!!coupon);
            customerData.reload(['cart'], false);
            getPaymentInformationAction().done(function () {
                self.successMsg(message);
            });
        }
    });
});
