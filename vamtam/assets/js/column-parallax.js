( function( v, undefined ) {
	'use strict';

	var initialized = false;

	v.parallaxBackground = {
		/**
		 * Loop through all rows with a parallax background,
		 * load the image asynchronously,
		 * and create the necessary elements
		 *
		 * Also bind resize/load events here
		 */
		init: function() {
			this.rows = document.querySelectorAll( '.fl-row-bg-parallax' );

			for ( var i = 0; i < this.rows.length; i++ ) {
				var row = this.rows[ i ];
				var src = row.getAttribute( 'data-parallax-image' );

				if ( ! row.vamtamParallaxLoaded && src ) {
					var img = new Image();

					img.addEventListener( 'load', this.loadImageCallback );

					img.row = row;
					img.src = src;
				}
			}

			window.addEventListener( 'resize', window.VAMTAM.debounce( this.onresize, 100 ).bind( this ), false );
			window.addEventListener( 'load', window.VAMTAM.debounce( this.onresize, 100 ).bind( this ), false );
			this.onresize();
		},

		/**
		 * Fired when the background image is loaded,
		 * this creates the element holding the background
		 */
		loadImageCallback: function( e ) {
			var row         = e.target.row;
			var contentWrap = row.firstElementChild;

			var imageHolder = document.createElement( 'div' );

			imageHolder.classList.add( 'vamtam-parallax-bg' );

			Object.assign( imageHolder.style, {
				backgroundImage: 'url(' + e.target.src + ')',
				backgroundSize: row.dataset.backgroundSize,
				backgroundRepeat: 'no-repeat',
				backgroundPosition: row.dataset.backgroundPosition,
				position: 'absolute',
				top: '-300px',
				right: 0,
				bottom: '-300px',
				left: 0,
				'will-change': 'transform',
			} );

			requestAnimationFrame( function() {
				row.vamtamParallaxLoaded = true;

				var content = contentWrap.querySelector( '.fl-node-content' );

				content.before( imageHolder );

				contentWrap.style.overflow        = 'hidden';
				contentWrap.style.backgroundImage = 'none';

				content.style.zIndex       = 1;
				content.style.position     = 'relative';
			} );
		},

		/**
		 * Measure and store the offset for each row
		 * This only needs to happen on resize/page load
		 */
		onresize: function() {
			requestAnimationFrame( function() {
				var cpos = window.pageYOffset;

				for ( var i = 0; i < this.rows.length; i++ ) {
					this.rows[ i ].vamtamParallaxOffset = v.offset( this.rows[ i ].firstElementChild );
				}

				this.measure( cpos );
				this.mutate( cpos );
			}.bind( this ) );
		},

		measure: function() {
		},

		/**
		 * Reposition the background elements.
		 */
		mutate: function( cpos ) {
			for ( var i = 0; i < this.rows.length; i++ ) {
				if ( this.rows[ i ].vamtamParallaxLoaded ) {
					var speed = this.rows[ i ].getAttribute( 'data-parallax-speed' );
					var pos   = - ( ( cpos - this.rows[ i ].vamtamParallaxOffset.top ) / speed );

					this.rows[ i ].firstElementChild.firstElementChild.style.transform = 'translateY(' + pos + 'px)';
				}
			}
		},
	};

	window.FLBuilderLayout && Object.assign( window.FLBuilderLayout, {
		/**
		 * Monkey patches the built-in parallax with a better implementation
		 */
		_initParallaxBackgrounds: function() {
			if ( ! initialized ) {
				initialized = true;

				// parallax should only be enabled if Beaver Builder is not active,
				// that is, only on pages which are not currently being edited
				if ( ! document.body.classList.contains( 'fl-builder-active' ) && ! ( window.matchMedia('(prefers-reduced-motion: reduce)').matches ) ) {
					v.addScrollHandler( v.parallaxBackground );
				} else {
					var rows = document.querySelectorAll( '.fl-row-bg-parallax[data-parallax-image]' );

					for ( var i = 0; i < rows.length; i++ ) {
						var row = rows[ i ];
						var src = row.getAttribute( 'data-parallax-image' );

						Object.assign( row.style, {
							backgroundImage: 'url(' + src + ')',
							backgroundSize: 'cover',
							backgroundRepeat: 'repeat',
						} );
					}
				}
			}
		},

		_scrollParallaxBackgrounds: function() {
			// should only be called once after we remove the event listener
			jQuery( window ).off( 'scroll.fl-bg-parallax' );
		},
	} );

	var previewCallback = function() {
		initialized = false;

		window.FLBuilderLayout._initParallaxBackgrounds();
	};

	window.FLBuilderLayout && document.addEventListener( 'DOMContentLoaded', function() {
		if ( document.body.classList.contains( 'fl-builder-active' ) ) {
			FLBuilder.addHook( 'didCompleteAJAX', previewCallback );
			FLBuilder.addHook( 'didRenderLayoutComplete', previewCallback );

			jQuery( FLBuilder._contentClass ).on( 'fl-builder.preview-rendered', previewCallback );
		}

		// force initialization of Beaver has disabled the parallax
		window.FLBuilderLayout._initParallaxBackgrounds();
	} );

} )( window.VAMTAM );
