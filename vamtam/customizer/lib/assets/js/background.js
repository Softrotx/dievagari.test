(function( $, undefined ) {
	'use strict';

	wp.customize.controlConstructor['vamtam-background'] = wp.customize.Control.extend({
		ready: function() {
			var control = this;

			control.setupColor();

			// Shortcut so that we don't have to use _.bind every time we add a callback.
			_.bindAll( control, 'restoreDefault', 'removeFile', 'openFrame', 'select' );

			// Bind events, with delegation to facilitate re-rendering.
			control.container.on( 'click keydown', '.upload-button', control.openFrame );
			control.container.on( 'click keydown', '.thumbnail-image img', control.openFrame );
			control.container.on( 'click keydown', '.default-button', control.restoreDefault );
			control.container.on( 'click keydown', '.remove-button', control.removeFile );

			control.container.on( 'change', 'select', function() {
				var changed = {};

				changed[ this.dataset.key ] = this.value;

				control.setBackground( changed );
			} );
		},

		setupColor: function() {
			var control = this;
			var picker  = control.container.find('.vamtam-bg-color');

			picker.val( control.setting()['background-color'] ).wpColorPicker({
				change: function() {
					control.setBackground( { 'background-color': picker.val() } );
				},
				clear: function() {
					control.setBackground( { 'background-color': '' } );
				},
				palettes: false,
			});
		},

		/**
		 * Open the media modal.
		 */
		openFrame: function( event ) {
			if ( wp.customize.utils.isKeydownButNotEnterEvent( event ) ) {
				return;
			}

			event.preventDefault();

			if ( ! this.frame ) {
				this.initFrame();
			}

			this.frame.open();
		},

		/**
		 * Create a media modal select frame, and store it so the instance can be reused when needed.
		 */
		initFrame: function() {
			this.frame = wp.media({
				button: {
					text: this.params.button_labels.frame_button
				},
				states: [
					new wp.media.controller.Library({
						title:     this.params.button_labels.frame_title,
						library:   wp.media.query({ type: this.params.mime_type }),
						multiple:  false,
						date:      false
					})
				]
			});

			// When a file is selected, run a callback.
			this.frame.on( 'select', this.select );
		},

		/**
		 * Callback handler for when an attachment is selected in the media modal.
		 * Gets the selected image information, and sets it within the control.
		 */
		select: function() {
			// Get the attachment from the modal frame.
			var attachment = this.frame.state().get( 'selection' ).first().toJSON();

			// Set the Customizer setting; the callback takes care of rendering.
			this.setImage( attachment );
		},

		/**
		 * Reset the setting to the default value.
		 */
		restoreDefault: function( event ) {
			if ( wp.customize.utils.isKeydownButNotEnterEvent( event ) ) {
				return;
			}
			event.preventDefault();

			this.setImage( this.params.default['background-image'] );
			this.setBackground( this.params.default );
		},

		/**
		 * Called when the "Remove" link is clicked. Empties the setting.
		 *
		 * @param {object} event jQuery Event object
		 */
		removeFile: function( event ) {
			if ( wp.customize.utils.isKeydownButNotEnterEvent( event ) ) {
				return;
			}
			event.preventDefault();

			this.setImage( {} );
		},

		setImage: function( attachment ) {
			this.params.bg = _.extend( {}, this.params.bg, { 'background-image': attachment } );

			this.setting.set( _.extend( {}, this.setting(), { 'background-image': attachment.url || '' } ) );

			this.renderContent(); // Render the control to show the new image

			this.setupColor();
		},

		setBackground: function( new_bg ) {
			var old_setting = this.setting.get();

			this.params.bg = _.extend( {
				'background-repeat': 'no-repeat',
				'background-size': 'auto',
				'background-attachment': 'fixed',
				'background-position': 'left top'
			}, this.params.bg, new_bg );

			this.setting.set( _.extend( {
				'background-repeat': 'no-repeat',
				'background-size': 'auto',
				'background-attachment': 'fixed',
				'background-position': 'left top'
			}, old_setting, new_bg ) );
		}

	});
})( jQuery );