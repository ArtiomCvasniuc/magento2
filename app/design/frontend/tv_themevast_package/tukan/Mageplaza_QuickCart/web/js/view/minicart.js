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
    'jquery',
    'Magento_Checkout/js/view/minicart',
    'Mageplaza_QuickCart/js/model/config',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function ($, Component, config, modal, $t) {
    'use strict';

    return Component.extend({
        closeModalTimeout: null,

        setModalElement: function (element) {
            var self = this, options;

            options = {
                'id': 'mpquickcart',
                'type': 'slide',
                'title': $t('Coșul de cumpărături'),
                'modalClass': 'mpquickcart',
                'responsive': true,
                'innerScroll': true,
                'trigger': '.showcart',
                'buttons': [],
                'parentModalClass': '_has-modal mpquickcart-has-modal',
                'opened': function () {
                    $('div.block.block-minicart').trigger('dropdowndialogopen');
                },
                'closed': function () {
                    $('div.block.block-minicart').trigger('dropdowndialogclose');
                }
            };

            this.modalWindow = element;

            modal(options, $(this.modalWindow));

            if (config.getMpConfig('isHover')) {
                $('.showcart').on('mouseover', function () {
                    self.showModal();
                });

                $('.mpquickcart').on('mouseover', function () {
                    clearTimeout(self.closeModalTimeout);
                }).on('mouseleave', function () {
                    self.closeModalTimeout = setTimeout(function () {
                        self.closeModal();
                    }, 500);
                });
            }
        },

        showModal: function () {
            $(this.modalWindow).modal('openModal');
        },

        closeModal: function () {
            $(this.modalWindow).modal('closeModal');
        },

        minusQty: function (item, event) {
            var target = $(event.target).next();

            if (target.val() <= 1) {
                return;
            }

            target.val(Number(target.val()) - 1);

            target.trigger('change');
        },

        plusQty: function (item, event) {
            var target = $(event.target).prev();

            target.val(Number(target.val()) + 1);

            target.trigger('change');
        }
    });
});
