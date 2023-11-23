(function( v, undefined ) {
	"use strict";

	document.addEventListener('DOMContentLoaded', function () {
		if ( VAMTAM_HIDDEN_WIDGETS !== undefined && VAMTAM_HIDDEN_WIDGETS.length > 0 && ! window.VAMTAM.MEDIA.fallback ) {
			var width = -1;

			window.addEventListener( 'resize', v.debounce( function() {
				requestAnimationFrame( function() {
					var winWidth = window.innerWidth;

					if ( width !== winWidth ) {
						width = winWidth;

						var widget;

						for ( var i = 0; i < VAMTAM_HIDDEN_WIDGETS.length; i++ ) {
							widget = document.getElementById( VAMTAM_HIDDEN_WIDGETS[i] );
							widget && widget.classList.toggle( 'hidden', v.MEDIA.layout["layout-below-max"] );
						}
					}
				} );
			}, 100 ), false );
		}
	} );
} )( window.VAMTAM );
