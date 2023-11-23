/* jshint esnext:true */

(function($, undefined) {
	'use strict';

	var api = wp.customize;

	// toggle visibility of some controls based on a setting's value
	// @see wp-admin/js/customize-controls.js
	$.each({
		'vamtam_theme[header-logo-type]': [
			{
				controls: [ 'vamtam_theme[custom-header-logo]' ],
				callback: function( to ) { return 'image' === to; }
			},
		],

		'vamtam_theme[site-layout-type]': [
			{
				controls: [ 'vamtam_theme[full-width-header]' ],
				callback: function( to ) { return 'boxed' !== to; }
			},

			{
				controls: [ 'vamtam_theme[boxed-layout-padding]', ],
				callback: function( to ) { return 'boxed' === to; }
			},

			{
				controls: ['vamtam_theme[body-background]'],
				callback: function( to ) { return 'full' !== to; }
			},
		],

		'vamtam_theme[header-layout]': [
			{
				controls: [ 'vamtam_theme[full-width-header]' ],
				callback: function( to ) { return 'logo-menu' === to; } // show if header is 'logo-menu'
			},

			{
				controls: [ 'vamtam_theme[sub-header-background]' ],
				callback: function( to ) { return 'logo-menu' !== to; } // show if header is not 'logo-menu'
			},
		],

		'vamtam_theme[top-bar-layout]': [
			{
				controls: [
					'vamtam_theme[top-bar-social-lead]',
					'vamtam_theme[top-bar-social-fb]',
					'vamtam_theme[top-bar-social-twitter]',
					'vamtam_theme[top-bar-social-linkedin]',
					'vamtam_theme[top-bar-social-gplus]',
					'vamtam_theme[top-bar-social-flickr]',
					'vamtam_theme[top-bar-social-pinterest]',
					'vamtam_theme[top-bar-social-dribbble]',
					'vamtam_theme[top-bar-social-instagram]',
					'vamtam_theme[top-bar-social-youtube]',
					'vamtam_theme[top-bar-social-vimeo]',
				],
				callback: function( to ) {
					return [ 'menu-social', 'social-menu', 'social-text', 'text-social' ].indexOf( to ) > -1;
				}
			},

			{
				controls: [
					'vamtam_theme[top-bar-text]',
				],
				callback: function( to ) {
					return [ 'menu-text', 'text-menu', 'social-text', 'text-social', 'fulltext' ].indexOf( to ) > -1;
				}
			},
		],
	}, ( settingId, conditions ) => {
		api( settingId, setting => {
			$.each( conditions, ( cndi, o ) => {
				$.each( o.controls, ( i, controlId ) => {
					api.control( controlId, ( control ) => {
						var visibility = ( to ) => {
							control.container.toggle( o.callback( to ) );
						};

						visibility( setting.get() );
						setting.bind( visibility );
					});
				});
			} );
		});
	});

})(jQuery);