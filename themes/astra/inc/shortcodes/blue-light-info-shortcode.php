<?php
/**
 * Muukal blue light info shortcode.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ASTRA_THEME_DIR . 'inc/render/blue-light-info.php';

/**
 * Register assets for the blue light info shortcode.
 */
function muukal_blue_light_info_register_assets() {
	wp_register_style(
		'muukal-blue-light-info',
		ASTRA_THEME_URI . 'assets/css/blue-light-info.css',
		array(),
		filemtime( ASTRA_THEME_DIR . 'assets/css/blue-light-info.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'muukal_blue_light_info_register_assets' );

/**
 * Render the blue light info shortcode.
 *
 * @param array<string, string> $atts Shortcode attributes.
 * @return string
 */
function muukal_blue_light_info_shortcode( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'margin_top'   => '80px',
			'max_width'    => '1200px',
			'hero_heading' => 'What is Blue Light?',
			'strain_title' => 'Problems Caused by Blue Light: Digital Eye Strain',
			'lens_title'   => 'Why Choose Muukal Blue-light Blocking Lenses',
		),
		$atts,
		'muukal_blue_light_info'
	);

	wp_enqueue_style( 'muukal-blue-light-info' );

	return muukal_render_blue_light_info( $atts );
}
add_shortcode( 'muukal_blue_light_info', 'muukal_blue_light_info_shortcode' );
