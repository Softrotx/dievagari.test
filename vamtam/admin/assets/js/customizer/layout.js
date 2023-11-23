/* jshint esnext:true */

import { toggle } from './helpers';

var layout = ( api, $ ) => {
	'use strict';

	api( 'vamtam_theme[full-width-header]', value => {
		value.bind( to => {
			$( '.header-maybe-limit-wrapper' ).toggleClass( 'limit-wrapper', to );
		} );
	} );

	api( 'vamtam_theme[sticky-header]', value => {
		value.bind( to => {
			requestAnimationFrame( function() {
				document.body.classList.toggle( 'sticky-header', +to );
				document.body.classList.remove( 'had-sticky-header' );

				window.VAMTAM.stickyHeader.rebuild();
			} );
		} );
	} );

	api( 'vamtam_theme[enable-header-search]', value => {
		value.bind( to => {
			toggle( $( 'header.main-header .search-wrapper' ), + to );
		} );
	} );

	api( 'vamtam_theme[show-empty-header-cart]', value => {
		value.bind( to => {
			document.querySelectorAll( '.vamtam-header-cart-wrapper' ).forEach( el => el.classList.toggle( 'show-if-empty', + to ) );
			$( 'body' ).trigger( 'wc_fragments_refreshed' );
		} );
	} );

	api( 'vamtam_theme[one-page-footer]', value => {
		value.bind( to => {
			toggle( $( '.footer-wrapper' ), to );

			setTimeout( function() {
				window.VAMTAM.resizeElements();
			}, 50 );
		} );
	} );

	api( 'vamtam_theme[page-title-layout]', value => {
		value.bind( to => {
			var header = $( 'header.page-header' );
			var line   = header.find( '.page-header-line' );

			header
				.removeClass( 'layout-centered layout-one-row-left layout-one-row-right layout-left-align layout-right-align' )
				.addClass( 'layout-' + to );

			if ( to.match( /one-row-/ ) ) {
				line.appendTo( header.find( 'h1' ) );
			} else {
				line.appendTo( header );
			}
		} );
	} );
};

export default layout;
