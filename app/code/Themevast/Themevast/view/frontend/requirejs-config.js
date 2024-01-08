/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    "map": {
        '*': {
			'catalogAddToCart': 'Themevast_Themevast/js/catalog-add-to-cart',
			'Magento_Catalog/js/catalog-add-to-cart': 'Themevast_Themevast/js/catalog-add-to-cart'
        },
    },
	"shim": {
		"themevast/owl": ["jquery"],
		"themevast/choose": ["jquery"],
		"themevast/fancybox": ["jquery"]
	},
	'paths': {
		'themevast/fancybox': "Themevast_Themevast/js/jquery_fancybox",
        "themevast/owl": "Themevast_Themevast/js/owl_carousel",
        "themevast/choose": "Themevast_Themevast/js/jquery_choose",
        "themevast/equalheight": "Themevast_Themevast/js/equalheight"
    },
    "deps": ['themevast/theme']
};
