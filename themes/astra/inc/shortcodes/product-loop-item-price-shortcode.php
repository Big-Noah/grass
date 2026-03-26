<?php
/**
 * Muukal product loop item price-first shortcode.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ASTRA_THEME_DIR . 'inc/render/product-loop-item-price.php';

/**
 * Register assets for the price-first product loop item shortcode.
 */
function muukal_product_loop_item_price_register_assets() {
	$style_path = ASTRA_THEME_DIR . 'assets/css/product-loop-item-price.css';
	$style_ver  = file_exists( $style_path ) ? (string) filemtime( $style_path ) : ASTRA_THEME_VERSION;

	wp_register_style(
		'muukal-product-loop-item-price',
		ASTRA_THEME_URI . 'assets/css/product-loop-item-price.css',
		array( 'muukal-product-loop-item' ),
		$style_ver
	);
}
add_action( 'wp_enqueue_scripts', 'muukal_product_loop_item_price_register_assets' );

/**
 * Render the price-first Muukal-style product card for Elementor Loop Item.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function muukal_product_loop_item_price_shortcode( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'product_id' => '',
		),
		$atts,
		'muukal_product_loop_item_price'
	);

	wp_enqueue_style( 'muukal-product-loop-item' );
	wp_enqueue_style( 'muukal-product-loop-item-price' );
	wp_enqueue_script( 'muukal-product-loop-item' );

	return muukal_render_product_loop_item_price( $atts );
}
add_shortcode( 'muukal_product_loop_item_price', 'muukal_product_loop_item_price_shortcode' );
