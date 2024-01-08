/**
 * Copyright � 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
define([
    'jquery',
	'mage/smart-keyboard-handler',
	'mage/mage',
	'mage/ie-class-fixer',
	'themevast/owl',
    'domReady!'
], function ($, keyboardHandler) {
    'use strict';

	if ($('body').hasClass('checkout-cart-index')) {
		if ($('#co-shipping-method-form .fieldset.rates').length > 0 && $('#co-shipping-method-form .fieldset.rates :checked').length === 0) {
			$('#block-shipping').on('collapsiblecreate', function () {
				$('#block-shipping').collapsible('forceActivate');
			});
		}
	}

	$('.cart-summary').mage('sticky', {
		container: '#maincontent'
	});

	$('.panel.header > .header.links').clone().appendTo('#store\\.links');

	keyboardHandler.apply();

	$('.mobile-navigation').on('click', function(e) {
		e.stopPropagation();
		$(".megamenu-top").toggleClass('open-nav');
		$(".body").toggleClass('mobile-menu-open')
	});
	$('.menu-overlay, .close-menu').on('click', function(e) {
		if ($('.megamenu-top').hasClass('open-nav')) {
			$(".body").removeClass('mobile-menu-open')
			$(".megamenu-top").toggleClass('open-nav')
		}
	})

    $(window).scroll(function () {
		if ($(this).scrollTop() > 100) {
			$('#back-top').fadeIn();
		} else {
			$('#back-top').fadeOut();
		}
	});

	$('#back-top').click(function () {
		$('body,html').animate({
			scrollTop: 0
		}, 800);
		return false;
	});
	$(".header-toplinks .fa-user").click(function(){
        $(".links").toggle();
    });
	$(".main-menu-bar h3").click(function(){
        $(".nav-sections").toggle();
    });
    $(".quick-access .fa-cog").click(function(){
        $(".access-content").toggle();
    });
    $(".verticalmenu h3").click(function(){
        jQuery(".menu-item").toggle();
    });
	$(".about-box3 .box-drop1").click(function(){
		$(".setting").toggle();
	});

	$(".avatar-team .owl-carousel").owlCarousel({
		autoplay :false,
		items : 4,
		smartSpeed : 500,
		dotsSpeed : 500,
		rewindSpeed : 500,
		nav : false,
		autoplayHoverPause : true,
		dots : false,
		scrollPerPage:true,
		margin: 30,
		loop: false,
		responsive: {
			0: {
				items: 1,
			},
			480: {
				items:2,
			},
			768: {
				items:2,
			},
			991: {
				items:3,
			},
			1200: {
				items:4,
			}
		}
	});

	jQuery(document).ready(function () {
		jQuery('.about-box .box-drop').children('.drop-content').slideUp();
		jQuery('.about-box .box-drop > h3').on('click', function () {
			jQuery('.about-box .box-drop').children('.drop-content').slideUp();
			var edq = jQuery(this);
			edq.parent().siblings().find('span').removeClass('active');
			edq.parent().siblings().find('span').addClass('notactive');
			if (edq.children('span').hasClass('notactive')) {
				edq.parent().children('.drop-content').slideDown();
				edq.children('span').removeClass('notactive');
				edq.children('span').addClass('active');
			}else if(edq.children('span').hasClass('active')){
				edq.parent().children('.drop-content').slideUp();
				edq.children('span').removeClass('active');
				edq.children('span').addClass('notactive');
			}else{

			}
		});
	});
});
