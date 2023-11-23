<?php

/**

Background image + colors

**/

class Vamtam_Customize_Background_Control extends Vamtam_Customize_Control {
	public $type = 'vamtam-background';

	/**
	 * Media control mime type.
	 *
	 * @access public
	 * @var string
	 */
	public $mime_type = 'image';

	/**
	 * Button labels.
	 *
	 * @access public
	 * @var array
	 */
	public $button_labels = array();

	/**
	 * Defines which properties are configurable
	 */
	public $show = array();

	/**
	 * Holds all possible values for the dropdown options
	 *
	 * @var array
	 */
	public static $selects = array();

	/**
	 * Constructor.
	 *
	 * @since 4.1.0
	 * @since 4.2.0 Moved from WP_Customize_Upload_Control.
	 *
	 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      Control ID.
	 * @param array                $args    Optional. Arguments to override class property defaults.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );

		$this->button_labels = wp_parse_args( $this->button_labels, array(
			'select'       => esc_html__( 'Select Image', 'mozo' ),
			'change'       => esc_html__( 'Change Image', 'mozo' ),
			'remove'       => esc_html__( 'Remove', 'mozo' ),
			'default'      => esc_html__( 'Default', 'mozo' ),
			'placeholder'  => esc_html__( 'No image selected', 'mozo' ),
			'frame_title'  => esc_html__( 'Select Image', 'mozo' ),
			'frame_button' => esc_html__( 'Choose Image', 'mozo' ),
		) );

		$this->show = wp_parse_args( $this->show, array(
			'background-image'      => true,
			'background-color'      => true,
			'background-attachment' => true,
			'background-size'       => true,
			'background-repeat'     => true,
			'background-position'   => true,
		) );

		self::$selects = array(
			'background-repeat' => array(
				'no-repeat' => esc_html__( 'No repeat', 'mozo' ),
				'repeat-x'  => esc_html__( 'Repeat horizontally', 'mozo' ),
				'repeat-y'  => esc_html__( 'Repeat vertically', 'mozo' ),
				'repeat'    => esc_html__( 'Repeat both', 'mozo' ),
			),
			'background-attachment' => array(
				'scroll' => esc_html__( 'scroll', 'mozo' ),
				'fixed'  => esc_html__( 'fixed', 'mozo' ),
			),
			'background-size' => array(
				'auto'    => esc_html__( 'auto', 'mozo' ),
				'cover'   => esc_html__( 'cover', 'mozo' ),
				'contain' => esc_html__( 'contain', 'mozo' ),
			),
			'background-position' => array(
				'left top'      => esc_html__( 'left top', 'mozo' ),
				'left center'   => esc_html__( 'left center', 'mozo' ),
				'left bottom'   => esc_html__( 'left bottom', 'mozo' ),
				'center top'    => esc_html__( 'center top', 'mozo' ),
				'center center' => esc_html__( 'center center', 'mozo' ),
				'center bottom' => esc_html__( 'center bottom', 'mozo' ),
				'right top'     => esc_html__( 'right top', 'mozo' ),
				'right center'  => esc_html__( 'right center', 'mozo' ),
				'right bottom'  => esc_html__( 'right bottom', 'mozo' ),
			),
		);
	}

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @since 3.4.0
	 * @since 4.2.0 Moved from WP_Customize_Upload_Control.
	 */
	public function enqueue() {
		wp_enqueue_media();

		wp_enqueue_script(
			'customizer-control-vamtam-background-js',
			VAMTAM_CUSTOMIZER_LIB_URL . 'assets/js/background' . ( WP_DEBUG ? '' : '.min' ) . '.js',
			array( 'jquery', 'customize-base', 'wp-color-picker' ),
			Vamtam_Customizer::$version,
			true
		);

		wp_enqueue_style(
			'customizer-control-vamtam-background',
			VAMTAM_CUSTOMIZER_LIB_URL . 'assets/css/background.css',
			array( 'wp-color-picker' ),
			Vamtam_Customizer::$version
		);
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 3.4.0
	 * @since 4.2.0 Moved from WP_Customize_Upload_Control.
	 *
	 * @see WP_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();

		$this->json['label']         = html_entity_decode( $this->label, ENT_QUOTES, get_bloginfo( 'charset' ) );
		$this->json['mime_type']     = $this->mime_type;
		$this->json['button_labels'] = $this->button_labels;
		$this->json['canUpload']     = current_user_can( 'upload_files' );
		$this->json['show']          = $this->show;
		$this->json['alt_medium']    = esc_html__( 'Medium-sized attachment', 'mozo' );
		$this->json['alt_full']      = esc_html__( 'Full-sized attachment', 'mozo' );

		$this->json['option_labels'] = array(
			'background-repeat'     => esc_html__( 'Repeat', 'mozo' ),
			'background-attachment' => esc_html__( 'Attachment', 'mozo' ),
			'background-size'       => esc_html__( 'Size', 'mozo' ),
			'background-position'   => esc_html__( 'Position', 'mozo' ),
			'background-color'      => esc_html__( 'Color', 'mozo' ),
		);

		$this->json['selects'] = self::$selects;

		$value = $this->value();

		if ( is_object( $this->setting ) ) {
			if ( $this->setting->default ) {
				// Fake an attachment model - needs all fields used by template.
				// Note that the default value must be a URL, NOT an attachment ID.
				$url = $this->setting->default['background-image'];

				$default = $this->setting->default;

				if ( ! empty( $url ) ) {
					$default['background-image'] = array(
						'id'    => 1,
						'url'   => $url,
						'type'  => 'image',
						'icon'  => 'image',
						'title' => basename( $url ),
						'sizes' => array(
							'full' => array(
								'url' => $url,
							),
						),
					);
				}

				$this->json['default'] = $default;
			}

			if ( $value && $this->setting->default && $value === $this->setting->default ) {
				// Set the default as the attachment.
				$this->json['bg'] = $this->json['default'];
			} elseif ( $value ) {
				if ( ! empty( $value['background-image'] ) && 0 !== ( $bg_image_id = attachment_url_to_postid( $value['background-image'] ) ) ) {
					$value['background-image'] = wp_prepare_attachment_for_js( $bg_image_id );
				}

				$this->json['bg'] = $value;
			}
		}
	}

	/**
	 * Don't render any content for this control from PHP.
	 *
	 * @since 3.4.0
	 * @since 4.2.0 Moved from WP_Customize_Upload_Control.
	 *
	 * @see WP_Customize_Media_Control::content_template()
	 */
	public function render_content() {}

	/**
	 * Render a JS template for the content of the media control.
	 *
	 * @since 4.1.0
	 * @since 4.2.0 Moved from WP_Customize_Upload_Control.
	 */
	public function content_template() {
		?>
		<label for="{{ data.settings['default'] }}-button">
			<# if ( data.label ) { #>
				<span class="customize-control-title">{{ data.label }}</span>
			<# } #>
			<# if ( data.description ) { #>
				<span class="description customize-control-description">{{{ data.description }}}</span>
			<# } #>
		</label>

		<# if ( data.bg['background-image'] && data.bg['background-image'].id ) { #>
			<div class="current">
				<div class="container">
					<div class="attachment-media-view attachment-media-view-{{ data.bg['background-image'].type }} {{ data.bg['background-image'].orientation }}">
						<div class="thumbnail thumbnail-{{ data.bg['background-image'].type }}">
							<# if ( data.bg['background-image'].sizes && data.bg['background-image'].sizes.medium ) { #>
								<img class="attachment-thumb" src="{{ data.bg['background-image'].sizes.medium.url }}" draggable="false" alt="{{ data.alt_medium }}" />
							<# } else if ( data.bg['background-image'].sizes && data.bg['background-image'].sizes.full ) { #>
								<img class="attachment-thumb" src="{{ data.bg['background-image'].sizes.full.url }}" draggable="false" alt="{{ data.alt_full }}" />
							<# } #>
						</div>
					</div>
				</div>
			</div>
			<div class="actions">
				<# if ( data.canUpload ) { #>
				<button type="button" class="button remove-button">{{ data.button_labels.remove }}</button>
				<button type="button" class="button upload-button control-focus" id="{{ data.settings['default'] }}-button">{{ data.button_labels.change }}</button>
				<div style="clear:both"></div>
				<# } #>
			</div>
		<# } else { #>
			<div class="current">
				<div class="container">
					<div class="placeholder">
						<div class="inner">
							<span>
								{{ data.button_labels.placeholder }}
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class="actions">
				<# if ( data.default ) { #>
					<button type="button" class="button default-button">{{ data.button_labels.default }}</button>
				<# } #>
				<# if ( data.canUpload ) { #>
				<button type="button" class="button upload-button" id="{{ data.settings['default'] }}-button">{{ data.button_labels.select }}</button>
				<# } #>
				<div style="clear:both"></div>
			</div>
		<# } #>

		<div class="background-grid">
			<# for ( key in data.selects ) { #>
				<# if ( data.show[ key ] ) { #>
					<div class="background-grid-el">
						<h5>{{ data.option_labels[ key ] }}</h5>
						<select id="{{ data.id }}-{{ key }}" data-key="{{ key }}">
							<# _.each( data.selects[ key ], function( val, opt_key ) { #>
								<option {{ data.bg[ key ] === opt_key ? 'selected' : '' }} value="{{ opt_key }}">{{ val }}</option>
							<# } ) #>
						</select>
					</div>
				<# } #>
			<# } #>

			<# if ( data.show['background-color'] ) { #>
				<div class="background-grid-el">
					<h5>{{ data.option_labels[ 'background-color' ] }}</h5>
					<input id="{{ data.id }}-color" type="text" data-default-color="{{ data.default[ 'background-color' ] }}" data-alpha="true" value="{{ data.bg[ 'background-color' ] }}" class="color-picker vamtam-bg-color" />
				</div>
			<# } #>
		</div>

		<?php
	}

	/**
	 * Sanitize setting value
	 *
	 * @uses shortcode_atts to ensure that a fixed set of properties is saved for this setting
	 */
	public static function sanitize_callback( $value ) {
		// must-have attributes
		$value = shortcode_atts( array(
			'background-image'      => '',
			'background-color'      => '',
			'background-repeat'     => '',
			'background-attachment' => '',
			'background-size'       => '',
			'background-position'    => '',
		), $value );

		// sanitize color and image
		$value['background-color'] = sanitize_hex_color( $value['background-color']	);
		$value['background-image'] = esc_url_raw( $value['background-image'] );

		// sanitize selects
		if ( ! in_array( $value['background-repeat'], array_keys( self::$selects['background-repeat'] ), true ) ) {
			$value['background-repeat'] = 'repeat';
		}

		if ( ! in_array( $value['background-attachment'], array_keys( self::$selects['background-attachment'] ), true ) ) {
			$value['background-attachment'] = 'scroll';
		}

		if ( ! in_array( $value['background-size'], array_keys( self::$selects['background-size'] ), true ) ) {
			$value['background-size'] = 'auto';
		}

		if ( ! in_array( $value['background-position'], array_keys( self::$selects['background-position'] ), true ) ) {
			$value['background-position'] = 'left top';
		}

		return $value;
	}
}
