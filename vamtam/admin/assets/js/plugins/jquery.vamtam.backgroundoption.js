(function($, undefined) {
	"use strict";

	$.fn.vamtamBackgroundOption = function() {
		$(this).find('.vamtam-config-row.background:not(.vamtambg-loaded)').each(function() {
			var row = $(this).addClass('vamtambg-loaded'),
				size = row.find('.bg-block.bg-size'),
				repeat = row.find('.bg-block.bg-repeat'),
				position = row.find('.bg-block.bg-position');

			size.on('change', 'input', function() {
				repeat.add(position).show();

				if($(':checked', size).val() === 'cover')
					repeat.add(position).hide();
			}).change();

		});
		return this;
	};
})(jQuery);