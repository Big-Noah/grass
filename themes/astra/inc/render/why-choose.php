<?php
/**
 * Muukal why choose section markup.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get default item data for the why choose section.
 *
 * @return array<int, array<string, string>>
 */
function muukal_why_choose_default_items() {
	return array(
		array(
			'icon'        => 'https://img.muukal.com/img/index/wcm_1.png',
			'title'       => 'Delivery',
			'description' => 'Free standard shipping on usd 65+, fast delivery within 3-7 business days.',
		),
		array(
			'icon'        => 'https://img.muukal.com/img/index/wcm_2.png',
			'title'       => 'Product',
			'description' => 'All glasses come with a 90-days quality guarantee.',
		),
		array(
			'icon'        => 'https://img.muukal.com/img/index/wcm_4.png',
			'title'       => 'Gift',
			'description' => 'Glasses case, eyeglasses cloth, dedicated screwdriver for fixing glasses and more.',
		),
		array(
			'icon'        => 'https://img.muukal.com/img/index/wcm_3.png',
			'title'       => 'Money Return',
			'description' => '100% Money Back Guarantee, no reason for return.',
		),
	);
}

/**
 * Build a why choose item from shortcode arguments.
 *
 * @param array<string, mixed>  $args    Shortcode arguments.
 * @param int                   $index   One-based item index.
 * @param array<string, string> $default Default item values.
 * @return array<string, string>
 */
function muukal_why_choose_build_item( $args, $index, $default ) {
	$icon_key        = 'icon_' . $index;
	$title_key       = 'title_' . $index;
	$description_key = 'description_' . $index;

	$icon        = ! empty( $args[ $icon_key ] ) ? esc_url_raw( $args[ $icon_key ] ) : $default['icon'];
	$title       = ! empty( $args[ $title_key ] ) ? sanitize_text_field( $args[ $title_key ] ) : $default['title'];
	$description = ! empty( $args[ $description_key ] ) ? sanitize_text_field( $args[ $description_key ] ) : $default['description'];

	return array(
		'icon'        => $icon,
		'title'       => $title,
		'description' => $description,
	);
}

/**
 * Render the why choose section.
 *
 * @param array<string, mixed> $args Shortcode attributes.
 * @return string
 */
function muukal_render_why_choose( $args ) {
	$heading                = isset( $args['heading'] ) ? sanitize_text_field( $args['heading'] ) : 'Why choose Muukal';
	$container_width        = isset( $args['container_width'] ) ? sanitize_text_field( $args['container_width'] ) : '1900px';
	$padding_top            = isset( $args['padding_top'] ) ? sanitize_text_field( $args['padding_top'] ) : '75px';
	$trustpilot_enabled     = isset( $args['trustpilot_enabled'] ) ? filter_var( $args['trustpilot_enabled'], FILTER_VALIDATE_BOOLEAN ) : true;
	$trustpilot_locale      = isset( $args['trustpilot_locale'] ) ? sanitize_text_field( $args['trustpilot_locale'] ) : 'en-US';
	$trustpilot_template_id = isset( $args['trustpilot_template_id'] ) ? sanitize_text_field( $args['trustpilot_template_id'] ) : '53aa8912dec7e10d38f59f36';
	$trustpilot_business_id = isset( $args['trustpilot_businessunit_id'] ) ? sanitize_text_field( $args['trustpilot_businessunit_id'] ) : '5f8d0532cc88c000013357a8';
	$trustpilot_height      = isset( $args['trustpilot_style_height'] ) ? sanitize_text_field( $args['trustpilot_style_height'] ) : '140px';
	$trustpilot_width       = isset( $args['trustpilot_style_width'] ) ? sanitize_text_field( $args['trustpilot_style_width'] ) : '100%';
	$trustpilot_theme       = isset( $args['trustpilot_theme'] ) ? sanitize_text_field( $args['trustpilot_theme'] ) : 'light';
	$trustpilot_stars       = isset( $args['trustpilot_stars'] ) ? sanitize_text_field( $args['trustpilot_stars'] ) : '4,5';
	$trustpilot_languages   = isset( $args['trustpilot_review_languages'] ) ? sanitize_text_field( $args['trustpilot_review_languages'] ) : 'en';
	$trustpilot_link        = isset( $args['trustpilot_link'] ) ? esc_url_raw( $args['trustpilot_link'] ) : 'https://www.trustpilot.com/review/muukal.com';
	$items                  = array();

	foreach ( muukal_why_choose_default_items() as $index => $default ) {
		$items[] = muukal_why_choose_build_item( $args, $index + 1, $default );
	}

	ob_start();
	?>
	<div class="pt-75 muukal-why-choose" style="--muukal-why-choose-pt: <?php echo esc_attr( $padding_top ); ?>;">
		<div class="container-fluid pl-55 pr-55" style="max-width: <?php echo esc_attr( $container_width ); ?>;">
			<div class="area-title text-center why-cm-bg">
				<h2><?php echo esc_html( $heading ); ?></h2>
				<div class="why-cm-box">
					<?php foreach ( $items as $item ) : ?>
						<div class="why-cm-item">
							<img src="<?php echo esc_url( $item['icon'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" loading="lazy" decoding="async">
							<div class="why-cm-text">
								<span class="why-cm-h"><?php echo esc_html( $item['title'] ); ?></span>
								<span class="why-cm-i"><?php echo esc_html( $item['description'] ); ?></span>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<?php if ( $trustpilot_enabled ) : ?>
					<div
						class="trustpilot-widget"
						data-locale="<?php echo esc_attr( $trustpilot_locale ); ?>"
						data-template-id="<?php echo esc_attr( $trustpilot_template_id ); ?>"
						data-businessunit-id="<?php echo esc_attr( $trustpilot_business_id ); ?>"
						data-style-height="<?php echo esc_attr( $trustpilot_height ); ?>"
						data-style-width="<?php echo esc_attr( $trustpilot_width ); ?>"
						data-theme="<?php echo esc_attr( $trustpilot_theme ); ?>"
						data-stars="<?php echo esc_attr( $trustpilot_stars ); ?>"
						data-review-languages="<?php echo esc_attr( $trustpilot_languages ); ?>"
					>
						<a href="<?php echo esc_url( $trustpilot_link ); ?>" target="_blank" rel="noopener">Trustpilot</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php

	return ob_get_clean();
}
