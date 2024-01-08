/**
 * Copyright © 2016 Themevast. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            menuLayout: 'Themevast_MegaMenu/js/menu-layout',
			jqueryTmpl: "Themevast_MegaMenu/js/jquery.tmpl",
			tvJqueryUi: "Themevast_MegaMenu/js/jquery-ui.min",
			categoryChooser: "Themevast_MegaMenu/js/categorychooser",
			megamenu: 'Themevast_MegaMenu/js/menu',
			wysiwygEditor: 'Themevast_MegaMenu/js/wysiwyg-editor',
			tv_googlemap: 'Themevast_MegaMenu/js/googlemap'
        }
    },
	shim:{
		"Themevast_MegaMenu/js/menu-layout": ["jqueryTmpl","tvJqueryUi","categoryChooser","wysiwygEditor"],
		"Themevast_MegaMenu/js/jquery.tmpl": ["jquery"],
		"Themevast_MegaMenu/js/jquery-ui.min": ["jquery/jquery-ui"],
		"Themevast_MegaMenu/js/menu": ["jquery"],
		"Themevast_MegaMenu/js/wysiwyg-editor": ["jquery"],
		"Themevast_MegaMenu/js/googlemap": ["jquery"]
	}
};
