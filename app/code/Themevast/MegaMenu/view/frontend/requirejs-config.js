/**
 * Copyright © 2016 Themevast. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            megamenu: 'Themevast_MegaMenu/js/menu',
			tv_googlemap: 'Themevast_MegaMenu/js/googlemap',
        }
    },
	shim:{
		"Themevast_MegaMenu/js/menu": ["jquery"],
		"Themevast_MegaMenu/js/googlemap": ["jquery"]
	}
};
