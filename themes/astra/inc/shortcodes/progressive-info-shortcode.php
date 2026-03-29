<?php
/**
 * Muukal progressive info shortcode.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ASTRA_THEME_DIR . 'inc/render/progressive-info.php';

/**
 * Register assets for the progressive info shortcode.
 */
function muukal_progressive_info_register_assets() {
	wp_register_style(
		'muukal-progressive-info',
		ASTRA_THEME_URI . 'assets/css/progressive-info.css',
		array(),
		filemtime( ASTRA_THEME_DIR . 'assets/css/progressive-info.css' )
	);

	wp_register_script(
		'muukal-progressive-info',
		ASTRA_THEME_URI . 'assets/js/progressive-info.js',
		array(),
		filemtime( ASTRA_THEME_DIR . 'assets/js/progressive-info.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'muukal_progressive_info_register_assets' );

/**
 * Render the progressive info shortcode.
 *
 * @param array<string, string> $atts Shortcode attributes.
 * @return string
 */
function muukal_progressive_info_shortcode( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'margin_top' => '50px',
			'max_width'  => '1200px',
			'section_id' => 'muukal-progressive-info',
		),
		$atts,
		'muukal_progressive_info'
	);

	wp_enqueue_style( 'muukal-progressive-info' );
	wp_enqueue_script( 'muukal-progressive-info' );

	return muukal_render_progressive_info( $atts );
}
add_shortcode( 'muukal_progressive_info', 'muukal_progressive_info_shortcode' );
