<?php

/**
 * Local CSS compiler (Customizer preview)
 *
 * @package vamtam/landscaping
 */
/**
 * class VamtamCustomizerLocalCssAjax
 */
class VamtamCustomizerLocalCssAjax extends VamtamAjax {

	public static $storage_path;

	/**
	 * Hook ajax actions
	 */
	public function __construct() {
		$this->actions = array(
			'compile-local-css' => 'compile',
		);

		parent::__construct();
	}

	public static function compile() {
		$source  = $_POST['source'];
		$accents = $_POST['accents'];

		$accents_less = '';

		foreach ( $accents as $num => $color ) {
			$accents_less .= '@accent-color-' . intval( $num ) . ': ' . $color . ';';
		}

		$l            = new VamtamLessc();
		$l->importDir = '.';
		$l->setFormatter( 'compressed' );

		$inner_style = $l->compile( $accents_less . $source );

		$source_attr = ' data-vamtam-less-source="' . esc_attr( $source ) . '"';

		echo '<style' . $source_attr . '>' . $inner_style . '</style>'; // xss ok

		exit;
	}
}
