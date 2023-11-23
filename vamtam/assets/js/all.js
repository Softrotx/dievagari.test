/**
 * Often used vanilla js functions, so that we don't need
 * to use all of underscore/jQuery
 */
(function( undefined ) {
	"use strict";

	var v = ( window.VAMTAM = window.VAMTAM || {} ); // Namespace

	// Returns a function, that, as long as it continues to be invoked, will not
	// be triggered. The function will be called after it stops being called for
	// N milliseconds. If `immediate` is passed, trigger the function on the
	// leading edge, instead of the trailing.
	v.debounce = function( func, wait, immediate ) {
		var timeout;
		return function() {
			var context = this, args = arguments;
			var later = function() {
				timeout = null;
				if ( ! immediate ) func.apply( context, args );
			};
			var callNow = immediate && ! timeout;
			clearTimeout( timeout );
			timeout = setTimeout( later, wait );
			if ( callNow ) func.apply( context, args );
		};
	};

	if ( 'jQuery' in window && ! ( 'debounce' in window.jQuery ) ) {
		window.jQuery.debounce = function( delay, at_begin, callback ) {
			return callback === undefined ?
				v.debounce( at_begin, delay, false )
				: v.debounce( callback, delay, at_begin !== false );
		};
	}


	// vanilla jQuery.fn.offset() replacement
	// @see https://plainjs.com/javascript/styles/get-the-position-of-an-element-relative-to-the-document-24/

	v.offset = function( el ) {
		var rect = el.getBoundingClientRect(),
		scrollLeft = window.pageXOffset || document.documentElement.scrollLeft,
		scrollTop = window.pageYOffset || document.documentElement.scrollTop;
		return { top: rect.top + scrollTop, left: rect.left + scrollLeft };
	};

	// Faster scroll-based animations

	v.scroll_handlers = [];
	v.latestKnownScrollY = 0;

	var ticking = false;

	v.addScrollHandler = function( handler ) {
		requestAnimationFrame( function() {
			handler.init();
			v.scroll_handlers.push( handler );

			handler.measure( v.latestKnownScrollY );
			handler.mutate( v.latestKnownScrollY );
		} );
	};

	v.onScroll = function() {
		v.latestKnownScrollY = window.pageYOffset;

		if ( ! ticking ) {
			ticking = true;

			requestAnimationFrame( function() {
				var i;

				for ( i = 0; i < v.scroll_handlers.length; i++ ) {
					v.scroll_handlers[i].measure( v.latestKnownScrollY );
				}

				for ( i = 0; i < v.scroll_handlers.length; i++ ) {
					v.scroll_handlers[i].mutate( v.latestKnownScrollY );
				}

				ticking = false;
			} );
		}
	};

	window.addEventListener( 'scroll', v.onScroll, { passive: true } );

	// Load an async script
	v.load_script = function( src, callback ) {
		var s = document.createElement('script');
		s.type = 'text/javascript';
		s.async = true;
		s.src = src;

		if ( callback ) {
			s.onload = callback;
		}

		document.getElementsByTagName('script')[0].before( s );
	};

	v.load_style = function( href, media, callback, after ) {
		var l = document.createElement('link');
		l.rel = 'stylesheet';
		l.type = 'text/css';
		l.media = media;
		l.href = href;

		if ( callback ) {
			l.onload = callback;
		}

		if ( after ) {
			after.after( l );
		} else {
			document.getElementsByTagName('link')[0].before( l );
		}
	};
})();
(function(v, undefined) {
	"use strict";

	var queue = [];

	function process_queue() {
		for ( var i = 0; i < queue.length; i++ ) {
			queue[i].call( window );
		}
	}

	document.addEventListener( 'DOMContentLoaded', function() {
		var scripts = [];

		if ( 'punchgs' in window ) {
			window.vamtamgs = window.GreenSockGlobals = window.punchgs;
			window._gsQueue = window._gsDefine = null;
		} else {
			window.vamtamgs = window.GreenSockGlobals = {};
			window._gsQueue = window._gsDefine = null;

			scripts.push(
				window.VAMTAM_FRONT.jspath + 'plugins/thirdparty/gsap/TweenLite.min.js',
				window.VAMTAM_FRONT.jspath + 'plugins/thirdparty/gsap/TimelineLite.min.js',
				window.VAMTAM_FRONT.jspath + 'plugins/thirdparty/gsap/plugins/CSSPlugin.min.js'
			);
		}

		window.vamtam_greensock_loaded = false;

		if ( ! ( 'scroll-behavior' in document.documentElement.style ) ) {
			scripts.push( window.VAMTAM_FRONT.jspath + 'plugins/thirdparty/smoothscroll.js' );
		}

		var total_ready = 0;
		var maybe_ready = function() {
			if ( ++ total_ready >= scripts.length ) {
				window.GreenSockGlobals = window._gsQueue = window._gsDefine = null;

				window.vamtam_greensock_loaded = true;

				process_queue();
			}
		};

		if ( scripts.length > 0 ) {
			for ( var i = 0; i < scripts.length; i++ ) {
				v.load_script( scripts[i], maybe_ready );
			}
		} else {
			maybe_ready();
		}
	});

	window.vamtam_greensock_wait = function( callback ) {
		var callback_wrapper = function() {
			requestAnimationFrame( callback );
		};

		if ( window.vamtam_greensock_loaded ) {
			callback_wrapper();
		} else {
			queue.push( callback_wrapper );
		}
	};
} )( window.VAMTAM );
(function() {
	'use strict';

	// ChildNode (MDN)

	var buildDOM = function() {
		var nodes = Array.prototype.slice.call(arguments),
			frag = document.createDocumentFragment(),
			div, node;

		while (node = nodes.shift()) {
			if (typeof node == "string") {
				div = document.createElement("div");
				div.innerHTML = node;
				while (div.firstChild) {
					frag.appendChild(div.firstChild);
				}
			} else {
				frag.appendChild(node);
			}
		}

		return frag;
	};

	var proto = {
		before: function() {
			var frag = buildDOM.apply(this, arguments);
			this.parentNode.insertBefore(frag, this);
		},
		after: function() {
			var frag = buildDOM.apply(this, arguments);
			this.parentNode.insertBefore(frag, this.nextSibling);
		},
		replaceWith: function() {
			if (this.parentNode) {
				var frag = buildDOM.apply(this, arguments);
				this.parentNode.replaceChild(frag, this);
			}
		},
		remove: function() {
			if (this.parentNode) {
				this.parentNode.removeChild(this);
			}
		}
	};

	var a = ["Element", "DocumentType", "CharacterData"]; // interface
	var b = ["before", "after", "replaceWith", "remove"]; // methods
	a.forEach(function(v) {
		b.forEach(function(func) {
			if (window[v]) {
				if (window[v].prototype[func]) { return; }
				window[v].prototype[func] = proto[func];
			}
		});
	});

	// ParentNode.prepend()
	// Source: https://github.com/jserz/js_piece/blob/master/DOM/ParentNode/prepend()/prepend().md

	(function(arr) {
		arr.forEach(function(item) {
			if (item.hasOwnProperty('prepend')) {
				return;
			}
			Object.defineProperty(item, 'prepend', {
				configurable: true,
				enumerable: true,
				writable: true,
				value: function prepend() {
					var argArr = Array.prototype.slice.call(arguments),
						docFrag = document.createDocumentFragment();

					argArr.forEach(function(argItem) {
						var isNode = argItem instanceof Node;
						docFrag.appendChild(isNode ? argItem : document.createTextNode(String(argItem)));
					});

					this.insertBefore(docFrag, this.firstChild);
				}
			});
		});
	})([Element.prototype, Document.prototype, DocumentFragment.prototype]);

	// Object.assign() (MDN)

	if (typeof Object.assign != 'function') {
	  (function () {
		Object.assign = function (target) {
		  // We must check against these specific cases.
		  if (target === undefined || target === null) {
			throw new TypeError('Cannot convert undefined or null to object');
		  }

		  var output = Object(target);
		  for (var index = 1; index < arguments.length; index++) {
			var source = arguments[index];
			if (source !== undefined && source !== null) {
			  for (var nextKey in source) {
				if (source.hasOwnProperty(nextKey)) {
				  output[nextKey] = source[nextKey];
				}
			  }
			}
		  }
		  return output;
		};
	  })();
	}

	// Element.prototype.matches (https://plainjs.com/javascript/traversing/get-closest-element-by-selector-39/)
	window.Element && function(ElementPrototype) {
		ElementPrototype.matches = ElementPrototype.matches ||
		ElementPrototype.matchesSelector ||
		ElementPrototype.webkitMatchesSelector ||
		ElementPrototype.msMatchesSelector ||
		function(selector) {
			var node = this, nodes = (node.parentNode || node.document).querySelectorAll(selector), i = -1;
			while (nodes[++i] && nodes[i] != node);
			return !!nodes[i];
		};
	}(Element.prototype);

	// Element.prototype.closest (https://plainjs.com/javascript/traversing/get-closest-element-by-selector-39/)
	window.Element && function(ElementPrototype) {
		ElementPrototype.closest = ElementPrototype.closest ||
		function(selector) {
			var el = this;
			while (el.matches && !el.matches(selector)) el = el.parentNode;
			return el.matches ? el : null;
		};
	}(Element.prototype);
}());

(function(undefined) {
	'use strict';

	// Namespace
	window.VAMTAM = window.VAMTAM || {};

	window.VAMTAM.MEDIA = window.VAMTAM.MEDIA || {
		layout: {},
		fallback: ! ( window.CSS && window.CSS.supports && window.CSS.supports( '(--foo: red)' ) ),
	};

	var LAYOUT_SIZES = [{
			min: 0,
			max: window.VAMTAM_FRONT.beaver_small,
			className: 'layout-small'
		}, {
			min: window.VAMTAM_FRONT.beaver_responsive + 1,
			max: Infinity,
			className: 'layout-max'
		}, {
			min: window.VAMTAM_FRONT.beaver_responsive + 1,
			max: window.VAMTAM_FRONT.content_width,
			className: 'layout-max-low'
		}, {
			min: 0,
			max: window.VAMTAM_FRONT.beaver_responsive,
			className: 'layout-below-max'
		} ];

	var sizesLength = LAYOUT_SIZES.length;

	var remap = function() {
		var map   = {};

		for ( var i = 0; i < sizesLength; i++ ) {
			var mq = '(min-width: '+LAYOUT_SIZES[i].min+'px)';

			if ( LAYOUT_SIZES[i].max !== Infinity ) {
				mq += ' and (max-width: '+LAYOUT_SIZES[i].max+'px)';
			}

			if ( window.matchMedia(mq).matches ) {
				map[LAYOUT_SIZES[i].className] = true;
			}
			else {
				map[LAYOUT_SIZES[i].className] = false;
			}
		}

		window.VAMTAM.MEDIA.layout = map;
	};

	var debouncedRemap = window.VAMTAM.debounce( remap, 100 );

	remap();

	document.addEventListener('DOMContentLoaded', function () {
		if ( document.body.classList.contains( 'responsive-layout' ) && 'matchMedia' in window ) {
			window.addEventListener( 'resize', debouncedRemap, false );
			window.addEventListener( 'load', debouncedRemap, false );

			remap();
		} else {
			window.VAMTAM.MEDIA.layout = { 'layout-max': true, 'layout-below-max': false };
		}
	} );
})();

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

( function( v, undefined ) {
	'use strict';

	// this one can be initialized late (on load) instead of on DOMContentLoaded
	window.addEventListener( 'load', function() {
		var columns = document.querySelectorAll( '[data-progressive-animation]' );

		if ( columns.length && ! document.body.classList.contains( 'fl-builder-active' ) && ! ( window.matchMedia('(prefers-reduced-motion: reduce)').matches ) ) {
			vamtam_greensock_wait( function() {
				v.addScrollHandler( {
					defaultOptions: {
						origin: 'center center',
						type: 'progressive',
						exit: true,
						delay: 0,
						mobile: false,
						pin: false,
						pinUnit: 'px',
						pinTrigger: 'center',
					},
					blockAnimations: false,
					canActivate: function( mobile ) {
						return mobile || ! v.MEDIA.layout[ 'layout-below-max' ];
					},
					buildTimeline: function( target, withExit ) {
						var timeline = new vamtamgs.TimelineLite( { paused: true } );
						var type     = target.getAttribute( 'data-progressive-animation' );

						if ( type === 'dummy' ) {
							timeline.fromTo( target, 1, { opacity: 1 }, {opacity: 1 }, '0' );

							withExit && timeline.to( target, 1, { opacity: 1 }, '1' );

						} else if ( type === 'rotate' ) {
							timeline.fromTo( target, 1, { rotation: -180 }, { rotation: 0 }, '0' );

							withExit && timeline.to( target, 1, { rotation: 180 }, '1' );

						} else if ( type === 'fade' ) {
							timeline.fromTo( target, 1, {
								opacity: 0,
							}, {
								opacity: 1,
							}, '0' );

							withExit && timeline.to( target, 0.4, { opacity: 0 }, '1.6' );
							withExit && timeline.to( target, 1, { y: -100 }, '1.6' );

						//  Move + Fade  //

						} else if ( type === 'move-from-top' ) {
							timeline.fromTo( target, 1, {
								y: -160,
								opacity: 0,
							}, {
								y: 0,
								opacity: 1,
							}, '0' );

							withExit && timeline.to( target, 0.4, { opacity: 0 }, '1.6' );
							withExit && timeline.to( target, 1, { y: -100 }, '1.6' );

						} else if ( type === 'move-from-bottom' ) {
							timeline.fromTo( target, 1, {
								y: 100,
								opacity: 0,
							}, {
								y: 0,
								opacity: 1,
							}, '0' );

							withExit && timeline.to( target, 0.4, { opacity: 0 }, '1.6' );
							withExit && timeline.to( target, 1, { y: -50 }, '1.6' );


						} else if ( type === 'move-from-left' ) {
							timeline.fromTo( target, 1, {
								x: -160,
								opacity: 0,
							}, {
								x: 0,
								opacity: 1,
							}, '0' );

							withExit && timeline.to( target, 0.4, { opacity: 0 }, '1.6' );
							withExit && timeline.to( target, 1, { x: -100 }, '1.6' );


						} else if ( type === 'move-from-right' ) {
							timeline.fromTo( target, 1, {
								x: 160,
								opacity: 0,
							}, {
								x: 0,
								opacity: 1,
							}, '0' );

							withExit && timeline.to( target, 0.4, { opacity: 0 }, '1.6' );
							withExit && timeline.to( target, 1, { x: 100 }, '1.6' );

						//  Scale //

						} else if ( type === 'scale-in' ) {
							timeline.fromTo( target, 1, {
								opacity: 0,
								scaleX: 0.0,
								scaleY: 0.0,
							}, {
								opacity: 1,
								scaleX: 1,
								scaleY: 1,
							}, '0' );

							withExit && timeline.to( target, 0.4, { opacity: 0 }, '1.6' );

						} else if ( type === 'scale-out' ) {
							timeline.fromTo( target, 1, {
								opacity: 0,
								scaleX: 2,
								scaleY: 2,
							}, {
								opacity: 1,
								scaleX: 1,
								scaleY: 1,
							}, '0' );

							withExit && timeline.to( target, 0.4, { opacity: 0 }, '1.6' );


						//  Move + Scale //

						//  Zoom In //

						} else if ( type === 'move-scale-in-from-top' ) {
							timeline.fromTo( target, 1, {
								y: -160,
								opacity: 0,
								scaleX: 0.6,
								scaleY: 0.6,
							}, {
								y: 0,
								opacity: 1,
								scaleX: 1,
								scaleY: 1,
							}, '0' );

							withExit && timeline.to( target, 0.4, { opacity: 0 }, '1.6' );
							withExit && timeline.to( target, 1, { y: -100 }, '1.6' );

						} else if ( type === 'move-scale-in-from-bottom' ) {
							timeline.fromTo( target, 1, {
								y: 160,
								opacity: 0,
								scaleX: 0.6,
								scaleY: 0.6,
							}, {
								y: 0,
								opacity: 1,
								scaleX: 1,
								scaleY: 1,
							}, '0' );

							withExit && timeline.to( target, 0.4, { opacity: 0 }, '1.6' );
							withExit && timeline.to( target, 1, { y: -100 }, '1.6' );

						} else if ( type === 'move-scale-in-from-left' ) {
							timeline.fromTo( target, 1, {
								x: -160,
								opacity: 0,
								scaleX: 0.6,
								scaleY: 0.6,
							}, {
								x: 0,
								opacity: 1,
								scaleX: 1,
								scaleY: 1,
							}, '0' );

							withExit && timeline.to( target, 0.4, { opacity: 0 }, '1.6' );
							withExit && timeline.to( target, 1, { x: -100 }, '1.6' );

						} else if ( type === 'move-scale-in-from-right' ) {
							timeline.fromTo( target, 1, {
								x: 160,
								opacity: 0,
								scaleX: 0.6,
								scaleY: 0.6,
							}, {
								x: 0,
								opacity: 1,
								scaleX: 1,
								scaleY: 1,
							}, '0' );

							withExit && timeline.to( target, 0.4, { opacity: 0 }, '1.6' );
							withExit && timeline.to( target, 1, { x: 100 }, '1.6' );

						//  Zoom Out //

						} else if ( type === 'move-scale-out-from-top' ) {
							timeline.fromTo( target, 1, {
								y: -160,
								opacity: 0,
								scaleX: 1.6,
								scaleY: 1.6,
							}, {
								y: 0,
								opacity: 1,
								scaleX: 1,
								scaleY: 1,
							}, '0' );

							withExit && timeline.to( target, 0.4, { opacity: 0 }, '1.6' );
							withExit && timeline.to( target, 1, { y: -100 }, '1.6' );

						} else if ( type === 'move-scale-out-from-bottom' ) {
							timeline.fromTo( target, 1, {
								y: 160,
								opacity: 0,
								scaleX: 1.6,
								scaleY: 1.6,
							}, {
								y: 0,
								opacity: 1,
								scaleX: 1,
								scaleY: 1,
							}, '0' );

							withExit && timeline.to( target, 0.4, { opacity: 0 }, '1.6' );
							withExit && timeline.to( target, 1, { y: -100 }, '1.6' );

						} else if ( type === 'move-scale-out-from-left' ) {
							timeline.fromTo( target, 1, {
								x: -160,
								opacity: 0,
								scaleX: 1.6,
								scaleY: 1.6,
							}, {
								x: 0,
								opacity: 1,
								scaleX: 1,
								scaleY: 1,
							}, '0' );

							withExit && timeline.to( target, 0.4, { opacity: 0 }, '1.6' );
							withExit && timeline.to( target, 1, { x: -100 }, '1.6' );

						} else if ( type === 'move-scale-out-from-right' ) {
							timeline.fromTo( target, 1, {
								x: 160,
								opacity: 0,
								scaleX: 1.6,
								scaleY: 1.6,
							}, {
								x: 0,
								opacity: 1,
								scaleX: 1,
								scaleY: 1,
							}, '0' );

							withExit && timeline.to( target, 0.4, { opacity: 0 }, '1.6' );
							withExit && timeline.to( target, 1, { x: 100 }, '1.6' );

						//  Rotate //

						} else if ( type === 'rotate-from-top-right' ) {
							timeline.fromTo( target, 1, {
								y: -200,
								x: 120,
								rotation: -10,
								opacity: 0,
							}, {
								y: 0,
								x: 0,
								rotation: 0,
								opacity: 1,
							}, '0' );

							withExit && timeline.fromTo( target, 1, { immediateRender: false, y: 0 }, { y: -70 }, '1.6' );

						} else if ( type === 'expand-scroll' ) {
							var contentWrap = target.querySelector( '.fl-row-content-wrap' );

							var scrollableContent = target.querySelector( '.vamtam-expand-scroll-content' );

							var totalWidth = target.vamtamProgressiveTimeline.options.pin;

							scrollableContent.style.width = totalWidth + 'px';
							scrollableContent.style.boxSizing = 'content-box';

							// this always has an exit animation
							timeline.fromTo( scrollableContent, 2, {
								x: 0,
							}, {
								x: - ( totalWidth - this.winWidth + parseInt( getComputedStyle( contentWrap ).paddingLeft, 10 ) * 2 ) + 'px',
							}, 0 );

						//  Rotate //

						} else if ( type === 'page-title' ) {
							var line   = target.querySelector( '.page-header-line' );
							var desc   = target.querySelector( '.desc' );
							var shadow = document.getElementById( 'sub-header' ).querySelector( '.text-shadow' );

							timeline.fromTo( target.querySelector( 'h1' ), 0.9, { y: 0, opacity: 1 }, { y: -10, opacity: 0, ease: vamtamgs.Quad.easeIn }, '0.1' );

							desc && timeline.fromTo( desc, 1, { y: 0, opacity: 1 }, { y: -40, opacity: 0, ease: vamtamgs.Quad.easeIn }, '0' );

							shadow && timeline.to( shadow, 1, { className: 'text-shadow shadow-darkened', ease: vamtamgs.Quad.easeIn }, '0' );

							line && timeline.to( line, 1, { scaleX: 1, y: -20, opacity: 0, ease: vamtamgs.Quad.easeIn }, '0' );
						} else if ( type === 'custom' ) {
							timeline.to( target, 1, { className: target.getAttribute( 'data-progressive-animation-custom' ) }, '1' );
						}


						return timeline;
					},
					getPinTrigger: function( column ) {
						if ( column.options.pinTrigger === 'center' ) {
							return this.winHeight / 2 - column.height / 2;
						}

						if ( column.options.pinTrigger === 'bottom' ) {
							return this.winHeight - column.height;
						}

						if ( column.options.pinTrigger === 'top' ) {
							return 0;
						}
					},
					calculatePinDuration: function( column ) {
						if ( column.vamtamProgressiveTimeline.options.pin === 'parent' ) {
							if ( v.MEDIA.layout[ 'layout-below-max' ] ) {
								column.vamtamProgressiveTimeline.pinDuration = 0;
							} else {
								var closestRow = column.closest( '.fl-row-content' );
								column.vamtamProgressiveTimeline.pinDuration = closestRow.offsetHeight - ( column.vamtamProgressiveTimeline.top - v.offset( closestRow ).top );
							}
						} else {
							if ( 'pinUnit' in column.vamtamProgressiveTimeline.options && column.vamtamProgressiveTimeline.options.pinUnit === 'vw' ) {
								column.vamtamProgressiveTimeline.pinDuration = ( + column.vamtamProgressiveTimeline.options.pin ) / 100 * this.winWidth;
							} else {
								column.vamtamProgressiveTimeline.pinDuration = + column.vamtamProgressiveTimeline.options.pin;
							}
						}
					},
					onresize: function() {
						var self = this;

						if (this.winWidth === window.innerWidth) {
							return;
						}

						this.winWidth = window.innerWidth;

						this.winHeight = window.innerHeight;
						this.blockAnimations = true;

						requestAnimationFrame( function() {
							// if the timeline was previously initialized - reset the progress to 0
							for ( var i = 0; i < columns.length; i++ ) {
								if ( columns[i].vamtamProgressiveTimeline.timeline ) {
									columns[i].vamtamProgressiveTimeline.timeline.progress( 0 );
								}

								if ( columns[i].vamtamProgressiveTimeline.wrapper ) {
									Object.assign( columns[i].vamtamProgressiveTimeline.pusher.style, {
										top: '',
										width: '',
										height: '',
									} );

									Object.assign( columns[i].vamtamProgressiveTimeline.wrapper.style, {
										top: '',
										width: '',
										height: '',
										position: '',
									} );
								}
							}

							requestAnimationFrame( function() {
								var cpos = window.pageYOffset;
								var i;
								var chromeWrapperFix = [];

								// measure
								for ( i = 0; i < columns.length; i++ ) {
									var columnTop = v.offset( columns[i] ).top;

									Object.assign( columns[i].vamtamProgressiveTimeline, {
										top: columnTop,
										height: columns[i].offsetHeight,
										width: columns[i].offsetWidth,
									} );

									self.calculatePinDuration( columns[i] );
								}

								// mutate
								for ( i = 0; i < columns.length; i++ ) {
									var data = columns[i].vamtamProgressiveTimeline;

									if ( self.canActivate( data.options.mobile ) ) {
										data.timeline = self.buildTimeline(
											columns[i],
											data.options.type === 'progressive' && data.options.exit
										);

										if ( data.pusher ) {
											data.pusher.parentElement.minHeight = data.options.pin + 'px';
											data.pusher.style.height = data.pinDuration + 'px';

											if ( ! data.pusher.classList.contains( 'fl-col' ) ) {
												data.pusher.style.width = data.width + 'px';
											}

											data.wrapper.style.height = data.height + 'px';
											data.wrapper.style.top    = self.getPinTrigger( data ) + 'px';

											data.wrapper.classList.add( 'vamtam-pin-active' );
										}
									} else if ( data.timeline ) {
										if ( data.options.type === 'progressive' ) {
											if ( data.timeline.totalDuration() > 1 || ! data.options.exit ) {
												data.timeline.seek( 1 );
											} else {
												data.timeline.seek( 0 );
											}
										} else {
											data.timeline.seek( 1 );
										}

										data.timeline = null;

										if ( data.pusher ) {
											data.pusher.parentElement.minHeight = '';
											data.wrapper.classList.remove( 'vamtam-pin-active' );

											/*
												Fix a weird Chrome bug where the wrapper
												behaves as if it has visibility: hidden
												after disabling the pin for narrow screens
											 */
											data.wrapper.style.display = 'block';
											chromeWrapperFix.push( data.wrapper );
											/* End Chrome fix */
										}
									}
								}

								(function( wrappers ) {
									requestAnimationFrame( function() {
										wrappers.forEach( function( wrapper ) {
											wrapper.style.display = '';
										} );
									} );
								})( chromeWrapperFix );

								self.blockAnimations = false;
								self.measure( cpos );
								self.mutate( cpos );
							} );
						} );
					},
					init: function() {
						this.winHeight = window.innerHeight;
						this.winWidth = window.innerWidth;

						var i, closestRow;

						// measure
						for ( i = 0; i < columns.length; i++ ) {
							var options = Object.assign( {}, this.defaultOptions, JSON.parse( columns[i].getAttribute( 'data-vamtam-animation-options' ) ) || {} );

							var columnTop = v.offset( columns[i] ).top;
							var rect = columns[i].getBoundingClientRect();

							columns[i].vamtamProgressiveTimeline = {
								top: columnTop,
								height: Math.floor(rect.height),
								width: Math.floor(rect.width),
								options: options
							};

							this.calculatePinDuration( columns[i] );

							columns[i].style.transformOrigin = columns[i].vamtamProgressiveTimeline.options.origin;

							if ( this.canActivate( options.mobile ) ) {
								columns[i].vamtamProgressiveTimeline.timeline = this.buildTimeline(
									columns[i],
									options.type === 'progressive' && options.exit
								);
							} else {
								columns[i].vamtamProgressiveTimeline.timeline = null;
							}
						}

						// mutate
						for ( i = 0; i < columns.length; i++ ) {
							var data = columns[i].vamtamProgressiveTimeline;

							closestRow = columns[i].closest( '.fl-row' );

							closestRow && closestRow.classList.add( 'vamtam-animation-inside' );

							if ( data.options.pin !== false ) {
								closestRow.classList.add( 'vamtam-pin-inside' );

								data.pusher = document.createElement( 'div' );
								data.pusher.classList.add( 'vamtam-pin-pusher' );

								// by default Beaver Builder sets the width of the .fl-col element
								// we need to move the .fl-col class to the wrapper
								// and set the width of the original column to 100%
								if ( columns[i].classList.contains( 'fl-col' ) ) {
									data.pusher.classList.add( 'fl-col' );
									data.pusher.classList.add( 'fl-node-' + columns[i].attributes['data-node'].value );
									data.pusher.style.width = '';
								}

								data.wrapper = document.createElement( 'div' );
								data.wrapper.classList.add( 'vamtam-pin-wrapper' );
								data.wrapper.style.willChange = 'transform, position';

								data.wrapper.style.height = data.height + 'px';
								data.wrapper.style.top    = this.getPinTrigger( data ) + 'px';

								columns[i].before( data.pusher );
								data.wrapper.appendChild( columns[i] );
								data.pusher.appendChild( data.wrapper );

								columns[i].style.width = '100%';

								data.pusher.parentElement.style.position = 'relative';

								if ( data.timeline && data.options.pin !== 'parent' ) {
									data.pusher.parentElement.style.minHeight = data.options.pin + 'px';
								}

								if ( this.canActivate( data.options.mobile ) ) {
									Object.assign( data.pusher.style, {
										width: data.width + 'px',
										height: data.pinDuration + 'px',
									});

									data.wrapper.classList.add( 'vamtam-pin-active' );
								}
							}
						}

						window.addEventListener( 'resize', window.VAMTAM.debounce( this.onresize, 100 ).bind( this ), false );
					},
					measure: function() {

					},
					mutate: function( cpos ) {
						if ( this.blockAnimations ) {
							return;
						}

						for ( var i = 0; i < columns.length; i++ ) {
							var data = columns[i].vamtamProgressiveTimeline;

							if ( data.timeline && cpos + this.winHeight > data.top ) {
								// natural column vertical middle
								var from = data.top + data.height / 2;

								var progress;

								if ( data.options.pin !== false ) {
									var pinTrigger;

									if ( data.options.pinTrigger === 'center' ) {
										pinTrigger = cpos + this.winHeight / 2;
									} else if ( data.options.pinTrigger === 'bottom' ) {
										pinTrigger = cpos + this.winHeight - data.height / 2;
									} else if ( data.options.pinTrigger === 'top' ) {
										pinTrigger = cpos + data.height / 2;
									}

									// pin length starts when the "natural column vertical middle"
									// aligns with the trigger (middle of the viewport, top/bottom of viewport - half column height)
									//
									// it ends after data.pinDuration - data.height px

									var pinTo = from + data.pinDuration - data.height;

									progress = 2 * ( pinTrigger - from ) / ( pinTo - from ) - 1;
								} else {
									progress = 1 - ( ( from - cpos ) / Math.min( this.winHeight / 2, from ) );
								}

								progress -= data.options.delay;

								if ( data.options.type === 'progressive' ) {
									if ( data.timeline.totalDuration() > 1 || ! columns[i].vamtamProgressiveTimeline.options.exit ) {
										// two part (entry/exit) animation
										// note that the exit is optional
										progress = Math.min( 1, Math.max( -1, progress ) ); // clip

										// progress + 1 is used so that we can avoid negative position params
										//
										// [0; 1] -> entrance animation
										// [1; 2] -> exit animation
										//
										// it's then divided by two, since the progress() method takes a [0; 1] fraction as its argument
										progress = progress + 1;
									} else {
										// only exit animation
										progress = Math.min( 1, Math.max( 0, progress ) ); // clip
									}

									data.timeline.seek( progress );
								} else {
									if ( ! data.used && progress >= 0 ) {
										data.used = true;
										data.timeline.timeScale( 2 ).play();
									} else if ( data.used && ! data.timeline.isActive() && data.timeline.progress() < 1 ) {
										// if the animation was played once - make sure that the timeline is at its end
										data.timeline.seek( 1 );
									}
								}
							}
						}
					}
				} );
			} );
		}
	}, { passive: true });

} )( window.VAMTAM );

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
(function(v, undefined) {
	'use strict';

	// lazy loading
	var observer;

	if ( 'IntersectionObserver' in window ) {
		observer = new IntersectionObserver( function( changes ) {
			changes.forEach( function( change ) {
				if ( change.intersectionRatio > 0 || change.isIntersecting ) {
					showImage( change.target );
					observer.unobserve(change.target);
				}
			});
		}, {
			rootMargin: '200px',
		});
	}

	function onImageLoad() {
		/* jshint validthis: true */
		this.removeEventListener( 'load', onImageLoad );

		requestAnimationFrame( function() {
			if ( ! ( this.classList.contains( 'vamtam-lazyload-noparent' ) ) && this.parentElement ) {
				this.parentElement.classList.add( 'image-loaded' );
			} else {
				this.classList.add( 'image-loaded' );
			}
		}.bind( this ) );
	}

	function showImage( image ) {
		var srcset = image.dataset.srcset;

		if ( srcset ) {
			requestAnimationFrame( function() {
				image.addEventListener( 'load', onImageLoad );
				image.srcset = srcset;
			} );

			delete image.dataset.srcset;
		} else {
			onImageLoad.call( image );
		}
	}

	// Either observe the images, or load immediately if IntersectionObserver doesn't exist
	function addElements() {
		let i;

		let backgrounds = document.querySelectorAll( '.fl-row-content-wrap:not(.vamtam-show-bg-image), .fl-col-content:not(.vamtam-show-bg-image)' );
		for ( i = 0; i < backgrounds.length; i++ ) {
			backgrounds[i].classList.add( 'vamtam-show-bg-image' );
		}
	}

	document.addEventListener('DOMContentLoaded', function() {
		var mutationObserver = new MutationObserver( addElements );

		mutationObserver.observe( document.body, {
			childList: true,
			subtree: true
		} );

		addElements();
	});
})( window.VAMTAM );

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

( function( $, undefined ) {
	'use strict';

	window.Cookies = window.Cookies || {
		get: function( name ) {
			var value = '; ' + document.cookie;
			var parts = value.split( '; ' + name + '=' );

			if ( parts.length === 2 ) {
				return parts.pop().split( ';' ).shift();
			}
		}
	};

	$( function() {
		var dropdown  = $( '.fixed-header-box .cart-dropdown' ),
			wrapper   = $( '.vamtam-header-cart-wrapper' ),
			link      = $( '.vamtam-cart-dropdown-link' ),
			count     = $( '.products', link ),
			widget    = $( '.widget', dropdown ),
			isVisible = false;

		var dropdownEnabled = false;

		if ( 'wc_cart_fragments_params' in window ) {
			// this is the shimmed version
			if ( 'jspath' in window.wc_cart_fragments_params ) {
				window.VAMTAM.load_script( window.wc_cart_fragments_params.jspath );

				window.addEventListener( 'load', function() {
					window.VAMTAM.load_style( window.wc_cart_fragments_params.csspath, 'all', function() {
						dropdownEnabled = true;
					} );
				} );
			} else {
				dropdownEnabled = true;
			}
		}

		$( document.body ).on( 'added_to_cart removed_from_cart wc_fragments_refreshed wc_fragments_loaded', function() {
			var count_val = parseInt( Cookies.get( 'woocommerce_items_in_cart' ) || 0, 10 );

			if ( count_val > 0 ) {
				var count_real = 0;

				var spans = document.querySelector( '.widget_shopping_cart' ).querySelectorAll( 'li .quantity' );

				for ( var i = 0; i < spans.length; i++ ) {
					count_real += parseInt( spans[i].innerHTML.split( '<span' )[0].replace( /[^\d]/g, '' ), 10 );
				}

				// sanitize count_real - if it's not a number, then don't show the counter at all
				count_real = count_real >= 0 ? count_real : '';

				count.text( count_real );
				count.removeClass( 'cart-empty' );
				wrapper.removeClass( 'hidden' );
			} else {
				var show_if_empty = dropdown.hasClass( 'show-if-empty' );

				count.addClass( 'cart-empty' );
				count.text( '0' );

				wrapper.toggleClass( 'hidden', ! show_if_empty );
			}
		} );

		var open = 0;

		var showCart = function() {
			if ( ! dropdownEnabled ) {
				return;
			}

			open = +new Date();
			dropdown.addClass( 'state-hover' );
			widget.stop( true, true ).fadeIn( 300, function() {
				isVisible = true;
			} );
		};

		var hideCart = function() {
			var elapsed = new Date() - open;

			if( elapsed > 1000 ) {
				dropdown.removeClass( 'state-hover' );
				widget.stop( true, true ).fadeOut( 300, function() {
					isVisible = false;
				} );
			} else {
				setTimeout( function() {
					if( !dropdown.is( ':hover' ) ) {
						hideCart();
					}
				}, 1000 - elapsed );
			}
		};

		dropdown.on( 'mouseenter', function() {
			showCart();
		} ).on( 'mouseleave', function() {
			hideCart();
		} );

		link.on( 'click', function( e ) {
			if ( ! link.hasClass( 'no-dropdown' ) && dropdownEnabled ) {
				if( isVisible ) {
					hideCart();
				} else {
					showCart();
				}

				e.preventDefault();
			}
		} );
	} );
} )( jQuery );