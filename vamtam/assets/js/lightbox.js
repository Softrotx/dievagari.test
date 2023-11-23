( function( v, $, undefined ) {
	"use strict";

	$(function() {
		var lightbox = document.getElementById( 'vamtam-overlay-search' );
		var inside   = lightbox.children;

		// initialize styles before any animations
		lightbox.style.display = 'none';

		for ( var i = 0; i < inside.length; i++ ) {
			inside[ i ].style.animationDuration = '300ms';
			inside[ i ].style.display           = 'none';
		}

		// opening animation
		document.body.addEventListener( 'click', function( e ) {
			if ( e.target.closest( '.vamtam-overlay-search-trigger' ) ) {
				e.preventDefault();

				requestAnimationFrame( function() {
					lightbox.classList.add( 'vamtam-animated', 'vamtam-fadein' );
					lightbox.style.display = 'block';

					setTimeout( function() {
						requestAnimationFrame( function() {
							for ( var i = 0; i < inside.length; i++ ) {
								inside[ i ].style.display = 'block';
								inside[ i ].classList.add( 'vamtam-animated', 'vamtam-zoomin' );
							}

							requestAnimationFrame( function() {
								lightbox.querySelector( 'input[type=search]' ).focus();
							} );
						} );
					}, 200 );
				} );
			}
		} );

		var $lightbox = $( lightbox );

		// closing animation
		document.getElementById( 'vamtam-overlay-search-close' ).addEventListener( 'click', function(e) {
			e.preventDefault();

			requestAnimationFrame( function() {
				lightbox.classList.remove( 'vamtam-animated', 'vamtam-fadein' );
				lightbox.classList.add( 'vamtam-animated', 'vamtam-fadeout' );

				for ( var i = 0; i < inside.length; i++ ) {
					inside[ i ].classList.remove( 'vamtam-animated', 'vamtam-zoomin' );
					inside[ i ].classList.add( 'vamtam-animated', 'vamtam-zoomout' );
				}

				$lightbox.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
					requestAnimationFrame( function() {
						lightbox.style.display = 'none';
						lightbox.classList.remove( 'vamtam-animated', 'vamtam-fadeout' );

						for ( var i = 0; i < inside.length; i++ ) {
							inside[ i ].style.display = 'none';
							inside[ i ].classList.remove( 'vamtam-animated', 'vamtam-zoomout' );
						}
					} );
				} );
			} );
		} );
	});
} )( window.VAMTAM, jQuery );