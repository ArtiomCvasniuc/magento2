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
    'ko',
    'jquery',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'mage/translate'
], function (ko, $, Component, quote, priceUtils, $t) {
    'use strict';

    var discountItems = ko.observable({});

    /**
     * Collec Discount Items
     * @param totals
     */
    function collectDiscountItems(totals){
        var items = {},
            discountSegment = getSegment(totals, 'discount');

        if (discountSegment
            && discountSegment['extension_attributes']
            && discountSegment['extension_attributes']['discount_details']
        ) {
            var discountDetail = discountSegment['extension_attributes']['discount_details'];
            for (var i in discountDetail) {
                if (discountDetail.hasOwnProperty(i) && discountDetail[i].hasOwnProperty('items')) {
                    for (var j in discountDetail[i]['items']) {
                        if (discountDetail[i]['items'].hasOwnProperty(j)) {
                            var item = discountDetail[i]['items'][j];
                            if (!items[item.item_id]) {
                                items[item.item_id] = {amount: 0, qty: 0, rules: []};
                            }

                            items[item.item_id].amount += item.amount;
                            items[item.item_id].qty += item.qty;
                            items[item.item_id].rules.push({
                                rule_id: discountDetail[i].rule_id,
                                title: discountDetail[i].title,
                                amount: item.amount,
                                qty: item.qty
                            });
                        }
                    }
                }
            }
        }

        discountItems(items);
    }

    /**
     * @param totals
     * @param code
     * @return {null|*}
     */
    function getSegment(totals, code) {
        if (!totals) {
            return null;
        }

        var i, total;
        for (i in totals['total_segments']) { //eslint-disable-line guard-for-in
            total = totals['total_segments'][i];
            if (total.code == code) { //eslint-disable-line eqeqeq
                return total;
            }
        }

        return null;
    }

    collectDiscountItems(quote.totals());
    quote.totals.subscribe(function (totals) {
        collectDiscountItems(totals);
    });

    return Component.extend({
        defaults: {
            itemId: 0,
        },

        /**
         * Extends Component object by storage observable messages.
         */
        initialize: function () {
            this._super();

            this.message = ko.computed(function () {
                var items = discountItems(),
                    message = '';

                if (items.hasOwnProperty(this.itemId)) {
                    var item = items[this.itemId];
                    if(item.hasOwnProperty('amount') && item.amount > 0) {
                        for (var i in item) {
                            if (item.hasOwnProperty(i)) {
                                switch (i) {
                                    case 'amount':
                                        message += '<p><span style="display:inline-block; width: 40%; min-width: 110px; padding-right: 10px;">' + $t('Discount amount:') + '</span>';
                                        message += this.getFormattedPrice(item[i]) + '</p>';
                                        break;
                                    case 'qty':
                                        message += '<p><span style="display:inline-block; width: 40%; min-width: 110px; padding-right: 10px;">' + $t('Qty discount:') + '</span>';
                                        message += item[i] + '</p>';
                                        break;
                                    case 'rules':
                                        var detail = '';
                                        for (var j in item[i]) {
                                            if (item[i].hasOwnProperty(j)) {
                                                var rule = item[i][j];
                                                if (rule.amount > 0) {
                                                    detail += '<p><span style="display:inline-block; width: 60%; padding-right: 10px; padding-left: 20px">' + rule.title + ' (ID: ' + rule.rule_id + ', Qty: ' + rule.qty + '):</span>';
                                                    detail += this.getFormattedPrice(rule.amount) + '</p>';
                                                }
                                            }
                                        }

                                        if (detail !== '') {
                                            message += '<p><span style="display:inline-block;">' + $t('Rule(s) applied:') + '</span></p>';
                                            message += detail;
                                        }
                                        break;
                                }
                            }
                        }
                    }
                }

                return message;
            }, this);
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
