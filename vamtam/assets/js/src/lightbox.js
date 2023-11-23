/* jshint esversion: 6, module: true */
export default function() {
	// button with lightbox

	{
		window.addEventListener( 'click', e => {
			let button = e.target.closest( '[data-vamtam-lightbox]' );

			if ( button ) {
				e.preventDefault();

				const contents = document.getElementById( 'vamtam-lightbox-template' );

				const lightboxWrapper = document.createElement( 'div' );
				lightboxWrapper.classList.add( 'vamtam-button-lightbox-wrapper' );
				lightboxWrapper.innerHTML = contents.innerHTML.replace( '{{ lightbox_content }}', button.dataset.vamtamLightbox );

				const closeLightbox = e => {
					e.preventDefault();

					requestAnimationFrame( () => {
						lightboxWrapper.addEventListener( 'transitionend', () => {
							lightboxWrapper.remove();

							document.documentElement.style.marginRight = '';
							document.documentElement.style.overflow    = '';
						}, false );

						lightboxWrapper.style.transitionDuration = '0.2s';
						lightboxWrapper.style.opacity = 0;
					} );
				};

				lightboxWrapper.querySelector( '.vamtam-button-lightbox-close' ).addEventListener( 'click', closeLightbox );
				lightboxWrapper.addEventListener( 'click', closeLightbox );

				requestAnimationFrame( () => {
					document.body.appendChild( lightboxWrapper );

					document.documentElement.style.marginRight = ( window.innerWidth - document.documentElement.offsetWidth ) + 'px';
					document.documentElement.style.overflow    = 'hidden';

					window.VAMTAM.resizeElements();
					lightboxWrapper.style.opacity = 1;
				} );
			}
		} );
	}

	// search

	const lightbox = document.getElementById( 'vamtam-overlay-search' );
	const inside   = lightbox.children;

	// initialize styles before any animations
	lightbox.style.display = 'none';

	for ( let i = 0; i < inside.length; i++ ) {
		inside[ i ].style.animationDuration = '300ms';
		inside[ i ].style.display           = 'none';
	}

	// opening animation
	document.body.addEventListener( 'click', function( e ) {
		if ( e.target.closest( '.vamtam-overlay-search-trigger' ) ) {
			e.preventDefault();

			requestAnimationFrame( function() {
				lightbox.classList.add( 'vamtam-animated', 'vamtam-fadein' );
				lightbox.style.display = 'block';

				setTimeout( function() {
					requestAnimationFrame( function() {
						for ( var i = 0; i < inside.length; i++ ) {
							inside[ i ].style.display = 'block';
							inside[ i ].classList.add( 'vamtam-animated', 'vamtam-zoomin' );
						}

						requestAnimationFrame( function() {
							lightbox.querySelector( 'input[type=search]' ).focus();
						} );
					} );
				}, 200 );
			} );
		}
	} );

	var $lightbox = jQuery( lightbox );

	var animationEndCallback = function() {
		requestAnimationFrame( function() {
			lightbox.style.display = 'none';
			lightbox.classList.remove( 'vamtam-animated', 'vamtam-fadeout' );

			for ( var i = 0; i < inside.length; i++ ) {
				inside[ i ].style.display = 'none';
				inside[ i ].classList.remove( 'vamtam-animated', 'vamtam-zoomout' );
			}
		} );
	};

	// closing animation
	document.getElementById( 'vamtam-overlay-search-close' ).addEventListener( 'click', function(e) {
		e.preventDefault();

		requestAnimationFrame( function() {
			lightbox.classList.remove( 'vamtam-animated', 'vamtam-fadein' );
			lightbox.classList.add( 'vamtam-animated', 'vamtam-fadeout' );

			for ( var i = 0; i < inside.length; i++ ) {
				inside[ i ].classList.remove( 'vamtam-animated', 'vamtam-zoomin' );
				inside[ i ].classList.add( 'vamtam-animated', 'vamtam-zoomout' );
			}

			$lightbox.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', animationEndCallback );

			if ( window.VAMTAM.MEDIA.fallback ) {
				animationEndCallback(); // IE11 won't animate because of incorrect classList support
			}
		} );
	} );
}
