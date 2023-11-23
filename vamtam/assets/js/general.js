/* jshint multistr:true */
(function( $, undefined ) {
	"use strict";

	window.VAMTAM = window.VAMTAM || {}; // Namespace

	$(function () {
		window.VAMTAM.admin_bar_fix = document.body.classList.contains( 'admin-bar' ) ? 32 : 0;

		if ( /iPad|iPhone|iPod/.test( navigator.userAgent ) && ! window.MSStream) {
			requestAnimationFrame( function() {
				document.documentElement.classList.add( 'ios-safari' );
			} );
		}

		// trigger resize after publishing a layout in order to deal with the disappearance of Beaver's UI
		if ( 'FLBuilder' in window ) {
			FLBuilder.addHook( 'didPublishLayout', function() {
				window.dispatchEvent( new Event( 'resize' ) );
			} );
		}

		// prevent hover when scrolling
		(function() {
			var wrapper = document.getElementById( 'page' ),
				timer;

			window.addEventListener( 'scroll', function() {
				clearTimeout(timer);

				requestAnimationFrame( function() {
					wrapper.style.pointerEvents = 'none';

					timer = setTimeout( function() {
						wrapper.style.pointerEvents = '';
					}, 300 );
				} );
			}, { passive: true } );
		})();


		// Code which depends on the window width
		// =====================================================================

		window.VAMTAM.resizeElements = function() {
			// video size
			$('.portfolio-image-wrapper,\
				#page .media-inner,\
				#page .loop-wrapper.news .thumbnail,\
				#page .portfolio-image .thumbnail,\
				.wp-block-embed-vimeo:not(.wp-has-aspect-ratio),\
				:not(.wp-block-embed__wrapper) > .vamtam-video-frame').find('iframe, object, embed, video').each(function() {

				setTimeout( function() {
					requestAnimationFrame( function() {
						var v_width = this.offsetWidth;

						this.style.width = '100%';

						if ( this.width === '0' && this.height === '0' ) {
							this.style.height = ( v_width * 9/16 ) + 'px';
						} else {
							this.style.height = ( this.height * v_width / this.width ) + 'px';
						}

						$( this ).trigger('vamtam-video-resized');
					}.bind( this ) );
				}.bind( this ), 50 );
			});

			setTimeout( function() {
				requestAnimationFrame( function() {
					$('.mejs-time-rail').css('width', '-=1px');
				} );
			}, 100 );
		};

		window.addEventListener( 'resize', window.VAMTAM.debounce( window.VAMTAM.resizeElements, 100 ), false );
		window.VAMTAM.resizeElements();
	} );

	// Low priority scripts are loaded later
	document.addEventListener('DOMContentLoaded', function () {
		window.VAMTAM.load_script( VAMTAM_FRONT.jspath + 'build/low-priority.min.js' );

		if ( ! ( window.CSS && window.CSS.supports && window.CSS.supports( '(--foo: red)' ) ) ) {
			window.VAMTAM.load_script( VAMTAM_FRONT.jspath + 'plugins/thirdparty/css-variables-polyfill.js' );
			window.VAMTAM.load_style( VAMTAM_FRONT.jspath + '../css/dist/ie11.css', 'all' );
		}
	}, { passive: true } );

})(jQuery);