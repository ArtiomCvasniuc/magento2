define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Amasty_Base/vendor/slick/slick.min',
    'Amasty_AdvancedReview/vendor/fancybox/jquery.fancybox.min',
], function ($) {
    'use strict';

    $.widget('mage.amReview', {
        options: {
            slidesToShow: 3,
            slidesToScroll: 3,
            centerMode: false,
            variableWidth: false,
            responsive: [
                {
                    breakpoint: 460,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2,
                    }
                },
                {
                    breakpoint: 360,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                    }
                }
            ],
            selectors: {
                imageContainer: '[data-amreview-js="review-images"]',
                sliderItem: '[data-amreview-js="slider-item"]',
                hide: 'hidden',
                active: '-active'
            }
        },

        _create: function () {
            $('[data-amreview-js="show-more"]').on('click', function () {
                $('[data-amreview-js="percent"]').toggle();
                $('[data-amreview-js="summary-details"]').toggle();
            });

            // Fix problem with slick init
            $('#tab-label-reviews').on('click', function () {
                $('.amreview-images.slick-initialized').slick('setPosition');
            });

            this.initSlider();
        },

        initSlider: function () {
            var self = this,
                slidesToShow = $(window).width() > 768 ? self.options.slidesToShow : 1,
                $imageContainer = self.element.find(self.options.selectors.imageContainer);

            if ($imageContainer.length) {
                $.each($imageContainer, function () {
                    var $element = $(this);

                    $element.find('a').fancybox({
                        loop: true,
                        toolbar: false,
                        baseClass: 'amrev-fancybox-zoom'
                    });

                    if ($element.find(self.options.selectors.sliderItem).length > slidesToShow && self.options.slidesToShow) {
                        $element.slick(self.options);
                        $element.slick('resize');
                    }
                });
            }
        }
    });

    return $.mage.amReview;
});
