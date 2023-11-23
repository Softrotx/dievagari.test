/* jshint esversion: 6, module: true */
export default function() {
	const $ = jQuery;

	const body = $( document.body );

	let settings = {};

	const mediaElement = function( context ) {
		if ( typeof window._wpmejsSettings !== 'undefined' ) {
			settings = $.extend( true, {}, window._wpmejsSettings );
		}

		settings.classPrefix = 'mejs-';
		settings.success = settings.success || function( mejs ) {
			var autoplay, loop;

			if ( mejs.rendererName && -1 !== mejs.rendererName.indexOf( 'flash' ) ) {
				autoplay = mejs.attributes.autoplay && 'false' !== mejs.attributes.autoplay;
				loop = mejs.attributes.loop && 'false' !== mejs.attributes.loop;

				if ( autoplay ) {
					mejs.addEventListener( 'canplay', function() {
						mejs.play();
					}, false );
				}

				if ( loop ) {
					mejs.addEventListener( 'ended', function() {
						mejs.play();
					}, false );
				}
			}
		};

		if ( 'mediaelementplayer' in $.fn ) {
			// Only initialize new media elements.
			$( '.wp-audio-shortcode, .wp-video-shortcode', context )
				.not( '.mejs-container' )
				.filter(function () {
					return ! $( this ).parent().hasClass( 'mejs-mediaelement' );
				})
				.mediaelementplayer( settings );
		}
	};

	// infinite scrolling
	if ( document.body.classList.contains( 'pagination-infinite-scrolling' ) ) {
		var last_auto_load = 0;
		$(window).on('resize scroll', function(e) {
			var button = $('.lm-btn'),
				now_time = e.timeStamp || (new Date()).getTime();

			if(now_time - last_auto_load > 500 && parseFloat(button.css('opacity'), 10) === 1 && $(window).scrollTop() + $(window).height() >= button.offset().top) {
				last_auto_load = now_time;
				button.click();
			}
		});
	}

	if ( ! ( document.body.classList.contains( 'fl-builder-active' ) ) ) {
		document.body.addEventListener( 'click', ( e ) => {
			var button = e.target.closest( '.load-more' );

			if ( button ) {
				e.preventDefault();
				e.stopPropagation(); // customizer support

				var self = $( button );
				var list = self.prev();
				var link = button.querySelector( 'a' );

				if ( button.classList.contains( 'loading' ) ) {
					return false;
				}

				self.addClass( 'loading' ).find( '> *' ).animate({opacity: 0});

				$.post( VAMTAM_FRONT.ajaxurl, {
					action: 'vamtam-load-more',
					query: JSON.parse( link.dataset.query ),
					other_vars: JSON.parse( link.dataset.otherVars ),
				}, ( result ) => {
					var content = $( result.content );

					mediaElement( content );

					var visible = list.find( '.cbp-item:not( .cbp-item-off )' ).length;

					list.cubeportfolio( 'append', content, () => {
						if ( visible === list.find( '.cbp-item:not( .cbp-item-off )' ).length ) {
							const warning = document.createElement( 'p' );
							warning.classList.add( 'vamtam-load-more-warning' );
							warning.innerText = list.data( 'hidden-by-filters' );

							button.after( warning );

							body.one( 'click', () => {
								warning.remove();
							} );
						}

						button.outerHTML = result.button;

						self.removeClass( 'loading' ).find( '> *' ).animate({opacity: 1});

						window.VAMTAM.resizeElements();
					} );
				});
			}
		} );
	}
}