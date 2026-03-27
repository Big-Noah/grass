<?php
/**
 * Muukal header shortcode.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ASTRA_THEME_DIR . 'inc/render/header.php';
require_once ASTRA_THEME_DIR . 'inc/render/trustpilot-bar.php';

/**
 * Register assets for the custom header.
 */
function muukal_header_register_assets() {
	wp_register_style(
		'muukal-header',
		ASTRA_THEME_URI . 'assets/css/header.css',
		array(),
		ASTRA_THEME_VERSION
	);

	wp_register_script(
		'muukal-header',
		ASTRA_THEME_URI . 'assets/js/header.js',
		array( 'jquery' ),
		ASTRA_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'muukal_header_register_assets' );

/**
 * Return the latest cart count for header badges.
 *
 * @return void
 */
function muukal_header_cart_count_ajax() {
	$count = 0;

	if ( function_exists( 'WC' ) && WC()->cart ) {
		$count = (int) WC()->cart->get_cart_contents_count();
	}

	wp_send_json_success(
		array(
			'count' => $count,
		)
	);
}
add_action( 'wp_ajax_muukal_header_cart_count', 'muukal_header_cart_count_ajax' );
add_action( 'wp_ajax_nopriv_muukal_header_cart_count', 'muukal_header_cart_count_ajax' );

/**
 * Render the header shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function muukal_header_shortcode( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'menu_location'  => 'primary',
			'promo_text'     => 'Spring Sale, Second Pair Free',
			'promo_link'     => home_url( '/' ),
			'search_text'    => 'Search products',
			'account_label'  => 'Login/Register',
			'mobile_label'   => 'Menu',
		),
		$atts,
		'muukal_header'
	);

	wp_enqueue_style( 'muukal-header' );
	wp_enqueue_script( 'muukal-header' );
	wp_localize_script(
		'muukal-header',
		'muukalHeader',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		)
	);

	return muukal_render_header( $atts );
}
add_shortcode( 'muukal_header', 'muukal_header_shortcode' );

/**
 * Render the Trustpilot review bar shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function muukal_trustpilot_bar_shortcode( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'label'           => 'Excellent',
			'rating'          => '4.3',
			'reviews'         => '6,438 reviews',
			'prefix'          => '4.3 out of 5 based on',
			'brand'           => 'Trustpilot',
			'link'            => 'https://www.trustpilot.com/review/muukal.com',
			'background'      => '#f3f3f3',
			'padding'         => '10px 0',
			'container_width' => '1400px',
		),
		$atts,
		'muukal_trustpilot_bar'
	);

	wp_enqueue_style( 'muukal-header' );

	return muukal_render_trustpilot_bar( $atts );
}
add_shortcode( 'muukal_trustpilot_bar', 'muukal_trustpilot_bar_shortcode' );
