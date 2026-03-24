<?php
/**
 * Muukal index nav box shortcode.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ASTRA_THEME_DIR . 'inc/render/index-nav-box.php';

/**
 * Register assets for index nav box.
 */
function muukal_index_nav_box_register_assets() {
	wp_register_style(
		'muukal-index-nav-box',
		ASTRA_THEME_URI . 'assets/css/index-nav-box.css',
		array(),
		ASTRA_THEME_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'muukal_index_nav_box_register_assets' );

/**
 * Render the index nav box shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function muukal_index_nav_box_shortcode( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'link_base'        => 'https://muukal.com',
			'container_width'  => '1900px',
			'padding_top'      => '55px',
			'use_local_images' => 'true',
		),
		$atts,
		'muukal_index_nav_box'
	);

	wp_enqueue_style( 'muukal-index-nav-box' );

	return muukal_render_index_nav_box( $atts );
}
add_shortcode( 'muukal_index_nav_box', 'muukal_index_nav_box_shortcode' );

