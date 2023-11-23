( function( $, v, undefined ) {
	'use strict';


	$(function() {
		var cube_found = 'cubeportfolio' in $.fn;
		var cube_loading = false;

		var win = $(window);

		var cube_narrow = function( el ) {
			requestAnimationFrame( function() {
				var inner = el.find( '.cbp-wrapper' );
				var outer = el.find( '.cbp-wrapper-outer' );

				if ( inner.width() <= outer.width() ) {
					el.addClass( 'vamtam-cube-narrow' );
				} else {
					el.removeClass( 'vamtam-cube-narrow' );
				}
			} );
		};

		var attempt_cube_load_callback = function() {
			$( '.vamtam-cubeportfolio[data-options]:not(.vamtam-cube-loaded)' ).filter( ':visible' ).each( function() {
				var self    = $( this );
				var options = self.data( 'options' );

				if ( ! ( 'singlePageCallback' in options ) ) {
					options.singlePageDelegate = null;
				}

				options.singlePageCallback = cube_single_page[ options.singlePageCallback ] || null;

				self.on( 'initComplete.cbp', function() {
					if ( 'slider' === options.layoutMode ) {
						cube_narrow( self );

						win.on( 'resize.vamtamcube', function() {
							cube_narrow( self );
						} );
					}
				} );

				self.addClass( 'vamtam-cube-loaded' ).cubeportfolio( options );

				self.on( 'vamtam-video-resized', 'iframe, object, embed, video', function() {
					self.data('cubeportfolio').layoutAndAdjustment();
				} );

				this.addEventListener( 'vamtamlazyloaded', function() {
					self.data('cubeportfolio').layoutAndAdjustment();
				} );
			} );
		};

		// if there are cube instances, check that the script is loaded
		// otherwise - load it and prevent further calls to attempt_cube_load
		// until cube is available
		var attempt_cube_load = function() {
			if ( document.getElementsByClassName( 'vamtam-cubeportfolio' ).length ) {
				if ( cube_found ) {
					attempt_cube_load_callback();
				} else if ( ! cube_loading ) {
					cube_loading = true;

					// load css and js in parallel, init cube when both have finished loading

					var loaded_assets = 0;

					var check_loaded = function() {
						if ( ++loaded_assets === 2 ) {
							cube_found = 'cubeportfolio' in $.fn;

							attempt_cube_load_callback();
						}
					};

					v.load_style( VAMTAM_FRONT.cube_path + 'css/cubeportfolio.min.css', 'all', check_loaded, document.getElementById( 'vamtam-front-all-css' ) );
					v.load_script( VAMTAM_FRONT.cube_path + 'js/jquery.cubeportfolio.min.js', check_loaded );
				}
			}
		};

		var cube_single_page = {
			portfolio: function( url ) {
				var t = this;

				$.ajax({
					url: url,
					type: 'GET',
					dataType: 'html'
				})
				.done(function(result) {
					t.updateSinglePage(result);

					attempt_cube_load();

					$( document ).trigger( 'vamtam-single-page-project-loaded' );
				})
				.fail(function() {
					t.updateSinglePage('AJAX Error! Please refresh the page!');
				});
			}
		};

		$( document ).on( 'vamtam-attempt-cube-load', attempt_cube_load );
		attempt_cube_load();

		window.addEventListener( 'resize', window.VAMTAM.debounce( attempt_cube_load, 100 ), false );

		const resizeAll = function() {
			$( '.cbp' ).each( function() {
				try {
					$(this).data( 'cubeportfolio' ).layoutAndAdjustment();
				} catch ( e ) {}
			} );
		};

		window.addEventListener( 'load', function() {
			resizeAll();
			setTimeout( resizeAll, 200 );
			setTimeout( resizeAll, 500 );
			setTimeout( resizeAll, 1000 );
		}, false );

		resizeAll();
	});

	function attemptCubeLoad() {
		$( document ).trigger( 'vamtam-attempt-cube-load' );
	}

	document.addEventListener( 'DOMContentLoaded', function() {
		if ( window.FLBuilder ) {
			FLBuilder.addHook( 'didCompleteAJAX', attemptCubeLoad );
			FLBuilder.addHook( 'didRenderLayoutComplete', attemptCubeLoad );
		}
		attemptCubeLoad();
	} );
} )( jQuery, window.VAMTAM );
