(function($, undefined) {
	'use strict';

	window.VAMTAM = window.VAMTAM || {};

	window.VAMTAM.upload = {
		init: function() {
			var file_frame;

			$(document).on('click', '.vamtam-upload-button', function(e) {
				var field_id = $(this).attr('data-target');

				file_frame = wp.media.frames.file_frame = wp.media({
					multiple: false,
					library: {
						type: $(this).hasClass('vamtam-video-upload') ? 'video' : 'image'
					}
				});

				file_frame.on( 'select', function() {
					var attachment = file_frame.state().get('selection').first();
					window.VAMTAM.upload.fill(field_id, attachment.attributes.url);
				});

				file_frame.open();
				e.preventDefault();
			});

			$(document).on('click', '.vamtam-upload-clear', function(e) {
				window.VAMTAM.upload.remove($(this).attr('data-target'));
				e.preventDefault();
			});

			$(document).on('click', '.vamtam-upload-undo', function(e) {
				window.VAMTAM.upload.undo($(this).attr('data-target'));
				e.preventDefault();
			});
		},

		fill: function(id, str) {
			if (/^\s*$/.test(str)) {
				window.VAMTAM.upload.remove(id);
				return;
			}

			var target = $('#' + id);
			target.data('undo', target.val());
			target.val(str);
			target.siblings('.vamtam-upload-clear, .vamtam-upload-undo').css({
				display: 'inline-block'
			});
			window.VAMTAM.upload.preview(id, str);
		},

		preview: function(id, str) {
			$('#' + id + '_preview').parents('.upload-basic-wrapper').addClass('active');
			$('#' + id + '_preview').find('img').attr('src', str).css({
				display: 'inline-block'
			});
		},

		remove: function(id) {
			var inp = $('#' + id);
			$('#' + id + '_preview').find('img').attr('src', '').hide();
			$('#' + id + '_preview').parents('.upload-basic-wrapper').removeClass('active');
			inp.data('undo', inp.val()).val('')
				.siblings('.vamtam-upload-undo').css({
				display: 'inline-block'
			})
				.siblings('.vamtam-upload-clear').hide();
		},
		undo: function(id) {
			var inp = $('#' + id);
			this.preview(id, inp.data('undo'));
			inp.val(inp.data('undo'));
			inp.data('undo', '').siblings('.vamtam-upload-undo').hide();
			var remove = inp.siblings('.vamtam-upload-clear');
			if (inp.val().length === 0 && remove.is(':visible')) {
				remove.hide();
			} else if (inp.val().length > 0 && remove.is(':hidden')) {
				remove.css({
					display: 'inline-block'
				});
			}
		}
	};
})(jQuery);