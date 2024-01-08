define([
    "jquery",
    'domReady!'
], function ($) {
    'use strict';

    $.widget('mage.amProductReviews', {
        options: {
            productId: ''
        },
        selectors: {
            widget: '[data-amreview-js="widget-container"]',
            reviewList: '[data-amreview-js="review-block-{id}"]',
            pager: '[data-amreview-js="amreviews-pages-*]'
        },

        _create: function () {
            var self = this;

            self.pager = $(self.selectors.pager);
            self.widget = $(self.selectors.widget);

            this.element.find('a').on('click', function (event) {
                event.preventDefault();
                self.options.data.isAjax = true;
                $.ajax({
                    url: event.currentTarget.href,
                    cache: true,
                    data: self.options.data,
                }).done(function (data) {
                    var widgetSelect = self.selectors.reviewList.replace('{id}', self.options.productId);
                    $(widgetSelect).replaceWith(data.reviewsBlock).trigger('contentUpdated');
                    $(widgetSelect).trigger('contentUpdated');
                });
            });
        },
    });

    return $.mage.amProductReviews;
});
