/* jshint esnext:true */

import { toggle } from './helpers';

var general = ( api, $ ) => {
	'use strict';

	api( 'vamtam_theme[show-splash-screen]', value => {
		value.bind( to => {
			if ( + to ) {
				$( 'body' ).triggerHandler( 'vamtam-preview-splash-screen' );
			}
		} );
	} );

	api( 'vamtam_theme[splash-screen-logo]', value => {
		value.bind( to => {
			var wrapper = $( '.vamtam-splash-screen-progress-wrapper' );
			var current_image = wrapper.find( '> img' );

			if ( current_image.length === 0 ) {
				current_image = $('<img />');
				wrapper.prepend( current_image );
			}

			current_image.attr( 'src', to );

			$( 'body' ).triggerHandler( 'vamtam-preview-splash-screen' );
		} );
	} );

	api( 'vamtam_theme[show-scroll-to-top]', value => {
		value.bind( to => {
			toggle( $( '#scroll-to-top' ), to );
		} );
	} );

	api( 'vamtam_theme[show-related-posts]', value => {
		value.bind( to => {
			toggle( $( '.vamtam-related-content.related-posts' ), to );
		} );
	} );

	api( 'vamtam_theme[related-posts-title]', value => {
		value.bind( to => {
			$( '.related-posts .related-content-title' ).html( to );
		} );
	} );

	api( 'vamtam_theme[show-single-post-image]', value => {
		value.bind( to => {
			toggle( $( '.single-post > .post-media-image' ), to );
		} );
	} );

	api( 'vamtam_theme[post-meta]', value => {
		value.bind( to => {
			for ( let type in to ) {
				toggle( $( '.vamtam-meta-' + type ), + to[ type ] );
			}
		} );
	} );

	api( 'vamtam_theme[show-related-portfolios]', value => {
		value.bind( to => {
			toggle( $( '.vamtam-related-content.related-portfolios' ), to );
		} );
	} );

	api( 'vamtam_theme[related-portfolios-title]', value => {
		value.bind( to => {
			$( '.related-portfolios .related-content-title' ).html( to );
		} );
	} );
};

export default general;