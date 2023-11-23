(function($, undefined) {
	"use strict";

	var picker, selected, fbt;

	var init = function() {

		picker = $('<div id="vamtam-colorpicker"></div>').hide();

		$('body').append(picker);
		fbt = $.farbtastic('#vamtam-colorpicker');

		picker.append(function() {
			return $('<a class="transparent">transparent</a>').on( 'click', function() {
				if (selected) {
					$(selected).val('transparent').css({
						background: 'white'
					});
					picker.fadeOut();
				}
			});
		});
	};

	$.fn.vamtamColorPicker = function() {
		var self = this;

		if(!picker)
			init();

		$('[type=color], .vamtam-color-input', self).not('.vamtam-colorpicker').each(function() {
			$(this).prop('type', 'text').addClass('vamtam-colorpicker');
			fbt.linkTo(this);
		}).on('focus', null, function() {
			if (selected) $(selected).removeClass('colorwell-selected');

			var self = this;
			fbt.linkTo(function(color) {
				$(self).val(color).change();
			});

			picker.css({
				position: 'absolute',
				left: $(this).offset().left + $(this).outerWidth(),
				top: $(this).offset().top
			}).fadeIn();
			$(selected = this).addClass('colorwell-selected');
		}).on('blur', null, function() {
			picker.fadeOut();
		}).on('change keyup', null, function() {
			$(this).css({
				'background-color': $(this).val()
			});
		});

		return this;
	};
})(jQuery);