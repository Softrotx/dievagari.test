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
(function($, undefined) {
	"use strict";

	window.VAMTAM = window.VAMTAM || {};

	$(function() {
		$('body').vamtamColorPicker().vamtamBackgroundOption();

		window.VAMTAM.upload.init();

		$(document).on('change select', '[data-field-filter]', function() {
			var prefix = $(this).attr('data-field-filter');
			var selected = $(':checked', this).val();

			var others = $(this).closest('.vamtam-config-group').find('.' + prefix).filter(':not(.hidden)');
			others.show().filter(':not(.' + prefix + '-' + selected + ')').hide();
		});

		$('[data-field-filter]').change();

		$(document).on('change', '.social_icon_select_sites', function() {
			var wrap = $(this).closest('p').siblings('.social_icon_wrap');
			wrap.children('p').hide();
			$('option:selected', this).each(function() {
				wrap.find('.social_icon_' + $(this).val()).show();
			});
		});

		$(document).on('change', '.num_shown', function() {
			var wrap = $(this).closest('p').siblings('.hidden_wrap');
			wrap.children('div').hide();
			$('.hidden_el:lt(' + $(this).val() + ')', wrap).show();
		});

		$('.metabox').each(function() {
			var meta_tabs = $('<ul>').addClass('vamtam-meta-tabs');

			$('.config-separator:first', this).before(meta_tabs);
			$('.config-separator', this).each(function() {
				var id = $(this).text().replace(/[\s\n]+/g, '').toLowerCase();
				$(this).nextUntil('.config-separator').wrapAll('<div class="vamtam-meta-part" id="tab-' + id + '"></div>');
				$(this).css('cursor', 'pointer');
				if ($(this).next().is('.vamtam-meta-part')) {
					meta_tabs.append('<li class="vamtam-meta-tab '+$(this).attr('data-tab-class')+'"><a href="#tab-' + id + '" title="">' + $(this).text() + '</a></li>');
				}
				$(this).remove();
			});

			if(meta_tabs.children().length > 1) {
				meta_tabs.closest('.metabox').tabs();
			} else {
				meta_tabs.hide();
			}
		});

		$('#vamtam-config').tabs({
			activate: function(event, ui) {
				var hash = ui.newTab.context.hash;
				var element = $(hash);
				element.attr('id', '');
				window.location.hash = hash;
				element.attr('id', hash.replace('#', ''));

				$('.save-vamtam-config').show();
				if (ui.newTab.hasClass('nosave')) $('.save-vamtam-config').hide();
			},
			create: function(event, ui) {
				if (ui.tab.hasClass('nosave')) $('.save-vamtam-config').hide();
			}
		});

		$('body').on('click', '.info-wrapper > a', function(e) {
			var other = $(this).attr('data-other');
			$(this).attr('data-other', $(this).text()).text(other);
			$(this).siblings('.desc').slideToggle(200);
			e.preventDefault();
		});

		// Asynchronously posts a given form using the default Settings API approach (options.php).
		function save_options_ajax( $formToSave, disableForm ) {
			$formToSave = $formToSave || $( 'form[method="post"][action="options.php"]' );
			if ( $formToSave ) {
				$formToSave.unbind( 'submit' );
				$formToSave.on( 'submit', function () {
					$('#vamtam-post-result span.spinner').addClass( 'is-active' );
					var b =  $(this).serialize();

					// Disable here so serialize can work properly.
					disableForm && $formToSave.find( ':input' ).attr("disabled", true);

					$.post( 'options.php', b )
						.error( function() {
							$('#vamtam-post-result .vamtam-post-msg-failure').show();
							$('#vamtam-post-result').show('slow');
							$('#vamtam-post-result span.spinner').removeClass( 'is-active' );
						})
						.success( function() {
							$('#vamtam-post-result .vamtam-post-msg-success').show();
							$('#vamtam-post-result').show('slow');
							$('#vamtam-post-result span.spinner').removeClass( 'is-active' );
						})
						.done( function () {
							setTimeout( function () {
								$('#vamtam-post-result').hide('slow');
								$('#vamtam-post-result > p').hide();
							}, 3000 );
							disableForm && $formToSave.find( ':input' ).attr("disabled", false);
						} );
						return false;
				});
				$formToSave.submit();
			}
		}

		$( document ).ready(function() {

			//help page, enable status gathering radios
			$( '#vamtam-ts-help form input[type="radio"]' ).each( function () {
				$(this).on( 'change', function() {
					save_options_ajax( $(this).closest( 'form' ), true );
				});
			} );

			//dashboard register copy button
			$( 'button#vamtam-check-license' ).click( function () {
				save_options_ajax( $(this).closest( 'form' ) );
			} );
		});
	});

})(jQuery);

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
// Contains logic related to tgmpa.
(function($, undefined) {
	"use strict";

	$(function() {

		// Filters the tgmpa plugins table for a given plugin category.
		function filterPluginsTableByCategory( category, target, isInitial ) {
			var activeTab = document.querySelector( '#vamtam-plugins-filters ul li a.active' );

			if ( ( ! category || activeTab === target ) && ! isInitial ) {
				return; // nothing to do
			}

			var table, tr, td, i;
			table = document.getElementsByClassName("wp-list-table")[ 0 ];
			tr = table.querySelectorAll( "tbody tr" );

			//change tabs active btn
			if ( activeTab ) {
				if ( activeTab.dataset.filter !== category ) {
					activeTab.classList.remove( 'active' );
					target.classList.add( 'active' );
				}
			}

			// Loop through all table rows, and hide those who don't match the selected category
			for ( i = 0; i < tr.length; i++ ) {
			  td = tr[i].getElementsByTagName( "td" )[ 0 ];
			  if ( td ) {
				var has_category = tr[i].querySelector( '[data-category="' + category + '"]' ) || category === 'all';
				if ( has_category ) {
				  tr[i].style.display = "";
				} else {
				  tr[i].style.display = "none";
				}
			  }
			}
		}

		$( document ).ready(function() {

			// Theme setup page
			var $isVerified = $( '#vamtam-verified-code' );
			if ( $( '.vamtam-ts' ).length && ! $isVerified.length ) { // true only for dashboard page with activated theme.
				//the verification check is really basic here, but we just use it to hide some tgmpa notices.
				$( '#setting-error-tgmpa' ).hide(); // if not verified, hide tgmpa notices.
			}

			// Plugins page (tgmpa), form submit.
			$('form#tgmpa-plugins').one( 'submit', function (e) {
				e.preventDefault();
				var applyBtn = $('input#doaction2');
				applyBtn.addClass('disabled');
				applyBtn.after( $('<span />').addClass('spinner is-active') );
				setTimeout( function() {
					// The event is not sent to the form when calling the form.submit() method directly,
					// so we click the apply (submit) button once more.
					applyBtn.click();
				}, 300 );
			} );

			// Plugins page (tgmpa), change return message after plugin installation.
			var $returnLink = $( 'p > a[href$="admin.php?page=tgmpa-install-plugins"]' );
			if ( $returnLink.length ) { // true only for dashboard page with activated theme.
				$returnLink.addClass( 'button button-primary' );
				$returnLink.attr( 'href', '/wp-admin/admin.php?page=vamtam_theme_setup' );
				$returnLink.text( 'Return To Theme Setup' );
			}

			// Plugins page (tgmpa), filter tabs btns
			$( '#vamtam-plugins-filters ul li a' ).click( function () {
				var filter = $(this).data( 'filter' );
				filterPluginsTableByCategory( filter, $(this)[ 0 ] );
				if ( filter === 'recommended' ) {
					$( '#vamtam-recommended-plugins-notice' ).css( 'display', 'flex' );
					$( '#vamtam-required-plugins-notice' ).hide();
				} else {
					$( '#vamtam-recommended-plugins-notice' ).hide();
					$( '#vamtam-required-plugins-notice' ).css( 'display', 'inline-block' );
				}
			} );

			// Plugins page (tgmpa), show/hide filter tabs
			var $filters = $( '#vamtam-plugins-filters ul li a' );

			// If we have plugin_status on url we filter based on that (only for recommended).
			var searchParams = new window.URLSearchParams( window.location.search );
			var filterInUrl = false;
			if ( searchParams.has( 'plugin_status' ) ) {
				var pluginStatus = searchParams.get( 'plugin_status' );
				if ( pluginStatus === 'recommended' ) {
					var $recommendedTab = $( $filters ).parent().find( 'a[data-filter="recommended"]' );
					$recommendedTab.toggleClass( 'active' );
					$( '#vamtam-recommended-plugins-notice' ).css( 'display', 'inline-block' );
					filterInUrl = true;
					filterPluginsTableByCategory( 'recommended', $recommendedTab[ 0 ], true );
				}
			}

			var isDefaultHidden = false;
			$filters.each( function () {
				var filterCategory = $(this).data( 'filter' );
				if ( filterCategory !== 'all' ) {
					var categoryExists = $( '.wp-list-table tbody tr [data-category="' + filterCategory + '"]' ).length > 0;
					if( ! categoryExists ) {
						$(this).hide();
						if ( filterCategory === 'required' ) {
							isDefaultHidden = true;
						}
					}
					// This is for the initial table filter
					if ( ! filterInUrl && ( ( categoryExists && filterCategory === 'required' ) || ( categoryExists && isDefaultHidden ) ) ) {
						$(this).toggleClass( 'active' );
						if ( filterCategory === 'recommended' ) {
							$( '#vamtam-recommended-plugins-notice' ).css( 'display', 'flex' );
						} else {
							$( '#vamtam-required-plugins-notice' ).css( 'display', 'inline-block' );
						}
						filterPluginsTableByCategory( filterCategory, $(this)[ 0 ], true );
					}
				}
			} );

			if ( ! isDefaultHidden && ! filterInUrl ) {
				// Make required plugins checked by default.
				var allCb = $( '.vamtam-ts table input#cb-select-all-1' );
				allCb.trigger( 'click' );
			}

			// Hide tgmpa's recommended plugins notice.
			var $recommendedPluginsInstallNotice = $( '#setting-error-tgmpa.notice-warning > p > strong > span:contains("This theme recommends the following plugin")' );
			if ( $recommendedPluginsInstallNotice.length ) {
				$recommendedPluginsInstallNotice.hide();
				var $requiredPluginsToInstall = $( '#setting-error-tgmpa.notice-warning > p > strong > span:contains("This theme requires the following plugin")' );
				if ( ! $requiredPluginsToInstall.length ) {
					//Remove the link to install plugins as it was generated due to not istalled recommended plugins and we only care about required (not installed) plugins.
					var $installLink = $( '#setting-error-tgmpa.notice-warning > p > strong > span > a:contains("Begin installing plugins")' );
					var $notices = $installLink.parent().siblings();
					var hasVisibleNotices = false;
					$notices.each( function () {
						if ( $(this).is( ':visible' ) ) {
							hasVisibleNotices = true;
						}
					} );
					//If we have no other visible notices
					if ( hasVisibleNotices  ) {
						//just hide the irrelevant action link.
						$installLink.hide();
						$installLink[ 0 ].nextSibling.nodeValue = '';// hide the vertical barrier as well.
					} else {
						//hide the whole tgmpa notice.
						$installLink.closest( '#setting-error-tgmpa' ).hide();
					}
				}
			}
		});
	});

})(jQuery);
