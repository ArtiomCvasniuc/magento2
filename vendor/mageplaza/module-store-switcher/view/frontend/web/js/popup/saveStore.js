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
 * @package     Mageplaza_StoreSwitcher
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'jquery/jquery.cookie'
], function ($) {
    'use strict';

    $.widget('mageplaza.storeSwitcher', {
        /**
         * @inheritDoc
         */
        _create: function () {
            this._savePopup();
        },

        _savePopup: function () {
            var _this           = this,
                date            = new Date(),
                popup           = $('#mpstoreswitcher-save-store-popup'),
                mainContent     = $('#maincontent'),
                switcherOptions = $('.switcher-language .switcher-option a,' +
                    ' .switcher-language li.switcher-option,' +
                    ' .switcher-store .switcher-option a' +
                    ' .switcher-store li.switcher-option');

            if (mainContent.length) {
                popup.appendTo(mainContent);
            }

            date.setTime(date.getTime() + 60 * 60 * 1000 * 24);

            switcherOptions.each(function () {
                var el = $(this),
                    storeUrl;

                el.on('click', function (e) {
                    var dataPost, url, data, params;

                    e.stopPropagation();
                    e.preventDefault();
                    popup.show();

                    if (el.attr('data-post')) {
                        dataPost = JSON.parse(el.attr('data-post'));
                        url      = dataPost.action;
                        data     = dataPost.data;
                        params   = '';

                        if (url.indexOf('___from_store') === -1) {
                            $.each(data, function (key, value) {
                                params += key + '=' + value + '&';
                            });
                            storeUrl = url + '?' + params.substring(0, params.length - 1);
                        } else {
                            storeUrl = url;
                        }
                    } else {
                        storeUrl = el.attr('href');
                    }

                    $('.mp-save-store-yes').on('click', function () {
                        $.cookie('mpstoreswitcher_last_code', null);
                        $.cookie('is_save', null);
                        $.cookie('stop_redirect', null);
                        window.location = storeUrl;
                    });

                    $('.mp-save-store-no').on('click', function () {
                        var lastStoreCode = $.cookie('mpstoreswitcher_last_code');

                        if (!lastStoreCode) {
                            $.cookie('mpstoreswitcher_last_code', _this.options.lastStore, {expires: date});
                        }

                        $.cookie('not_save', 1);
                        $.cookie('stop_redirect', 0);
                        window.location = storeUrl;
                    });
                });
            });

            $('.mpstoreswitcher-btn-close-save-popup').on('click', function () {
                popup.hide();
            });
        }
    });

    return $.mageplaza.storeSwitcher;
});

