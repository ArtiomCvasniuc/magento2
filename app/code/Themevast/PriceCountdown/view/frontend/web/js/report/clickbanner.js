
'use strict';
define([
    'magestore/jquery',
    'magestore/widget'
], function($) {

	$.widget('magestore.clickbanner', {
		options: {
		    url: '',
		    slider_id: '',
		    banner_id: '',
		},

		_create: function() {
			var o = this.options;
			this.element.each(function(index, el) {
				$(el).click(function(event) {
					// event.preventDefault();
					$.ajax({
					    url: o.url,
					    type: 'POST',
					    dataType: 'html',
					    data: {banner_id: o.banner_id, slider_id: o.slider_id},
					}).done(function() {
						console.log("success");
					});

				});
			});

		},
	});
	return $.magestore.clickbanner;
});
