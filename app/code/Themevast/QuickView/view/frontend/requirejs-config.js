/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    "map": {
        '*': {
			'productQuickview': 'Themevast_QuickView/js/quickview'
        },
    },
	"shim": {
		"quickview/cloudzoom": ["jquery"],
		"quickview/bxslider": ["jquery"]
	},
	'paths': {
		'quickview/cloudzoom': 'Themevast_QuickView/js/cloud-zoom',
        "quickview/bxslider": "Themevast_QuickView/js/jquery.bxslider"
    }
};
