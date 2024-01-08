/**
 *  Amasty Show More Component
 *
 *  @copyright 2009-2020 Amasty Ltd
 *  @license   https://amasty.com/license.html
 */

define([
    'jquery',
    'mage/translate'
], function ($) {
    'use strict';

    $.widget('mage.amShowMore', {
        options: {
            maxCharaters: 200,
            classes: {
                active: '-active'
            },
            selectors: {
                button: '[data-amshowmore-js="button"]',
                text: '[data-amshowmore-js="text"]'
            }
        },
        button: $('<span>', {
            'class': 'amshowmore-button',
            'data-amshowmore-js': 'button'
        }),

        _create: function () {
            var self = this;

            self.text = self.element.find(self.options.selectors.text);
            self.text.addClass(self.options.classes.active);

            if (self.text.text().length < self.options.maxCharaters) {
                return;
            }

            self.initButton();
        },

        toggle: function () {
            var self = this,
                options = this.options,
                buttonText = self.button.text() === $.mage.__('Show more') ? $.mage.__('Show less') : $.mage.__('Show more');

            self.text.toggleClass(options.classes.active);
            self.button.text(buttonText);
        },

        initButton: function () {
            var self = this;

            self.text.removeClass(self.options.classes.active);
            self.text.after(self.button.clone());
            self.button = self.element.find(self.options.selectors.button);
            self.button.text($.mage.__('Show more'));

            self.button.on('click', function (e) {
                e.preventDefault();
                self.toggle();
            });
        }
    });

    return $.mage.amShowMore;
});
