<?php
/**
 * Muukal home banner grid shortcode.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ASTRA_THEME_DIR . 'inc/render/home-banner-grid.php';

/**
 * Register assets for the home banner grid shortcode.
 */
function muukal_home_banner_grid_register_assets() {
	wp_register_style(
		'muukal-home-banner-grid',
		ASTRA_THEME_URI . 'assets/css/home-banner-grid.css',
		array(),
		ASTRA_THEME_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'muukal_home_banner_grid_register_assets' );

/**
 * Render the home banner grid shortcode.
 *
 * @param array<string, string> $atts Shortcode attributes.
 * @return string
 */
function muukal_home_banner_grid_shortcode( $atts = array() ) {
	$defaults = muukal_home_banner_grid_default_items();

	$atts = shortcode_atts(
		array(
			'container_width' => '1900px',
			'padding_top'     => '75px',
			'link_1'          => $defaults[0]['link'],
			'image_1'         => $defaults[0]['image'],
			'alt_1'           => $defaults[0]['alt'],
			'link_2'          => $defaults[1]['link'],
			'image_2'         => $defaults[1]['image'],
			'alt_2'           => $defaults[1]['alt'],
			'link_3'          => $defaults[2]['link'],
			'image_3'         => $defaults[2]['image'],
			'alt_3'           => $defaults[2]['alt'],
		),
		$atts,
		'muukal_home_banner_grid'
	);

	wp_enqueue_style( 'muukal-home-banner-grid' );

	return muukal_render_home_banner_grid( $atts );
}
add_shortcode( 'muukal_home_banner_grid', 'muukal_home_banner_grid_shortcode' );
