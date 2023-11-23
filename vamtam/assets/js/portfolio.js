(function( $, v, undefined ) {
	'use strict';

	var Portfolio = function() {
		$( function() {
			this.init();
		}.bind( this ) );
	};

	Portfolio.prototype.init = function() {
		this.wrappers = $( '.portfolios' );

		this.wrappers.on( 'mouseenter', '.vamtam-project', this.mouseenter.bind( this ) );
		this.wrappers.on( 'mouseleave', '.vamtam-project', this.mouseleave.bind( this ) );
		this.wrappers.on( 'touchstart', '.vamtam-project', this.touchstart.bind( this ) );
		this.wrappers.on( 'touchmove', '.vamtam-project', this.touchmove.bind( this ) );
		this.wrappers.on( 'touchend', '.vamtam-project', this.touchend.bind( this ) );

		// close all open projects on touchstart anywhere outside a project
		document.body.addEventListener( 'touchstart', function( e ) {
			var closest = e.target.closest( '.vamtam-project' );
			var open    = document.querySelectorAll( '.vamtam-project.state-open' );

			for ( var i = 0; i < open.length; i++ ) {
				if ( open[i] !== closest ) {
					this.doClose( open[i] );
				}
			}
		}.bind( this ) );
	};

	Portfolio.prototype.mouseenter = function( e ) {
		this.doOpen( e.target.closest( '.vamtam-project' ) );
	};

	Portfolio.prototype.mouseleave = function( e ) {
		this.doClose( e.target.closest( '.vamtam-project' ) );
	};

	Portfolio.prototype.touchstart = function( e ) {
		var item = e.target.closest( '.vamtam-project' );

		if ( item.classList.contains( 'state-closed' ) ) {
			item.vamtamMaybeOpen = true;
		}
	};

	Portfolio.prototype.touchend = function( e ) {
		var item = e.target.closest( '.vamtam-project' );

		if ( item.vamtamMaybeOpen ) {
			item.vamtamMaybeOpen = false;

			this.doOpen( item );
			e.preventDefault();
		}
	};

	Portfolio.prototype.touchmove = function( e ) {
		e.target.closest( '.vamtam-project' ).vamtamMaybeOpen = false;
	};

	Portfolio.prototype.doOpen = function( el ) {
		if ( ! el.classList.contains( 'state-open' ) ) {
			requestAnimationFrame( function() {
				el.classList.add( 'state-open' );
				el.classList.remove( 'state-closed' );
			} );
		}
	};

	Portfolio.prototype.doClose = function( el ) {
		if ( ! el.classList.contains( 'state-closed' ) ) {
			requestAnimationFrame( function() {
				el.classList.add( 'state-closed' );
				el.classList.remove( 'state-open' );
			} );
		}
	};

	v.portfolio = new Portfolio();
})( jQuery, window.VAMTAM );
