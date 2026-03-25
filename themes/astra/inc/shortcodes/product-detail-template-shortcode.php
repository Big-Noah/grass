<?php
/**
 * Muukal product detail template shortcode.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ASTRA_THEME_DIR . 'inc/render/product-detail-template.php';

/**
 * Register assets for the product detail template shortcode.
 */
function muukal_product_detail_template_register_assets() {
	wp_register_style(
		'muukal-product-detail-template',
		ASTRA_THEME_URI . 'assets/css/product-detail-template.css',
		array( 'muukal-product-loop-item' ),
		ASTRA_THEME_VERSION
	);

	wp_register_script(
		'muukal-product-detail-template',
		ASTRA_THEME_URI . 'assets/js/product-detail-template.js',
		array(),
		ASTRA_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'muukal_product_detail_template_register_assets' );

/**
 * Render the Muukal product detail shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function muukal_product_detail_template_shortcode( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'product_id'   => '',
			'short_name'   => '',
			'sub_name'     => '',
			'promo_text'   => 'Any 3 Pairs for $119',
			'promo_link'   => home_url( '/' ),
			'promo_image'  => 'https://img.muukal.com/topic/spring/2026/spring_goods.jpg',
		),
		$atts,
		'muukal_product_detail_template'
	);

	wp_enqueue_style( 'muukal-product-loop-item' );
	wp_enqueue_style( 'muukal-product-detail-template' );
	wp_enqueue_script( 'muukal-product-detail-template' );

	return muukal_render_product_detail_template( $atts );
}
add_shortcode( 'muukal_product_detail_template', 'muukal_product_detail_template_shortcode' );
