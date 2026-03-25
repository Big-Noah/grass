<?php
/**
 * Muukal featured collection nav shortcode.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ASTRA_THEME_DIR . 'inc/render/featured-collection-nav.php';

/**
 * Register assets for featured collection nav.
 */
function muukal_featured_collection_nav_register_assets() {
	wp_register_style(
		'muukal-featured-collection-nav',
		ASTRA_THEME_URI . 'assets/css/featured-collection-nav.css',
		array(),
		ASTRA_THEME_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'muukal_featured_collection_nav_register_assets' );

/**
 * Render the featured collection nav shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function muukal_featured_collection_nav_shortcode( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'items'           => implode( ',', array_keys( muukal_featured_collection_nav_default_items() ) ),
			'recommend_label' => 'RECOMMEND',
			'recommend_url'   => '',
		),
		$atts,
		'muukal_featured_collection_nav'
	);

	wp_enqueue_style( 'muukal-featured-collection-nav' );

	return muukal_render_featured_collection_nav( $atts );
}
add_shortcode( 'muukal_featured_collection_nav', 'muukal_featured_collection_nav_shortcode' );
