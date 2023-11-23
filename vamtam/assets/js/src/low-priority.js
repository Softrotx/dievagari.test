/* jshint esversion: 6 */

import Portfolio from './portfolio';
import ajaxNavigation from './ajax-navigation';
import lightbox from './lightbox';

(function( v, undefined ) {
	'use strict';

	// portfolio
	v.portfolio = new Portfolio();

	// infinite scrolling
	ajaxNavigation();

	// lightbox
	lightbox();

	// scroll to top button
	{
		var st_buttons = document.querySelectorAll( '.vamtam-scroll-to-top' );

		if ( st_buttons.length ) {
			vamtam_greensock_wait( () => {
				var side_st_button = document.getElementById( 'scroll-to-top' );

				if ( side_st_button ) {
					v.addScrollHandler( {
						init: function() {},
						measure: function() {},
						mutate: function( cpos ) {
							if ( cpos > 0 ) {
								side_st_button.style.opacity   = 1;
								side_st_button.style.transform = 'scale3d( 1, 1, 1 )';
							} else {
								side_st_button.style.opacity   = '';
								side_st_button.style.transform = '';
							}
						}
					} );
				}

				document.addEventListener( 'click', ( e ) => {
					if ( e.target.classList.contains( 'vamtam-scroll-to-top' ) ) {
						e.preventDefault();

						vamtam_greensock_wait( () => {
							// iOS Safari uses a simple animation, normal browsers use scroll-behavior:smooth
							if ( /iPad|iPhone|iPod/.test( navigator.userAgent ) && ! window.MSStream ) {
								window.scrollTo( 0, 0 );
							} else {
								window.scroll( { left: 0, top: 0, behavior: 'smooth' } );
							}
						} );
					}
				}, true );
			} );
		}
	}
})( window.VAMTAM );
