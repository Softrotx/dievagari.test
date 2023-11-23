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
