<?php
/**
 * Muukal home product tabs shortcode.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ASTRA_THEME_DIR . 'inc/render/home-product-tabs.php';

/**
 * Register assets for home product tabs.
 */
function muukal_home_product_tabs_register_assets() {
	wp_register_style(
		'muukal-home-product-tabs',
		ASTRA_THEME_URI . 'assets/css/home-product-tabs.css',
		array(),
		ASTRA_THEME_VERSION
	);

	wp_register_script(
		'muukal-home-product-tabs',
		ASTRA_THEME_URI . 'assets/js/home-product-tabs.js',
		array(),
		ASTRA_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'muukal_home_product_tabs_register_assets' );

/**
 * Render the home product tabs shortcode.
 *
 * @param array $atts Shortcode atts.
 * @return string
 */
function muukal_home_product_tabs_shortcode( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'title'           => 'Featured Eyewear Collections',
			'per_tab'         => 8,
			'recommend_label' => 'Recommend',
			'popular_label'   => 'Hot Sales',
			'new_label'       => 'New In',
			'category_slugs'  => '',
		),
		$atts,
		'muukal_home_product_tabs'
	);

	wp_enqueue_style( 'muukal-home-product-tabs' );
	wp_enqueue_script( 'muukal-home-product-tabs' );

	return muukal_render_home_product_tabs( $atts );
}
add_shortcode( 'muukal_home_product_tabs', 'muukal_home_product_tabs_shortcode' );

