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
