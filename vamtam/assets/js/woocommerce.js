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