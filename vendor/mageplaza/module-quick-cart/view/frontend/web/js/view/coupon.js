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
    'ko',
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'mage/translate'
], function (ko, $, Component, customerData, $t) {
    'use strict';

    var guestUrl    = 'guest-carts/:cartId/coupons/:couponCode',
        customerUrl = 'carts/mine/coupons/:couponCode';

    return Component.extend({
        couponCode: ko.observable(),
        isApplied: ko.observable(),
        quoteId: ko.observable(),
        isLoggedIn: ko.observable(),
        apiUrl: ko.observable(),
        errorMsg: ko.observable(),
        successMsg: ko.observable(),
        hideMsgTimeout: null,

        initialize: function () {
            this._super();

            this.initQuickCartData(customerData.get('cart')());

            return this;
        },

        initObservable: function () {
            var self = this;

            this._super();

            customerData.get('cart').subscribe(function (cartData) {
                self.initQuickCartData(cartData);
            });

            return this;
        },

        initQuickCartData: function (cartData) {
            if (cartData.hasOwnProperty('mpquickcart')) {
                this.couponCode(cartData.mpquickcart.couponCode);
                this.isApplied(!!cartData.mpquickcart.couponCode);
                this.isLoggedIn(cartData.mpquickcart.isLoggedIn);
                this.quoteId(cartData.mpquickcart.quoteId);
                this.apiUrl(cartData.mpquickcart.apiUrl);
            }
        },

        handleMsg: function (type) {
            $('#mpquickcart-coupon-form .message-' + type).show();

            this.hideMsgTimeout = setTimeout(function () {
                $('#mpquickcart-coupon-form .message-' + type).hide('blind', {}, 500);
            }, 3000);
        },

        apply: function () {
            var self  = this,
                field = $('#mpquickcart-coupon-code');

            clearTimeout(this.hideMsgTimeout);

            if (!this.couponCode()) {
                field.focus().trigger('focusin');
                field.css('border-color', '#ed8380');

                return;
            }

            field.css('border-color', '');

            $.ajax({
                method: 'put',
                contentType: 'application/json',
                showLoader: true,
                url: this.buildUrl(this.quoteId(), this.couponCode()),
                data: JSON.stringify({
                    quoteId: this.quoteId(),
                    couponCode: this.couponCode()
                }),
                success: function () {
                    var cartData = customerData.get('cart')();

                    cartData.mpquickcart.couponCode = self.couponCode();
                    customerData.set('cart', cartData);

                    self.handleSuccessApply();
                    self.handleMsg('success');
                },
                error: function (response) {
                    self.handleErrorResponse(response);
                    self.handleMsg('error');
                }
            });
        },

        cancel: function () {
            var self = this;

            clearTimeout(this.hideMsgTimeout);

            $.ajax({
                method: 'delete',
                contentType: 'application/json',
                showLoader: true,
                url: this.buildUrl(this.quoteId(), ''),
                data: JSON.stringify({quoteId: this.quoteId()}),
                success: function () {
                    var cartData = customerData.get('cart')();

                    cartData.mpquickcart.couponCode = '';
                    customerData.set('cart', cartData);

                    self.handleSuccessCancel();
                    self.handleMsg('success');
                },
                error: function (response) {
                    self.handleErrorResponse(response);
                    self.handleMsg('error');
                }
            });
        },

        handleSuccessApply: function () {
            customerData.reload(['cart'], false);

            this.successMsg($t('Your coupon was successfully applied.'));
        },

        handleSuccessCancel: function () {
            customerData.reload(['cart'], false);

            this.successMsg($t('Your coupon was successfully removed.'));
        },

        handleErrorResponse: function (response) {
            this.errorMsg(response.responseJSON ? response.responseJSON.message : '');
        },

        buildUrl: function (cartId, couponCode) {
            var url = guestUrl;

            if (this.isLoggedIn()) {
                url = customerUrl;
            }

            return this.apiUrl() + url.replace(':cartId', cartId).replace(':couponCode', couponCode);
        }
    });
});
