/* jshint esversion: 6 */
(function( v, undefined ) {
	'use strict';

	var StickyHeader = function() {
		var self = this;

		if ( ! this.setupElements() ) {
			return false;
		}

		// rebuild on resize

		window.addEventListener( 'resize', window.VAMTAM.debounce( function() {
			requestAnimationFrame( function() {
				if ( self.active && ( self.winWidth && window.innerWidth !== self.winWidth ) ) {
					self.destroy();

					requestAnimationFrame( function() {
						self.init();
					} );
				}
			} );
		}, 100 ), false );

		// selective refresh support

		if ( 'undefined' !== typeof wp && wp.customize && wp.customize.selectiveRefresh ) {
			wp.customize.selectiveRefresh.bind( 'partial-content-rendered', function( placement ) {
				if ( placement.partial.id &&
					[ 'header-layout-selective', 'header-height' ].indexOf( placement.partial.id ) !== -1
				) {
					self.rebuild();
				}
			} );
		}
	};

	StickyHeader.prototype.rebuild = function() {
		var self = this;

		requestAnimationFrame( function() {
			self.destroy();

			setTimeout( function() {
				requestAnimationFrame( function() {
					self.setupElements();
					self.init();
				} );
			}, 100 );
		} );
	};

	StickyHeader.prototype.setupElements = function() {
		this.el = {
			hbox: document.getElementsByClassName( 'fixed-header-box' )[0],
			header: document.querySelector( 'header.main-header' ),
			main: document.getElementById( 'main' ),
			topNav: document.getElementsByClassName( 'top-nav' )[0]
		};

		if ( ! this.el.hbox || ! this.el.header || ! this.el.main ) {
			return false;
		}

		this.prevPos = 0;

		this.el.logoWrapper = this.el.hbox.getElementsByClassName( 'logo-wrapper' )[0];

		this.layout = this.el.header.classList.contains( 'layout-logo-menu' ) ? 'single' :
		                ( this.el.header.classList.contains( 'layout-overlay-menu' ) ? 'overlay' : 'double' );

		this.typeOver  = document.body.classList.contains( 'sticky-header-type-over' );

		return true;
	};

	StickyHeader.prototype.canActivate = function() {
		return ! this.active &&
			( document.body.classList.contains( 'sticky-header' ) || document.body.classList.contains( 'had-sticky-header' ) ) &&
			! document.body.classList.contains( 'fl-builder-active' ) &&
			! VAMTAM_FRONT.is_mobile &&
			! window.matchMedia( '(max-width: ' + VAMTAM_FRONT.mobile_header_breakpoint + ')' ).matches &&
			! document.body.classList.contains( 'sticky-header-type-below' ) &&
			this.el.hbox &&
			! v.MEDIA.fallback;
	};

	StickyHeader.prototype.init = function() {
		// if we can't activate the sticky header - reset it so that it doesn't overlap anything
		if ( ! this.canActivate() ) {
			if ( document.body.classList.contains( 'sticky-header' ) ) {
				document.body.classList.remove( 'sticky-header' );
				document.body.classList.add( 'had-sticky-header' );

				if ( this.el.hbox && this.el.hbox.style.height === '0px' ) {
					this.el.hbox.style.height = 'auto';
				}
			}

			return;
		}

		// measure

		this.winWidth = window.innerWidth;

		this.hboxWidth         = this.el.hbox.offsetWidth;
		this.hboxHeight        = this.el.hbox.offsetHeight;
		this.topNavHeight      = this.el.topNav ? this.el.topNav.offsetHeight : 0;
		this.logoWrapperHeight = this.el.logoWrapper.offsetHeight;

		this.hboxOffset = v.offset( this.el.hbox );

		this.hboxLeft = this.hboxOffset.left;

		var fillerHeight = this.typeOver ? this.topNavHeight : this.hboxHeight;

		// mutate

		this.el.hboxFiller = this.el.hbox.cloneNode( false );
		Object.assign( this.el.hboxFiller.style, {
			height: fillerHeight + 'px',
			zIndex: 1,
		} );
		this.el.hboxFiller.classList.add( 'hbox-filler' );
		this.el.hbox.after( this.el.hboxFiller );

		// this.hbox loads with height:0px, so we must first reset the height and then measure it again on the next frame
		Object.assign( this.el.hbox.style, {
			position: this.layout === 'overlay' ? 'fixed' : 'absolute',
			top: v.admin_bar_fix + 'px',
			left: 0,
			width: this.hboxWidth + 'px',
			height: '',
			'will-change': 'transform',
			transform: 'translateX(' + this.hboxLeft + 'px)',
		} );

		requestAnimationFrame( () => {
			this.hboxHeight      = this.el.hbox.offsetHeight;
			this.resetBottomEdge = Math.max( this.hboxHeight, this.el.header.offsetHeight ) + this.hboxOffset.top;
			this.active          = true;
		} );

		if ( this.el.topNav ) {
			var topNavFiller = document.createElement( 'div' );

			topNavFiller.id           = 'top-nav-wrapper-filler';
			topNavFiller.style.height = this.topNavHeight + 'px';

			this.el.hboxFiller.appendChild( topNavFiller );
		}
	};

	StickyHeader.prototype.destroy = function() {
		if ( ! this.active ) {
			return;
		}

		if ( this.el.hboxFiller ) {
			this.el.hboxFiller.remove();
		}

		Object.assign( this.el.hbox.style, {
			position: '',
			top: '',
			left: '',
			width: '',
			'will-change': '',
			transform: '',
		} );

		// we don't need to add the classes if the sticky header was disabled in the Customizer
		if ( document.body.classList.contains( 'had-sticky-header' ) ) {
			document.body.classList.add( 'sticky-header' );
			document.body.classList.remove( 'had-sticky-header' );
		}

		this.active = false;
	};

	StickyHeader.prototype.measure = function( cpos ) {
		if ( ! this.active ) {
			return;
		}

		if ( this.layout !== 'overlay' ) {
			if ( ! ( 'blockStickyHeaderAnimation' in v ) || ! v.blockStickyHeaderAnimation ) {
				this.direction = this.prevPos === cpos ? '-' : ( this.prevPos < cpos ? 'down' : 'up' );

				if ( this.direction === 'up' && this.startScrollingUp === undefined ) {
					// first up
					this.startScrollingUp = cpos;
				} else if ( this.direction === 'down' ) {
					// any down - reset up mark
					this.startScrollingUp = undefined;
				}

				if ( this.direction === 'down' && this.startScrollingDown === undefined ) {
					// first down
					this.startScrollingDown = cpos;
				} else if ( this.direction === 'up' ) {
					// any up - reset down mark
					this.startScrollingDown = undefined;
				}

				this.prevPos = cpos;
			}
		} else {
		}
	};

	StickyHeader.prototype.mutate = function( cpos ) {
		if ( ! this.active ) {
			return;
		}

		if ( this.layout !== 'overlay' ) {
			if ( ! document.body.classList.contains( 'no-sticky-header-animation' ) && ! document.body.classList.contains( 'no-sticky-header-animation-tmp' ) ) {
				if ( cpos < this.resetBottomEdge + 200 ) {
					// at the top

					this.singleRowReset( 'fast' );
				} else if ( this.direction === 'down' && ( cpos - this.startScrollingDown > 30 || cpos < this.resetBottomEdge * 2 ) ) {
					// reset header position to absolute scrolling down

					this.singleRowReset( 'slow' );
				} else if ( this.direction === 'up' && ( this.startScrollingUp - cpos > 30 || cpos < this.resetBottomEdge * 2 ) ) {
					// scrolling up - show header

					this.singleRowStick();
				}
			} else if ( document.body.classList.contains( 'no-sticky-header-animation' ) ) {
				// the header should always be in its "scrolled up" state
				Object.assign( this.el.hbox.style, {
					position: 'fixed',
					top: v.admin_bar_fix + 'px',
					transform: '',
				} );

				this.el.hbox.classList.toggle( 'sticky-header-state-stuck', cpos > 0 );
				this.el.hbox.classList.toggle( 'sticky-header-state-reset', cpos <= 0 );
			}
		} else {
			this.el.hbox.classList.toggle( 'sticky-header-state-stuck', cpos > 0 );
			this.el.hbox.classList.toggle( 'sticky-header-state-reset', cpos <= 0 );
		}

		document.body.classList.toggle( 'vamtam-scrolled', cpos > 0 );
	};

	StickyHeader.prototype.singleRowReset = function( speed ) {
		speed = speed || 'fast';

		if ( ! this.active || this.el.hbox.classList.contains( 'sticky-header-state-reset' ) || this.singleRowResetStarted ) {
			return;
		}

		this.singleRowResetStarted = true;

		var true_reset = function() {
			vamtamgs.TweenLite.set( this.el.hbox, {
				position: 'absolute',
				y: 0,
				x: this.hboxLeft
			} );

			this.el.hbox.classList.add( 'sticky-header-state-reset' );
			this.el.hbox.classList.remove( 'sticky-header-state-stuck' );

			this.singleRowResetStarted = false;
		}.bind( this );

		window.vamtam_greensock_wait( function() {
			vamtamgs.TweenLite.killTweensOf( this.el.hbox );

			if ( speed === 'fast' ) {
				true_reset();
			} else if ( speed === 'slow' ) {
				vamtamgs.TweenLite.to( this.el.hbox, 0.15, {
					y: - this.hboxHeight,
					ease: vamtamgs.Power4.easeOut,
					onComplete: true_reset
				} );
			}
		}.bind( this ) );
	};

	StickyHeader.prototype.singleRowStick = function() {
		// it is possible that singleRowStick may be called during a "reset"
		// make sure to only stick the header if this.singleRowResetStarted === false
		if ( ! this.active || this.el.hbox.classList.contains( 'sticky-header-state-stuck' ) || this.singleRowResetStarted ) {
			return;
		}

		this.el.hbox.classList.add( 'sticky-header-state-stuck' );
		this.el.hbox.classList.remove( 'sticky-header-state-reset' );

		window.vamtam_greensock_wait( function() {
			vamtamgs.TweenLite.killTweensOf( this.el.hbox );

			vamtamgs.TweenLite.fromTo( this.el.hbox, 0.2, {
				position: 'fixed',
				top: v.admin_bar_fix,
				y: - this.hboxHeight,
				x: this.hboxOffset.left
			}, {
				y: - this.topNavHeight - ( this.layout === 'double' ? this.logoWrapperHeight : 0 ),
				ease: vamtamgs.Power4.easeOut
			} );
		}.bind( this ) );
	};

	document.addEventListener( 'DOMContentLoaded', function() {
		v.stickyHeader = new StickyHeader();

		vamtam_greensock_wait( function() {
			v.addScrollHandler( v.stickyHeader );
		} );
	} );
})( window.VAMTAM );
