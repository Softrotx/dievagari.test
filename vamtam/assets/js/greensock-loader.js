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