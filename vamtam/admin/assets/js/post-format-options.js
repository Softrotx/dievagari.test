(function($, undefined) {
	"use strict";

	$(function() {
		var groups = [{
			options: '#vamtam-post-format-options',
			select: '.editor-post-format select'
		}, {
			options: '#vamtam-portfolio-format-options',
			select: '#vamtam-portfolio-formats-select select'
		}];

		groups.forEach( function(group) {
			var post_formats = $(group.options);
			if(post_formats.length) {
				var pf_tabs = post_formats.find('.vamtam-meta-tabs').hide();

				var callback = function() {
					var select = document.querySelector( group.select );

					// we need to wait for the dropdown to be added to the DOM first
					if ( ! select ) {
						return window.setTimeout( callback, 200 );
					}

					var checked = select.value,
						format_name = 'post-format-'+checked,
						tab = pf_tabs.find('li.vamtam-'+ format_name + ' a');

					tab.click();

					pf_tabs.parent().find('.vamtam-config-row.vamtam-all-formats').appendTo($(tab.attr('href')));
				};

				$( document ).on( 'change', group.select, callback );
				callback();
			}
		} );
	});
})(jQuery);