<?php
/**
 * Muukal product loop item shortcode.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ASTRA_THEME_DIR . 'inc/render/product-loop-swatch.php';

/**
 * Register assets for the product loop item shortcode.
 */
function muukal_product_loop_item_register_assets() {
	$style_path  = ASTRA_THEME_DIR . 'assets/css/product-loop-item.css';
	$script_path = ASTRA_THEME_DIR . 'assets/js/product-loop-item.js';
	$style_ver   = file_exists( $style_path ) ? (string) filemtime( $style_path ) : ASTRA_THEME_VERSION;
	$script_ver  = file_exists( $script_path ) ? (string) filemtime( $script_path ) : ASTRA_THEME_VERSION;

	wp_register_style(
		'muukal-product-loop-item',
		ASTRA_THEME_URI . 'assets/css/product-loop-item.css',
		array(),
		$style_ver
	);

	wp_register_script(
		'muukal-product-loop-item',
		ASTRA_THEME_URI . 'assets/js/product-loop-item.js',
		array(),
		$script_ver,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'muukal_product_loop_item_register_assets' );

/**
 * Render one Muukal-style product card for Elementor Loop Item.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function muukal_product_loop_item_shortcode( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'product_id' => '',
		),
		$atts,
		'muukal_product_loop_item'
	);

	wp_enqueue_style( 'muukal-product-loop-item' );
	wp_enqueue_script( 'muukal-product-loop-item' );

	return muukal_render_product_loop_item( $atts );
}
add_shortcode( 'muukal_product_loop_item', 'muukal_product_loop_item_shortcode' );
