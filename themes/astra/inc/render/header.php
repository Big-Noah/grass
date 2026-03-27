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
 * Get current wishlist count.
 *
 * @return int
 */
function muukal_header_get_wishlist_count() {
	if ( function_exists( 'muukal_wishlist_get_items' ) ) {
		return count( muukal_wishlist_get_items() );
	}

	return 0;
}

/**
 * Get current cart count.
 *
 * @return int
 */
function muukal_header_get_cart_count() {
	if ( function_exists( 'WC' ) && WC()->cart ) {
		return (int) WC()->cart->get_cart_contents_count();
	}

	return 0;
}

/**
 * Check whether a menu item is inside the Shop By Color column.
 *
 * @param int   $menu_item_id Menu item post ID.
 * @param array $menu_tree    Indexed menu tree.
 * @return bool
 */
function muukal_header_is_shop_by_color_descendant( $menu_item_id, $menu_tree ) {
	$current_id = (int) $menu_item_id;

	while ( $current_id && isset( $menu_tree[ $current_id ] ) ) {
		$item = $menu_tree[ $current_id ];

		if ( isset( $item['title'] ) && 'shop by color' === strtolower( trim( wp_strip_all_tags( $item['title'] ) ) ) ) {
			return true;
		}

		$current_id = isset( $item['parent'] ) ? (int) $item['parent'] : 0;
	}

	return false;
}

/**
 * Build a lightweight tree of header menu items for contextual styling.
 *
 * @param stdClass $args Menu arguments.
 * @return array<int, array<string, mixed>>
 */
function muukal_header_get_menu_tree( $args ) {
	static $cache = array();

	$tree           = array();
	$cache_key      = '';
	$menu_id        = 0;

	if ( ! empty( $args->theme_location ) ) {
		$cache_key      = 'location:' . $args->theme_location;
		$menu_locations = get_nav_menu_locations();
		$menu_id        = isset( $menu_locations[ $args->theme_location ] ) ? (int) $menu_locations[ $args->theme_location ] : 0;
	} elseif ( ! empty( $args->menu ) ) {
		$menu_object = wp_get_nav_menu_object( $args->menu );
		$menu_id     = $menu_object ? (int) $menu_object->term_id : 0;
		$cache_key   = 'menu:' . $menu_id;
	}

	if ( $cache_key && isset( $cache[ $cache_key ] ) ) {
		return $cache[ $cache_key ];
	}

	if ( ! $menu_id ) {
		if ( $cache_key ) {
			$cache[ $cache_key ] = $tree;
		}
		return $tree;
	}

	$items = wp_get_nav_menu_items( $menu_id );

	if ( empty( $items ) || ! is_array( $items ) ) {
		if ( $cache_key ) {
			$cache[ $cache_key ] = $tree;
		}
		return $tree;
	}

	foreach ( $items as $item ) {
		$tree[ (int) $item->ID ] = array(
			'title'  => $item->title,
			'parent' => (int) $item->menu_item_parent,
		);
	}

	if ( $cache_key ) {
		$cache[ $cache_key ] = $tree;
	}

	return $tree;
}

/**
 * Add contextual classes to the custom header menu.
 *
 * @param string[]  $classes Existing CSS classes.
 * @param WP_Post   $item Menu item object.
 * @param stdClass  $args Menu arguments.
 * @param int       $depth Item depth.
 * @return string[]
 */
function muukal_header_nav_menu_css_class( $classes, $item, $args, $depth ) {
	$menu_class = ! empty( $args->menu_class ) ? ' ' . $args->menu_class . ' ' : '';

	if ( false === strpos( $menu_class, ' muukal-header__menu ' ) ) {
		return $classes;
	}

	$classes   = is_array( $classes ) ? $classes : array();
	$menu_tree = muukal_header_get_menu_tree( $args );
	$title     = strtolower( trim( wp_strip_all_tags( $item->title ) ) );

	if ( 0 === (int) $depth && in_array( 'menu-item-has-children', $classes, true ) ) {
		$classes[] = 'muukal-header__menu-item--mega';
	}

	if ( 1 === (int) $depth ) {
		$classes[] = 'muukal-header__mega-column';

		if ( 'shop by color' === $title ) {
			$classes[] = 'muukal-header__mega-column--colors';
		}
	}

	if ( 2 === (int) $depth && muukal_header_is_shop_by_color_descendant( $item->ID, $menu_tree ) ) {
		$classes[] = 'muukal-header__color-item';
		$classes[] = 'muukal-header__color-item--' . sanitize_html_class( sanitize_title( $title ) );
	}

	return array_values( array_unique( $classes ) );
}
add_filter( 'nav_menu_css_class', 'muukal_header_nav_menu_css_class', 10, 4 );

/**
 * Add contextual link attributes to the custom header menu.
 *
 * @param array     $atts Link attributes.
 * @param WP_Post   $item Menu item object.
 * @param stdClass  $args Menu arguments.
 * @param int       $depth Item depth.
 * @return array
 */
function muukal_header_nav_menu_link_attributes( $atts, $item, $args, $depth ) {
	$menu_class = ! empty( $args->menu_class ) ? ' ' . $args->menu_class . ' ' : '';

	if ( false === strpos( $menu_class, ' muukal-header__menu ' ) ) {
		return $atts;
	}

	$menu_tree = muukal_header_get_menu_tree( $args );

	if ( 2 === (int) $depth && muukal_header_is_shop_by_color_descendant( $item->ID, $menu_tree ) ) {
		$atts['data-muukal-color-label'] = sanitize_title( wp_strip_all_tags( $item->title ) );
	}

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'muukal_header_nav_menu_link_attributes', 10, 4 );

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

	$wishlist_url = function_exists( 'muukal_wishlist_get_account_url' ) ? muukal_wishlist_get_account_url() : home_url( '/wishlist/' );
	$cart_url     = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
	$wishlist_count = muukal_header_get_wishlist_count();
	$cart_count     = muukal_header_get_cart_count();
	$logo_id      = get_theme_mod( 'custom_logo' );
	$logo_markup  = $logo_id ? wp_get_attachment_image( $logo_id, 'full', false, array( 'class' => 'muukal-header__logo-image' ) ) : '';
	$promo_icon   = '<span class="muukal-header__promo-icon" aria-hidden="true"></span>';
	$search_icon  = '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6.5"></circle><path d="M16 16l5 5"></path></svg>';
	$account_icon = '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8.2" r="3.2"></circle><path d="M5 19c1.9-3 4.3-4.5 7-4.5s5.1 1.5 7 4.5"></path></svg>';
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
					<a class="muukal-header__action-icon" href="<?php echo esc_url( $account_url ); ?>" aria-label="<?php esc_attr_e( 'My Account', 'astra' ); ?>">
						<?php echo $account_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
					<a class="muukal-header__hamburger" href="<?php echo esc_url( $wishlist_url ); ?>" aria-label="<?php esc_attr_e( 'Wishlist', 'astra' ); ?>">
						<?php echo $heart_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span class="muukal-header__count-badge<?php echo $wishlist_count > 0 ? '' : ' is-empty'; ?>" data-muukal-wishlist-count><?php echo esc_html( (string) $wishlist_count ); ?></span>
					</a>
					<a class="muukal-header__hamburger" href="<?php echo esc_url( $cart_url ); ?>" aria-label="<?php esc_attr_e( 'Cart', 'astra' ); ?>">
						<?php echo $cart_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span class="muukal-header__count-badge<?php echo $cart_count > 0 ? '' : ' is-empty'; ?>" data-muukal-cart-count><?php echo esc_html( (string) $cart_count ); ?></span>
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
