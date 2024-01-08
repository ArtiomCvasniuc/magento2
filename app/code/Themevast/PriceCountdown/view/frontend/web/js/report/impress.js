
'use strict';
define([
    'magestore/jquery',
    'magestore/widget'
], function($) {
	"use strict";

	$.widget('magestore.impress', {
		options: {
		    url: '',
		    slider_id: '',
		},

		_create: function() {
			var o = this.options;
			$.ajax({
			    url: o.url,
			    type: 'POST',
			    dataType: 'html',
			    data: {slider_id: o.slider_id},
			});
		},
	});
	return $.magestore.impress;
});
