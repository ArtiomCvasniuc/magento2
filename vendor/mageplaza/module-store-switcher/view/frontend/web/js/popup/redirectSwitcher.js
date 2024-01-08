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
    'jquery/ui',
    'jquery/jquery.cookie'
], function ($) {
    'use strict';

    $.widget('mageplaza.storeSwitcher', {

        /**
         * @inheritDoc
         */
        _create: function () {
            this._redirectPopup();
        },

        _redirectPopup: function () {
            var date                 = new Date(),
                isSwitcher           = $.cookie('is_switcher'),
                btnYes               = $('.mp-redirect-yes'),
                btnNo                = $('.mp-redirect-no'),
                mpStoreSwitcherPopup = $('#mpstoreswitcher-redirect-popup');

            date.setTime(date.getTime() + 60 * 60 * 24 * 1000);

            if (isSwitcher) {
                mpStoreSwitcherPopup.hide();
            } else {
                mpStoreSwitcherPopup.show();
            }

            btnYes.on('click', function (e) {
                var switchUrl = $(this).find('a').attr('href');

                e.stopPropagation();
                $.cookie('is_switcher', 1, {expires: date});
                window.location.replace(switchUrl);

            });

            btnNo.on('click', function () {
                mpStoreSwitcherPopup.hide();
                $.cookie('is_switcher', 0, {expires: date});
            });
        }
    });

    return $.mageplaza.storeSwitcher;
});

