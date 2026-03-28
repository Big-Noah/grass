<?php
/**
 * Muukal footer markup.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the default footer service cards.
 *
 * @return array<int, array<string, string>>
 */
function muukal_footer_default_service_items() {
	return array(
		array(
			'image' => 'ship_icon_1_v1.png',
			'title' => 'Delivery',
			'text'  => 'Free standard shipping on usd 65+, fast delivery within 3-7 business days',
		),
		array(
			'image' => 'ship_icon_2_v1.png',
			'title' => 'Products',
			'text'  => 'All glasses come with a 90-days quality guarantee',
		),
		array(
			'image' => 'ship_icon_3_v1.png',
			'title' => 'Gift',
			'text'  => 'Glasses case, eyeglasses cloth, dedicated screwdriver for fixing glasses and more',
		),
		array(
			'image' => 'ship_icon_4_v1.png',
			'title' => 'Money Return',
			'text'  => '100% Money Back Guarantee, no reason for return',
		),
	);
}

/**
 * Get the default footer link groups.
 *
 * @return array<int, array<string, mixed>>
 */
function muukal_footer_default_link_groups() {
	return array(
		array(
			'title' => 'SHOP',
			'items' => array(
				'Women Eyeglasses',
				'Men Eyeglasses',
				'Round Glasses',
				'Oval Glasses',
				'Rectangular Glasses',
				'Cateye Glasses',
			),
		),
		array(
			'title' => 'POLICIES',
			'items' => array(
				'Shipping & Tracking',
				'Return & Refund',
				'Privacy & Security',
				'Terms & Conditions',
				'Intellectual Property Rights',
			),
		),
		array(
			'title' => 'HELP & INFO',
			'items' => array(
				'FAQS',
				'Payment Method',
				'Lenses and Coatings',
				'How to Place Order',
				'How To Measure Your Pd',
				'Choose Your Frame',
				'Choose Your Lens Type',
				'How To Adjust Your Eyeglasses',
			),
		),
		array(
			'title' => 'COMPANY',
			'items' => array(
				'About US',
				'Contact US',
				'Wholesale',
				'Order Tracking',
			),
		),
		array(
			'title' => 'Deals',
			'items' => array(
				'BOGO',
				'3 pairs for $119',
				'First Pair Free',
				'Clearance',
			),
		),
	);
}

/**
 * Get the footer social links.
 *
 * @return array<int, array<string, string>>
 */
function muukal_footer_social_links() {
	return array(
		array(
			'label' => 'Facebook',
			'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.5 22v-8h2.8l.42-3.27h-3.22V8.64c0-.95.26-1.6 1.63-1.6h1.74V4.11c-.3-.04-1.34-.11-2.56-.11-2.53 0-4.27 1.55-4.27 4.39v2.34H7.5V14h3.12v8h2.88Z"></path></svg>',
		),
		array(
			'label' => 'Twitter',
			'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18.55 7.78c.01.18.01.37.01.56 0 5.72-4.36 12.31-12.31 12.31A12.2 12.2 0 0 1 0 18.8a8.66 8.66 0 0 0 1.01.06 8.64 8.64 0 0 0 5.35-1.84 4.32 4.32 0 0 1-4.03-2.99 4.37 4.37 0 0 0 1.95-.08 4.32 4.32 0 0 1-3.46-4.24v-.05c.58.32 1.25.52 1.96.55A4.31 4.31 0 0 1 .84 6.61c0-.8.21-1.54.59-2.18a12.28 12.28 0 0 0 8.92 4.52 4.32 4.32 0 0 1 7.35-3.94 8.56 8.56 0 0 0 2.74-1.05 4.31 4.31 0 0 1-1.9 2.38 8.6 8.6 0 0 0 2.48-.68 9.28 9.28 0 0 1-2.16 2.12Z"></path></svg>',
		),
		array(
			'label' => 'Instagram',
			'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7.5 2h9A5.5 5.5 0 0 1 22 7.5v9A5.5 5.5 0 0 1 16.5 22h-9A5.5 5.5 0 0 1 2 16.5v-9A5.5 5.5 0 0 1 7.5 2Zm0 1.8A3.7 3.7 0 0 0 3.8 7.5v9a3.7 3.7 0 0 0 3.7 3.7h9a3.7 3.7 0 0 0 3.7-3.7v-9a3.7 3.7 0 0 0-3.7-3.7h-9Zm9.65 1.35a1.05 1.05 0 1 1 0 2.1 1.05 1.05 0 0 1 0-2.1ZM12 6.86A5.14 5.14 0 1 1 6.86 12 5.14 5.14 0 0 1 12 6.86Zm0 1.8A3.34 3.34 0 1 0 15.34 12 3.34 3.34 0 0 0 12 8.66Z"></path></svg>',
		),
		array(
			'label' => 'Pinterest',
			'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12.37 2C6.98 2 4 5.63 4 9.58c0 2.34.88 4.42 2.76 5.19.31.13.58.01.67-.34.06-.24.21-.86.28-1.11.09-.34.05-.46-.2-.76-.54-.64-.89-1.47-.89-2.64 0-3.4 2.54-6.44 6.61-6.44 3.6 0 5.58 2.2 5.58 5.13 0 3.86-1.71 7.11-4.25 7.11-1.4 0-2.45-1.15-2.12-2.57.41-1.69 1.19-3.51 1.19-4.73 0-1.09-.58-2-1.8-2-1.43 0-2.57 1.48-2.57 3.46 0 1.26.43 2.12.43 2.12l-1.73 7.34c-.51 2.18-.08 4.85-.04 5.12.02.16.22.2.31.08.13-.17 1.79-2.22 2.36-4.26.16-.58.92-3.58.92-3.58.46.88 1.8 1.65 3.22 1.65 4.24 0 7.12-3.87 7.12-9.06C20.72 5.66 16.85 2 12.37 2Z"></path></svg>',
		),
	);
}

/**
 * Render the custom footer.
 *
 * @param array $args Footer shortcode attributes.
 * @return string
 */
function muukal_render_footer( $args ) {
	$link_url           = isset( $args['link_url'] ) && $args['link_url'] ? esc_url( $args['link_url'] ) : esc_url( home_url( '/' ) );
	$container_width    = isset( $args['container_width'] ) ? sanitize_text_field( $args['container_width'] ) : '1400px';
	$show_service_strip = isset( $args['show_service_strip'] ) ? filter_var( $args['show_service_strip'], FILTER_VALIDATE_BOOLEAN ) : true;
	$show_newsletter    = isset( $args['show_newsletter'] ) ? filter_var( $args['show_newsletter'], FILTER_VALIDATE_BOOLEAN ) : true;
	$support_text       = isset( $args['support_text'] ) ? sanitize_text_field( $args['support_text'] ) : 'Have a question? Contact Customer Service Department';
	$support_email      = isset( $args['support_email'] ) ? sanitize_email( $args['support_email'] ) : 'service@mdoniver.com';
	$newsletter_title   = isset( $args['newsletter_title'] ) ? sanitize_text_field( $args['newsletter_title'] ) : 'Sign up to Newsletter';
	$newsletter_text    = isset( $args['newsletter_text'] ) ? sanitize_text_field( $args['newsletter_text'] ) : 'Be the first to get our best offers & new products';
	$newsletter_cta     = isset( $args['newsletter_cta'] ) ? sanitize_text_field( $args['newsletter_cta'] ) : 'subscribe';
	$newsletter_hint    = isset( $args['newsletter_hint'] ) ? sanitize_text_field( $args['newsletter_hint'] ) : 'Enter Your Email';
	$copyright_name     = isset( $args['copyright_name'] ) ? sanitize_text_field( $args['copyright_name'] ) : 'MuukalOptical Online Store.';
	$current_year       = function_exists( 'wp_date' ) ? wp_date( 'Y' ) : gmdate( 'Y' );

	$asset_base    = ASTRA_THEME_URI . 'assets/images/footer/';
	$service_items = muukal_footer_default_service_items();
	$link_groups   = muukal_footer_default_link_groups();
	$social_links  = muukal_footer_social_links();
	$star_icon     = '<svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.75l2.55 5.17 5.71.83-4.13 4.03.98 5.69L10 14.79l-5.11 2.68.98-5.69L1.74 7.75l5.71-.83L10 1.75Z"></path></svg>';

	ob_start();
	?>
	<section class="muukal-footer-block" style="--muukal-footer-container: <?php echo esc_attr( $container_width ); ?>;">
		<?php if ( $show_service_strip ) : ?>
			<div class="muukal-footer-block__services">
				<div class="muukal-footer-block__inner">
					<div class="muukal-footer-block__service-grid">
						<?php foreach ( $service_items as $item ) : ?>
							<article class="muukal-footer-block__service-card">
								<div class="muukal-footer-block__service-icon">
									<img src="<?php echo esc_url( $asset_base . $item['image'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" loading="lazy" decoding="async">
								</div>
								<div class="muukal-footer-block__service-copy">
									<h3><?php echo esc_html( $item['title'] ); ?></h3>
									<p><?php echo esc_html( $item['text'] ); ?></p>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $show_newsletter ) : ?>
			<div class="muukal-footer-block__newsletter">
				<div class="muukal-footer-block__inner">
					<div class="muukal-footer-block__newsletter-head">
						<h2><?php echo esc_html( $newsletter_title ); ?></h2>
						<p><?php echo esc_html( $newsletter_text ); ?></p>
					</div>
					<form class="muukal-footer-block__newsletter-form" action="#" method="post" onsubmit="return false;">
						<label class="screen-reader-text" for="muukal-footer-email"><?php esc_html_e( 'Email address', 'astra' ); ?></label>
						<input id="muukal-footer-email" type="email" placeholder="<?php echo esc_attr( $newsletter_hint ); ?>">
						<button type="submit">
							<?php echo esc_html( $newsletter_cta ); ?>
							<span aria-hidden="true">
								<svg viewBox="0 0 20 20"><path d="M3 10h11.5"></path><path d="M10.5 4.5 16 10l-5.5 5.5"></path></svg>
							</span>
						</button>
					</form>
				</div>
			</div>
		<?php endif; ?>

		<footer class="muukal-footer" aria-label="<?php esc_attr_e( 'Site footer', 'astra' ); ?>">
			<div class="muukal-footer-block__inner">
				<div class="muukal-footer__main">
					<div class="muukal-footer__brand">
						<a class="muukal-footer__logo" href="<?php echo $link_url; ?>">
							<img src="<?php echo esc_url( $asset_base . 'logo_n.png' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" loading="lazy" decoding="async">
						</a>

						<div class="muukal-footer__support">
							<p><?php echo esc_html( $support_text ); ?></p>
							<a href="<?php echo $link_url; ?>"><?php echo esc_html( $support_email ); ?></a>
						</div>

						<div class="muukal-footer__ratings">
							<a class="muukal-footer__trustpilot" href="<?php echo $link_url; ?>">
								<span class="muukal-footer__trustpilot-stars" aria-hidden="true">
									<?php
									for ( $i = 0; $i < 5; $i++ ) {
										echo $star_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									}
									?>
								</span>
								<span class="muukal-footer__trustpilot-copy">
									<strong>Excellent</strong>
									<small>Trustpilot</small>
								</span>
							</a>

							<a class="muukal-footer__google" href="<?php echo $link_url; ?>">
								<img src="<?php echo esc_url( $asset_base . 'google-4-5.png' ); ?>" alt="<?php esc_attr_e( 'Google rating', 'astra' ); ?>" loading="lazy" decoding="async">
							</a>
						</div>

						<div class="muukal-footer__socials">
							<?php foreach ( $social_links as $item ) : ?>
								<a href="<?php echo $link_url; ?>" aria-label="<?php echo esc_attr( $item['label'] ); ?>">
									<?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</a>
							<?php endforeach; ?>
						</div>
					</div>

					<div class="muukal-footer__links">
						<?php foreach ( $link_groups as $group ) : ?>
							<nav class="muukal-footer__column" aria-label="<?php echo esc_attr( $group['title'] ); ?>">
								<h3><?php echo esc_html( $group['title'] ); ?></h3>
								<ul>
									<?php foreach ( $group['items'] as $item ) : ?>
										<li><a href="<?php echo $link_url; ?>"><?php echo esc_html( $item ); ?></a></li>
									<?php endforeach; ?>
								</ul>
							</nav>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="muukal-footer__bottom">
					<p>Copyright &copy; <?php echo esc_html( $current_year ); ?> <a href="<?php echo $link_url; ?>"><?php echo esc_html( $copyright_name ); ?></a> All Rights Reserved</p>
					<a class="muukal-footer__payments" href="<?php echo $link_url; ?>">
						<img src="<?php echo esc_url( $asset_base . 'footer_icon_new_2510.jpg' ); ?>" alt="<?php esc_attr_e( 'Payment methods', 'astra' ); ?>" loading="lazy" decoding="async">
					</a>
				</div>
			</div>
		</footer>
	</section>
	<?php

	return ob_get_clean();
}
