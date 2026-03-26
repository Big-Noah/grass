<?php
/**
 * Muukal why choose shortcode.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ASTRA_THEME_DIR . 'inc/render/why-choose.php';

/**
 * Register assets for the why choose shortcode.
 */
function muukal_why_choose_register_assets() {
	wp_register_style(
		'muukal-why-choose',
		ASTRA_THEME_URI . 'assets/css/why-choose.css',
		array(),
		ASTRA_THEME_VERSION
	);

	wp_register_script(
		'muukal-trustpilot-widget',
		'https://widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js',
		array(),
		null,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'muukal_why_choose_register_assets' );

/**
 * Render the why choose shortcode.
 *
 * @param array<string, string> $atts Shortcode attributes.
 * @return string
 */
function muukal_why_choose_shortcode( $atts = array() ) {
	$defaults = muukal_why_choose_default_items();

	$atts = shortcode_atts(
		array(
			'heading'                    => 'Why choose Muukal',
			'container_width'            => '1900px',
			'padding_top'                => '75px',
			'icon_1'                     => $defaults[0]['icon'],
			'title_1'                    => $defaults[0]['title'],
			'description_1'              => $defaults[0]['description'],
			'icon_2'                     => $defaults[1]['icon'],
			'title_2'                    => $defaults[1]['title'],
			'description_2'              => $defaults[1]['description'],
			'icon_3'                     => $defaults[2]['icon'],
			'title_3'                    => $defaults[2]['title'],
			'description_3'              => $defaults[2]['description'],
			'icon_4'                     => $defaults[3]['icon'],
			'title_4'                    => $defaults[3]['title'],
			'description_4'              => $defaults[3]['description'],
			'trustpilot_enabled'         => 'true',
			'trustpilot_locale'          => 'en-US',
			'trustpilot_template_id'     => '53aa8912dec7e10d38f59f36',
			'trustpilot_businessunit_id' => '5f8d0532cc88c000013357a8',
			'trustpilot_style_height'    => '140px',
			'trustpilot_style_width'     => '100%',
			'trustpilot_theme'           => 'light',
			'trustpilot_stars'           => '4,5',
			'trustpilot_review_languages'=> 'en',
			'trustpilot_link'            => 'https://www.trustpilot.com/review/muukal.com',
		),
		$atts,
		'muukal_why_choose'
	);

	wp_enqueue_style( 'muukal-why-choose' );

	if ( filter_var( $atts['trustpilot_enabled'], FILTER_VALIDATE_BOOLEAN ) ) {
		wp_enqueue_script( 'muukal-trustpilot-widget' );
	}

	return muukal_render_why_choose( $atts );
}
add_shortcode( 'muukal_why_choose', 'muukal_why_choose_shortcode' );
