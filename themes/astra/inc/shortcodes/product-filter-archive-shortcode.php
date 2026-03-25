<?php
/**
 * Muukal product filter archive shortcode.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ASTRA_THEME_DIR . 'inc/render/product-loop-swatch.php';
require_once ASTRA_THEME_DIR . 'inc/render/product-filter-archive.php';

/**
 * Register assets for product filter archive.
 */
function muukal_product_filter_archive_register_assets() {
	wp_register_style(
		'muukal-product-filter-archive',
		ASTRA_THEME_URI . 'assets/css/product-filter-archive.css',
		array( 'muukal-product-loop-item' ),
		ASTRA_THEME_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'muukal_product_filter_archive_register_assets' );

/**
 * Render the product filter archive shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function muukal_product_filter_archive_shortcode( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'category'         => '',
			'per_page'         => 12,
			'sort_by'          => 'recommended',
			'default_gender'   => '',
			'default_shape'    => '',
			'default_style'    => '',
			'default_color'    => '',
			'default_material' => '',
			'default_size'     => '',
			'default_feature'  => '',
		),
		$atts,
		'muukal_product_filter_archive'
	);

	wp_enqueue_style( 'muukal-product-loop-item' );
	wp_enqueue_style( 'muukal-product-filter-archive' );
	wp_enqueue_script( 'muukal-product-loop-item' );

	return muukal_render_product_filter_archive( $atts );
}
add_shortcode( 'muukal_product_filter_archive', 'muukal_product_filter_archive_shortcode' );
