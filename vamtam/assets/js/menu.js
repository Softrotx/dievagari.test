(function($, v, undefined) {
	"use strict";

	window.FLBuilderLayout && Object.assign( window.FLBuilderLayout, {
		/**
		 * Monkey patches the built-in smooth scrolling (opt-in is better for load performance)
		 */
		_initAnchorLinks: function() {
		},
	} );

	var mainHeader      = $('header.main-header');
	var main            = $( '#main' );
	var body            = $( document.body );
	var header_contents = mainHeader.find( '.header-contents' );
	var menu_toggle     = document.getElementById( 'vamtam-megamenu-main-menu-toggle' );
	var original_toggle = document.querySelector( '#main-menu > .mega-menu-wrap > .mega-menu-toggle' );
	var main_menu       = document.querySelector( '#main-menu' );

	// main menu custom toggle

	if ( menu_toggle ) {
		menu_toggle.addEventListener( 'click', function( e ) {
			e.preventDefault();

			requestAnimationFrame( function() {
				var is_open = ( original_toggle || menu_toggle ).classList.contains( 'mega-menu-open' );

				menu_toggle.classList.toggle( 'mega-menu-open', ! is_open );

				( original_toggle || main_menu ).classList.toggle( 'mega-menu-open', ! is_open );
			} );
		} );
	}

	// overlay menu

	var overlay_menu        = document.getElementById( 'vamtam-overlay-menu' );
	var overlay_menu_toggle = document.querySelector( '.vamtam-overlay-menu-toggle' );

	var overlay_open = false;
	var toggle_clone;

	var toggle_overlay_menu = function( e ) {
		e.preventDefault();

		requestAnimationFrame( function() {
			overlay_open = ! overlay_open;

			if ( overlay_open ) {
				toggle_clone = overlay_menu_toggle.cloneNode( true );
				// measure

				var offset = overlay_menu_toggle.getBoundingClientRect();

				// mutate

				document.body.appendChild( toggle_clone );

				Object.assign( toggle_clone.style, {
					position: 'fixed',
					top: offset.top + 'px',
					left: offset.left + 'px',
				} );

				requestAnimationFrame( function() {
					overlay_menu.classList.add( 'open' );
					toggle_clone.classList.add( 'is-active' );
				} );
			} else {
				toggle_clone.classList.remove( 'is-active' );
				overlay_menu.classList.remove( 'open' );

				setTimeout( function() {
					requestAnimationFrame( function() {
						toggle_clone.remove();
					} );
				}, 650 );
			}
		} );
	};

	document.body.addEventListener( 'click', function( e ) {
		var button = e.target.closest( 'button' );
		if ( button && button.classList.contains( 'vamtam-overlay-menu-toggle' ) ) {
			toggle_overlay_menu( e );
		}
	} );

	// add left/right classes to submenus depending on resolution

	var allSubMenus = $( '#main-menu .sub-menu' );

	if ( allSubMenus.length ) {
		var invertPositionCallback = window.VAMTAM.debounce( function() {
			requestAnimationFrame( function() {
				var winWidth = window.innerWidth;

				allSubMenus.show().removeClass( 'invert-position' ).each( function() {
					if ( $( this ).offset().left + $( this ).width() > winWidth - 50 ) {
						$( this ).addClass( 'invert-position' );
					}
				} );

				allSubMenus.css( 'display', '' );
			} );
		}, 100 );

		invertPositionCallback();
		window.addEventListener( 'resize', invertPositionCallback, false );
	}

	// open submenus on click, only on mobile, when Max Mega Menu plugin is disabled
	if ( main_menu && main_menu.classList.contains('vamtam-basic-menu') ) {
		var allSubMenusResponsive = $(main_menu).find('.sub-menu');
		var allMenusResponsive = $(main_menu).find('.menu-item > a');

		allMenusResponsive.on( 'click', function( event ) {
			if (!main_menu.classList.contains('mega-menu-open')) {
				return;
			}

			var menuItem = this.parentElement;

			if ( this.classList.contains( 'menu-item-on' ) || ! menuItem.classList.contains( 'menu-item-has-children' ) ) {
				return;
			}

			event.preventDefault();

			var submenu = $( menuItem ).find( '.sub-menu' );

			allSubMenusResponsive.attr( 'style', '' );
			submenu.attr( 'style', 'display: block !important;' );
			allMenusResponsive.not( this ).removeClass( 'menu-item-on' );
			this.classList.add( 'menu-item-on' );
		});
	}

	// scrolling below

	var smoothScrollTimer, smoothScrollCallback;

	var smoothScrollListener = function() {
		clearTimeout( smoothScrollTimer );

		smoothScrollTimer = setTimeout( scrollToElComplete, 200 );
	};

	var scrollToElComplete = function() {
		window.removeEventListener( 'scroll', smoothScrollListener, { passive: true } );
		v.blockStickyHeaderAnimation = false;

		setTimeout( function() {
			requestAnimationFrame( function() {
				document.body.classList.remove( 'no-sticky-header-animation-tmp' );
			} );
		}, 50 );

		if ( smoothScrollCallback ) {
			smoothScrollCallback();
		}
	};

	var scrollToEl = function( el, duration, callback ) {
		requestAnimationFrame( function() {
			var el_offset = el.offset().top;

			v.blockStickyHeaderAnimation = true;

			// measure header height
			var header_height = 0;

			if ( mainHeader.hasClass( 'layout-standard' ) || mainHeader.hasClass( 'logo-text-menu' ) ) {
				if ( el_offset >= main.offset().top ) {
					header_height = mainHeader.find( '.second-row-columns' ).height();
				} else {
					header_height = mainHeader.height();
				}
			} else {
				if ( body.hasClass( 'no-sticky-header-animation' ) ) {
					// single line header with a special page template

					header_height = mainHeader.height();
				} else {
					header_height = header_contents.height();

					if ( 'stickyHeader' in v ) {
						v.stickyHeader.singleRowStick();
					}

					// in this case stick the header,
					// we'd like the menu to be visible after scrolling
					document.body.classList.add( 'no-sticky-header-animation-tmp' );
				}
			}

			if ( window.matchMedia( '(max-width: ' + VAMTAM_FRONT.mobile_header_breakpoint + ')' ).matches ) {
				header_height = mainHeader.height();
			}

			var scroll_position = el_offset - v.admin_bar_fix - header_height;

			smoothScrollCallback = callback;

			window.addEventListener( 'scroll', smoothScrollListener, { passive: true } );

			window.scroll( { left: 0, top: scroll_position, behavior: 'smooth' } );

			if ( el.attr( 'id' ) ) {
				if ( history.pushState ) {
					history.pushState( null, null, '#' + el.attr( 'id' ) );
				} else {
					window.location.hash = el.attr( 'id' );
				}
			}

			menu_toggle && menu_toggle.classList.remove( 'mega-menu-open' );
			original_toggle && original_toggle.classList.remove( 'mega-menu-open' );
		} );
	};

	window.FLBuilderLayout && Object.assign( window.FLBuilderLayout, {
		/**
		 * Monkey patches the built-in animated scroll with a better implementation
		 * which does not use jQuery
		 */
		_scrollToElement: function( el, callback ) {
			var config = window.FLBuilderLayoutConfig.anchorLinkAnimations;

			if ( el.length ) {
				menu_toggle.classList.remove( 'mega-menu-open' );
				original_toggle.classList.remove( 'mega-menu-open' );

				scrollToEl( el, config.duration / 1000, callback );
			}
		},
	} );

	$( document.body ).on('click', '.vamtam-animated-page-scroll[href], .vamtam-animated-page-scroll [href], .vamtam-animated-page-scroll [data-href], .mega-vamtam-animated-page-scroll[href], .mega-vamtam-animated-page-scroll [href], .mega-vamtam-animated-page-scroll [data-href]', function(e) {
		var href = $( this ).prop( 'href' ) || $( this ).data( 'href' );
		var el   = $( '#' + ( href ).split( "#" )[1] );

		var l  = document.createElement('a');
		l.href = href;

		if(el.length && l.pathname === window.location.pathname) {
			menu_toggle.classList.remove( 'mega-menu-open' );
			original_toggle.classList.remove( 'mega-menu-open' );

			scrollToEl( el );
			e.preventDefault();
		}
	});

	if ( window.location.hash !== "" &&
		(
			$( '.vamtam-animated-page-scroll[href*="' + window.location.hash + '"]' ).length ||
			$( '.vamtam-animated-page-scroll [href*="' + window.location.hash + '"]').length ||
			$( '.vamtam-animated-page-scroll [data-href*="'+window.location.hash+'"]' ).length ||
			$( '.mega-vamtam-animated-page-scroll[href*="' + window.location.hash + '"]' ).length ||
			$( '.mega-vamtam-animated-page-scroll [href*="' + window.location.hash + '"]').length ||
			$( '.mega-vamtam-animated-page-scroll [data-href*="'+window.location.hash+'"]' ).length
		)
	) {
		var el = $( window.location.hash );

		if ( el.length > 0 ) {
			$( window ).add( 'html, body, #page' ).scrollTop( 0 );
		}

		setTimeout( function() {
			scrollToEl( el );
		}, 400 );
	}

	// adds .current-menu-item classes

	var hashes = [
		// ['top', $('<div></div>'), $('#top')]
	];

	$('#main-menu').find('.mega-menu, .menu').find('.maybe-current-menu-item, .mega-current-menu-item, .current-menu-item').each(function() {
		var link = $('> a', this);

		if(link.prop('href').indexOf('#') > -1) {
			var link_hash = link.prop('href').split('#')[1];

			if('#'+link_hash !== window.location.hash) {
				$(this).removeClass('mega-current-menu-item current-menu-item');
			}

			hashes.push([link_hash, $(this), $('#'+link_hash)]);
		}
	});

	if ( hashes.length ) {
		var winHeight = 0;
		var documentHeight = 0;

		var prev_upmost_data = null;

		v.addScrollHandler( {
			init: function() {},
			add_current_menu_item: function( hash ) {
				// there may be more than one links with the same hash,
				// so we need to loop over all of the hashes

				for ( var i = 0; i < hashes.length; i++ ) {
					if ( hashes[i][0] === hash ) {
						hashes[i][1][0].classList.add( 'mega-current-menu-item' );
						hashes[i][1][0].classList.add( 'current-menu-item' );
					}
				}
			},
			measure: function( cpos ) {
				winHeight      = window.innerHeight;
				documentHeight = document.body.offsetHeight;

				this.upmost = Infinity;
				this.upmost_data = null;

				for ( var i = 0; i < hashes.length; i++ ) {
					var el = hashes[i][2];

					if ( el.length ) {
						var top = el.offset().top + 10;

						if (
							top > cpos &&
							top < this.upmost &&
							(
								top < cpos + winHeight / 2 ||
								( top < cpos + winHeight && cpos + winHeight === documentHeight )
							)
						) {
							this.upmost_data = hashes[i];
							this.upmost      = top;
						}
					}
				}
			},
			mutate: function( cpos ) {
				for ( var i = 0; i < hashes.length; i++ ) {
					if ( hashes[i][2].length ) {
						hashes[i][1][0].classList.remove( 'mega-current-menu-item' );
						hashes[i][1][0].classList.remove( 'current-menu-item' );
						hashes[i][1][0].childNodes[0].blur();
					}
				}

				if ( this.upmost_data ) {
					this.add_current_menu_item( this.upmost_data[0] );

					// attempt to push a state to the history if the current hash is different from the previous one
					if ( 'history' in window && ( prev_upmost_data !== null ? prev_upmost_data[0] : '' ) !== this.upmost_data[0] ) {
						window.history.pushState(
							this.upmost_data[0],
							$( '> a', this.upmost_data[1] ).text(),
							( cpos !== 0 ? '#' + this.upmost_data[0] : location.href.replace( location.hash, '' ) )
						);

						prev_upmost_data = $.extend({}, this.upmost_data);
					}
				} else if ( this.upmost_data === null && prev_upmost_data !== null ) {
					this.add_current_menu_item( prev_upmost_data[0] );
				}
			}
		} );
	}
})( jQuery, window.VAMTAM );
