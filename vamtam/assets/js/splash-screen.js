( function( $, undefined ) {
	'use strict';

	$(function() {
		var body = $( document.body );

		var wrapper  = $('.vamtam-splash-screen');
		var progress = wrapper.find( '.vamtam-splash-screen-progress' );

		var removeSplashScreen = function() {
			body.trigger('vamtam-hide-splash-screen');
		};

		// allow the first image at most 1000ms to load
		var timeout = setTimeout( removeSplashScreen, 1000 );

		var images = -1;
		var loaded = 0;

		try {
			imagesLoaded( window, removeSplashScreen ).on( 'progress', function( instance ) {
				if ( images < 0 ) {
					images = instance.images.length;
				}

				requestAnimationFrame( function() {
					progress.css( 'width', ( ++loaded / images ) * 100 + '%' );
				} );

				// allow any consecutive image at most 500ms to load
				clearTimeout( timeout );
				timeout = setTimeout( removeSplashScreen, 500 );
			} );
		} catch( e ) {
			removeSplashScreen();
		}

		body.one('vamtam-hide-splash-screen', function() {
			requestAnimationFrame( function() {
				progress.css( 'width', '100%' );

				setTimeout( function() {
					wrapper.fadeOut( 500 );
				}, 250 );
			} );
		}).on('vamtam-preview-splash-screen', function() {
			progress.css( {
				'transition-duration': 0,
				width: '0%'
			} );

			progress.css( {
				'transition-duration': '1s'
			} );

			wrapper.css( 'display', '' );

			setTimeout( function() {
				requestAnimationFrame( function() {
					progress.css( 'width', '100%' );

					setTimeout( function() {
						wrapper.fadeOut( 500 );
					}, 1000 );
				} );
			}, 100 );
		} );
	});
} )( jQuery );