/* jshint esnext:true */

import { isNumeric, hexToHsl } from './helpers';

const styles = ( api, $ ) => {
	'use strict';

	const prepare_background = to => {
		if ( to['background-image'] !== '' ) {
			to['background-image'] = 'url(' + to['background-image'] + ')';
		}

		return to;
	};

	{
		const compiler_options = VAMTAM_CUSTOMIZE_PREVIEW.compiler_options;

		const real_id = function( id ) {
			return id.replace( /vamtam_theme\[([^\]]+)]/, '$1' );
		};

		const change_handler_by_type = {
			number: function( to ) {
				let id = real_id( this.id );

				if ( VAMTAM_CUSTOMIZE_PREVIEW.percentages.indexOf( id ) !== -1 ) {
					to += '%';
				} else if ( VAMTAM_CUSTOMIZE_PREVIEW.numbers.indexOf( id ) !== -1 ) {
					// as is
				} else {
					to += 'px';
				}

				document.documentElement.style.setProperty( `--vamtam-${id}`, to )

				// trigger a resize event if we change any dimension
				$( window ).resize();
			},
			background: function( to ) {
				let id = real_id( this.id );

				to = prepare_background( to );

				for ( let prop in to ) {
					document.documentElement.style.setProperty( `--vamtam-${id}-${prop}`, to[ prop ] );
				}
			},
			radio: function( to ) {
				let id = real_id( this.id );

				if ( isNumeric( to ) ) {
					change_handler_by_type.number.call( this, to );
				} else {
					document.documentElement.style.setProperty( `--vamtam-${id}`, to )
				}
			},
			select: function( to ) {
				let id = real_id( this.id );

				change_handler_by_type.radio.call( this, to );
			},
			typography: function( to, from ) {
				let id = real_id( this.id );

				let variant = to.variant;

				to['font-weight'] = 'normal';
				to['font-style']  = 'normal';

				to.variant = to.variant.split( ' ' );

				if ( to.variant.length === 2 ) {
					to['font-weight'] = to.variant[0];
					to['font-style']  = to.variant[1];
				} else if ( to.variant[0] === 'italic' ) {
					to['font-style'] = 'italic';
				} else {
					to['font-weight'] = to.variant[0];
				}

				delete to.variant;

				for ( let prop in to ) {
					document.documentElement.style.setProperty( `--vamtam-${id}-${prop}`, to[ prop ] );
				}

				// if the font-family is changed - we need to load the new font stylesheet
				if ( to['font-family'] !== from['font-family'] || to['variant'] !== from['variant'] ) {
					let new_font = window.top.VAMTAM_ALL_FONTS[ to['font-family'] ];

					if ( new_font.gf ) {
						let family = encodeURIComponent( to['font-family'] ) + ':' + new_font.weights.join( ',' ).replace( ' ', '' );
						let subset = ''; // no subset support here, only newer browser can preview Google Fonts

						let link = document.createElement("link");
						link.href = 'https://fonts.googleapis.com/css?family=' + family + '&subset=' + subset;
						link.type = 'text/css';
						link.rel = 'stylesheet';
						document.getElementsByTagName( 'head' )[0].appendChild(link);
					}
				}
			},
			'color-row': function( to ) {
				let id = real_id( this.id );

				for ( let prop in to ) {
					document.documentElement.style.setProperty( `--vamtam-${id}-${prop}`, to[ prop ] );
				}

				if ( id === 'accent-color' ) {
					// accents need readable colors
					for ( let i = 1; i <= 8; i++ ) {
						let hex = to[ i ];
						let hsl = hexToHsl( hex );

						let readable = '';
						let hc       = '';

						if ( hsl[2] > 80 ) {
							readable = `hsl(${hsl[0]}, ${hsl[1]}%, ${ Math.max( 0, hsl[2] - 50 ) }%)`;//  $color->darken( 50 );
							hc       = '#000000';
						} else {
							readable = `hsl(${hsl[0]}, ${hsl[1]}%, ${ Math.min( 0, hsl[2] + 50 ) }%)`;//  $color->lighten( 50 );
							hc       = '#ffffff';
						}

						document.documentElement.style.setProperty( `--vamtam-accent-color-${i}-readable`, readable );
						document.documentElement.style.setProperty( `--vamtam-accent-color-${i}-hc`, hc );
						document.documentElement.style.setProperty( `--vamtam-accent-color-${i}-transparent`, `hsl(${hsl[0]}, ${hsl[1]}%, ${hsl[2]}%, 0)` )
					}
				}
			},
			color: function( to ) {
				let id = real_id( this.id );

				document.documentElement.style.setProperty( `--vamtam-${id}`, to )
			},
		};

		// const compiler_option_handler = ;
		for ( let opt_name in compiler_options ) {
			api( opt_name, function( setting ) {
				const type = compiler_options[ opt_name ];

				if ( type in change_handler_by_type ) {
					setting.bind( change_handler_by_type[ type ] );
				} else {
					console.error( `VamTam Customzier: Missing handler for option type ${type} - option ${opt_name}` );
					window.wpvval = setting;
				}
			} );
		}
	}

	api( 'vamtam_theme[page-title-background-hide-lowres]', value => {
		value.bind( to => {
			$( 'header.page-header' ).toggleClass( 'vamtam-hide-bg-lowres', to );
		} );
	} );

	api( 'vamtam_theme[main-background-hide-lowres]', value => {
		value.bind( to => {
			$( '.vamtam-main' ).toggleClass( 'vamtam-hide-bg-lowres', to );
		} );
	} );
};

export default styles;
