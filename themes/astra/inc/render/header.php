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
	$social_icons = array(
		array(
			'label' => 'Facebook',
			'icon'  => '<svg viewBox="0 0 1024 1024" aria-hidden="true"><path d="M1024 512c0-282.763636-229.236364-512-512-512C229.236364 0 0 229.236364 0 512s229.236364 512 512 512C794.763636 1024 1024 794.763636 1024 512zM374.504727 512 374.504727 414.021818l60.043636 0L434.548364 354.769455c0-79.918545 23.877818-137.495273 111.383273-137.495273l104.075636 0 0 97.745455-73.262545 0c-36.724364 0-45.056 24.389818-45.056 49.943273l0 49.058909 112.919273 0L629.201455 512l-97.512727 0 0 295.517091L434.548364 807.517091 434.548364 512 374.504727 512z"></path></svg>',
		),
		array(
			'label' => 'Instagram',
			'icon'  => '<svg viewBox="0 0 1024 1024" aria-hidden="true"><path d="M512 306.9c-113.5 0-205.1 91.6-205.1 205.1S398.5 717.1 512 717.1 717.1 625.5 717.1 512 625.5 306.9 512 306.9z m0 338.4c-73.4 0-133.3-59.9-133.3-133.3S438.6 378.7 512 378.7 645.3 438.6 645.3 512 585.4 645.3 512 645.3zM725.5 250.7c-26.5 0-47.9 21.4-47.9 47.9s21.4 47.9 47.9 47.9 47.9-21.3 47.9-47.9c-0.1-26.6-21.4-47.9-47.9-47.9z"></path><path d="M911.8 512c0-55.2 0.5-109.9-2.6-165-3.1-64-17.7-120.8-64.5-167.6-46.9-46.9-103.6-61.4-167.6-64.5-55.2-3.1-109.9-2.6-165-2.6-55.2 0-109.9-0.5-165 2.6-64 3.1-120.8 17.7-167.6 64.5C132.6 226.3 118.1 283 115 347c-3.1 55.2-2.6 109.9-2.6 165s-0.5 109.9 2.6 165c3.1 64 17.7 120.8 64.5 167.6 46.9 46.9 103.6 61.4 167.6 64.5 55.2 3.1 109.9 2.6 165 2.6 55.2 0 109.9 0.5 165-2.6 64-3.1 120.8-17.7 167.6-64.5 46.9-46.9 61.4-103.6 64.5-167.6 3.2-55.1 2.6-109.8 2.6-165z m-88 235.8c-7.3 18.2-16.1 31.8-30.2 45.8-14.1 14.1-27.6 22.9-45.8 30.2C695.2 844.7 570.3 840 512 840c-58.3 0-183.3 4.7-235.9-16.1-18.2-7.3-31.8-16.1-45.8-30.2-14.1-14.1-22.9-27.6-30.2-45.8C179.3 695.2 184 570.3 184 512c0-58.3-4.7-183.3 16.1-235.9 7.3-18.2 16.1-31.8 30.2-45.8s27.6-22.9 45.8-30.2C328.7 179.3 453.7 184 512 184s183.3-4.7 235.9 16.1c18.2 7.3 31.8 16.1 45.8 30.2 14.1 14.1 22.9 27.6 30.2 45.8C844.7 328.7 840 453.7 840 512c0 58.3 4.7 183.2-16.2 235.8z"></path></svg>',
		),
		array(
			'label' => 'Pinterest',
			'icon'  => '<svg viewBox="0 0 1024 1024" aria-hidden="true"><path d="M408.746667 765.056c-7.808 0-15.658667-1.578667-23.466667-6.4a48.213333 48.213333 0 0 1-23.466667-41.386667V350.976c0-17.493333 9.386667-33.450667 23.466667-41.386667a49.066667 49.066667 0 0 1 46.933333 0l311.338667 183.125334c14.08 7.978667 23.466667 23.893333 23.466667 41.386666 0 17.536-9.386667 33.450667-23.466667 41.429334l-311.338667 183.168c-7.808 3.2-15.658667 6.4-23.466666 6.4z m46.933333-332.842667v200.661334l170.538667-100.352-170.538667-100.309334z"></path><path d="M821.76 938.666667H203.818667C114.645333 938.666667 42.666667 865.408 42.666667 774.613333V292.053333C42.666667 201.258667 114.645333 128 203.818667 128H821.76C909.354667 128 981.333333 201.258667 981.333333 292.053333v484.181334C981.333333 865.365333 909.354667 938.666667 821.76 938.666667zM203.818667 223.573333c-37.546667 0-67.285333 30.250667-67.285334 68.48v484.181334c0 38.186667 29.738667 68.48 67.285334 68.48H821.76c37.546667 0 67.285333-30.293333 67.285333-68.48V292.010667c-1.578667-38.229333-31.317333-68.48-67.285333-68.48H203.818667z"></path></svg>',
		),
	);

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
					<div class="muukal-header__socials" aria-label="<?php esc_attr_e( 'Social links', 'astra' ); ?>">
						<?php foreach ( $social_icons as $social_icon ) : ?>
							<a class="muukal-header__social-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( $social_icon['label'] ); ?>">
								<?php echo $social_icon['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php endforeach; ?>
					</div>
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
