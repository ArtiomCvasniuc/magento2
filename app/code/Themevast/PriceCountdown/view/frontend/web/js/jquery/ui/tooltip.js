
'use strict';
define([
    'magestore/jquery',
    'magestore/widget'
], function($) {
	"use strict";

	$.widget('magestore.tooltip', {
		options: {
		    imgsrc: '',
		    classPrefix: '',
		    width : '200px',
		    height: '200px'
		},

		_create: function() {
			var m = this,o = m.options;
			//Select all anchor tag with rel set to tooltip
			this.element.each(function(index, el) {
				$(el).mouseover(function(e) {
					if ($(this).data('tooltip-image') == '') {
						return ;
					};
					var tooltip = $('<div class="'+ o.classPrefix +'tooltip"><div class="'+ o.classPrefix +'tipHeader"></div><div class="'+ o.classPrefix +'tipBody">' + '<img src="' + $(this).data('tooltip-image') + '" />'  + '</div><div class="'+ o.classPrefix +'tipFooter"></div></div>');

					tooltip.css({
					    position: 'absolute',
					    'z-index': '9999',
					    color: '#fff',
					    'font-size': '10px',
					    width: o.width,
					    height: o.height
					});

					tooltip.children('.'+ o.classPrefix +'tipBody').css({
					    'background-color': '#000',
					    padding: '5px'
					});

				    //Append the tooltip template and its value
				    $(this).append(tooltip);

				    //Set the X and Y axis of the tooltip
				    tooltip.css('top', e.pageY + 10 -950);
				    tooltip.css('left', e.pageX + 20 -400);

				    //Show the tooltip with faceIn effect
				    tooltip.fadeIn('500');
				    tooltip.fadeTo('10',0.8);

				}).mousemove(function(e) {
					var tooltip = $('.'  + o.classPrefix + 'tooltip');
				    //Keep changing the X and Y axis for the tooltip, thus, the tooltip move along with the mouse
				    tooltip.css('top', e.pageY + 10 -950);
				    tooltip.css('left', e.pageX + 20 -400);

				}).mouseout(function() {
				    //Remove the appended tooltip template
				    $(this).children('.'+ o.classPrefix +'tooltip').remove();

				});
			});
		},
	});
	return $.magestore.tooltip;
});
