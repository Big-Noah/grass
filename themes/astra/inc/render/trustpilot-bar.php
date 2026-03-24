<?php
/**
 * Muukal Trustpilot bar markup.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the Trustpilot review bar.
 *
 * @param array $args Shortcode attributes.
 * @return string
 */
function muukal_render_trustpilot_bar( $args ) {
	$label           = isset( $args['label'] ) ? sanitize_text_field( $args['label'] ) : 'Excellent';
	$rating          = isset( $args['rating'] ) ? sanitize_text_field( $args['rating'] ) : '4.3';
	$reviews         = isset( $args['reviews'] ) ? sanitize_text_field( $args['reviews'] ) : '6,438 reviews';
	$prefix          = isset( $args['prefix'] ) ? sanitize_text_field( $args['prefix'] ) : '4.3 out of 5 based on';
	$brand           = isset( $args['brand'] ) ? sanitize_text_field( $args['brand'] ) : 'Trustpilot';
	$link            = isset( $args['link'] ) ? esc_url_raw( $args['link'] ) : 'https://www.trustpilot.com/review/muukal.com';
	$background      = isset( $args['background'] ) ? sanitize_text_field( $args['background'] ) : '#f3f3f3';
	$padding         = isset( $args['padding'] ) ? sanitize_text_field( $args['padding'] ) : '10px 0';
	$container_width = isset( $args['container_width'] ) ? sanitize_text_field( $args['container_width'] ) : '1400px';

	ob_start();
	?>
	<div
		class="muukal-trustpilot-bar"
		style="--muukal-trustpilot-bg: <?php echo esc_attr( $background ); ?>; --muukal-trustpilot-padding: <?php echo esc_attr( $padding ); ?>; --muukal-trustpilot-width: <?php echo esc_attr( $container_width ); ?>;"
	>
		<div class="muukal-trustpilot-bar__inner">
			<a class="muukal-trustpilot-bar__content" href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener">
				<span class="muukal-trustpilot-bar__label"><?php echo esc_html( $label ); ?></span>
				<span class="muukal-trustpilot-bar__stars" aria-hidden="true">
					<span class="muukal-trustpilot-bar__star is-filled"></span>
					<span class="muukal-trustpilot-bar__star is-filled"></span>
					<span class="muukal-trustpilot-bar__star is-filled"></span>
					<span class="muukal-trustpilot-bar__star is-filled"></span>
					<span class="muukal-trustpilot-bar__star is-half"></span>
				</span>
				<span class="muukal-trustpilot-bar__summary">
					<?php echo esc_html( $prefix ); ?>
					<span class="muukal-trustpilot-bar__reviews"><?php echo esc_html( $reviews ); ?></span>
				</span>
				<span class="muukal-trustpilot-bar__brand"><?php echo esc_html( $brand ); ?></span>
			</a>
		</div>
	</div>
	<?php

	return ob_get_clean();
}
