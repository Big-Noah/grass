<?php
/**
 * Muukal footer shortcode.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ASTRA_THEME_DIR . 'inc/render/footer.php';

/**
 * Register assets for the Muukal footer.
 */
function muukal_footer_register_assets() {
	wp_register_style(
		'muukal-footer',
		ASTRA_THEME_URI . 'assets/css/footer.css',
		array(),
		ASTRA_THEME_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'muukal_footer_register_assets' );

/**
 * Render the footer shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function muukal_footer_shortcode( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'link_url'           => home_url( '/' ),
			'container_width'    => '1400px',
			'show_service_strip' => 'true',
			'show_newsletter'    => 'true',
			'support_text'       => 'Have a question? Contact Customer Service Department',
			'support_email'      => 'service@muukal.com',
			'newsletter_title'   => 'Sign up to Newsletter',
			'newsletter_text'    => 'Be the first to get our best offers & new products',
			'newsletter_cta'     => 'subscribe',
			'newsletter_hint'    => 'Enter Your Email',
			'copyright_name'     => 'MuukalOptical Online Store.',
		),
		$atts,
		'muukal_footer'
	);

	wp_enqueue_style( 'muukal-footer' );

	return muukal_render_footer( $atts );
}
add_shortcode( 'muukal_footer', 'muukal_footer_shortcode' );
