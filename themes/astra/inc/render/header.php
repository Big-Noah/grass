<?php
/**
 * Muukal header markup.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the custom header.
 *
 * @param array $args Header arguments.
 * @return string
 */
function muukal_render_header( $args ) {
	$menu_location = isset( $args['menu_location'] ) ? $args['menu_location'] : 'primary';
	$promo_text    = isset( $args['promo_text'] ) ? $args['promo_text'] : '';
	$promo_link    = isset( $args['promo_link'] ) ? $args['promo_link'] : home_url( '/' );
	$search_text   = isset( $args['search_text'] ) ? $args['search_text'] : 'Search products';
	$account_label = isset( $args['account_label'] ) ? $args['account_label'] : 'Login/Register';
	$mobile_label  = isset( $args['mobile_label'] ) ? $args['mobile_label'] : 'Menu';

	$menu_locations = get_nav_menu_locations();
	$has_menu       = isset( $menu_locations[ $menu_location ] );

	$account_url = wp_login_url();
	if ( function_exists( 'wc_get_page_permalink' ) && 'myaccount' !== wc_get_page_permalink( 'myaccount' ) ) {
		$account_url = wc_get_page_permalink( 'myaccount' );
	}

	$wishlist_url = home_url( '/wishlist/' );
	$cart_url     = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
	$logo_id      = get_theme_mod( 'custom_logo' );
	$logo_markup  = $logo_id ? wp_get_attachment_image( $logo_id, 'full', false, array( 'class' => 'muukal-header__logo-image' ) ) : '';
	$promo_icon   = '<span class="muukal-header__promo-icon" aria-hidden="true"></span>';
	$search_icon  = '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6.5"></circle><path d="M16 16l5 5"></path></svg>';
	$heart_icon   = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s-6.7-4.35-9.18-8.18C.92 9.87 2.08 5.88 5.86 5.2c2.07-.37 3.74.57 4.94 2.01 1.2-1.44 2.87-2.38 4.94-2.01 3.78.68 4.94 4.67 3.04 7.62C18.7 16.65 12 21 12 21z"></path></svg>';
	$cart_icon    = '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="20" r="1.5"></circle><circle cx="18" cy="20" r="1.5"></circle><path d="M3 4h2l2.4 10.2a1 1 0 0 0 1 .8h9.7a1 1 0 0 0 1-.76L21 7H7"></path></svg>';

	ob_start();
	?>
	<header class="muukal-header" data-muukal-header>
		<div class="muukal-header__promo">
			<div class="muukal-header__inner">
				<a class="muukal-header__promo-link" href="<?php echo esc_url( $promo_link ); ?>">
					<?php echo $promo_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo esc_html( $promo_text ); ?>
				</a>
				<div class="muukal-header__utilities muukal-header__utilities--top">
					<a class="muukal-header__utility-link" href="<?php echo esc_url( $account_url ); ?>"><?php echo esc_html( $account_label ); ?></a>
					<a class="muukal-header__utility-icon" href="<?php echo esc_url( $wishlist_url ); ?>" aria-label="<?php esc_attr_e( 'Wishlist', 'astra' ); ?>">
						<?php echo $heart_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
					<a class="muukal-header__utility-icon" href="<?php echo esc_url( $cart_url ); ?>" aria-label="<?php esc_attr_e( 'Cart', 'astra' ); ?>">
						<?php echo $cart_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				</div>
			</div>
		</div>

		<div class="muukal-header__main">
			<div class="muukal-header__inner">
				<div class="muukal-header__brand">
					<a class="muukal-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
						<?php if ( $logo_markup ) : ?>
							<?php echo $logo_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php else : ?>
							<span class="muukal-header__logo-text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
						<?php endif; ?>
					</a>
				</div>

				<div class="muukal-header__nav-wrap">
					<?php if ( $has_menu ) : ?>
						<nav class="muukal-header__nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'astra' ); ?>">
							<?php
							wp_nav_menu(
								array(
									'theme_location' => $menu_location,
									'container'      => false,
									'menu_class'     => 'muukal-header__menu',
									'fallback_cb'    => false,
									'depth'          => 3,
								)
							);
							?>
						</nav>
					<?php endif; ?>
				</div>

				<div class="muukal-header__utilities">
					<button class="muukal-header__search-toggle" type="button" data-muukal-search-toggle aria-expanded="false" aria-controls="muukal-header-search">
						<span class="muukal-header__search-text"><?php echo esc_html( $search_text ); ?></span>
						<span class="muukal-header__search-icon" aria-hidden="true">
							<?php echo $search_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
					</button>
					<a class="muukal-header__hamburger" href="<?php echo esc_url( $wishlist_url ); ?>" aria-label="<?php esc_attr_e( 'Wishlist', 'astra' ); ?>">
						<?php echo $heart_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
					<a class="muukal-header__hamburger" href="<?php echo esc_url( $cart_url ); ?>" aria-label="<?php esc_attr_e( 'Cart', 'astra' ); ?>">
						<?php echo $cart_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
					<button class="muukal-header__mobile-toggle" type="button" data-muukal-menu-toggle aria-expanded="false" aria-controls="muukal-header-mobile-menu">
						<span class="screen-reader-text"><?php echo esc_html( $mobile_label ); ?></span>
						<span></span>
						<span></span>
						<span></span>
					</button>
				</div>
			</div>
		</div>

		<div class="muukal-header__search-panel" id="muukal-header-search" hidden>
			<div class="muukal-header__inner">
				<?php if ( function_exists( 'get_product_search_form' ) ) : ?>
					<?php get_product_search_form(); ?>
				<?php else : ?>
					<?php get_search_form(); ?>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $has_menu ) : ?>
			<div class="muukal-header__mobile-panel" id="muukal-header-mobile-menu" hidden>
				<div class="muukal-header__inner">
					<nav class="muukal-header__mobile-nav" aria-label="<?php esc_attr_e( 'Mobile navigation', 'astra' ); ?>">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => $menu_location,
								'container'      => false,
								'menu_class'     => 'muukal-header__mobile-menu',
								'fallback_cb'    => false,
								'depth'          => 3,
							)
						);
						?>
					</nav>
				</div>
			</div>
		<?php endif; ?>
	</header>
	<?php

	return ob_get_clean();
}
