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
