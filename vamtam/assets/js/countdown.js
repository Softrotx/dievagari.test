(function($, undefined) {
	'use strict';

	$(function() {
		$('.vamtam-countdown').each(function() {
			var days = $('.vamtamc-days .value', this);
			var hours = $('.vamtamc-hours .value', this);
			var minutes = $('.vamtamc-minutes .value', this);
			var seconds = $('.vamtamc-seconds .value', this);

			var until = parseInt($(this).data('until'), 10);
			var done = $(this).data('done');

			var self = $(this);

			var updateTime = function() {
				var now = Math.round( (+new Date()) / 1000 );

				if(until <= now) {
					clearInterval(interval);
					self.html($('<span />').addClass('vamtamc-done vamtamc-block').html($('<span />').addClass('value').text(done)));
					return;
				}

				var left = until-now;

				seconds.text(left%60);

				left = Math.floor(left/60);
				minutes.text(left%60);

				left = Math.floor(left/60);
				hours.text(left%24);

				left = Math.floor(left/24);
				days.text(left);
			};

			var interval = setInterval(updateTime, 1000);
		});
	});
})(jQuery);