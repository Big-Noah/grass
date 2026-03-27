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
			'icon'  => '<svg viewBox="0 0 16 16" aria-hidden="true"><path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/></svg>',
		),
		array(
			'label' => 'Instagram',
			'icon'  => '<svg viewBox="0 0 16 16" aria-hidden="true"><path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/></svg>',
		),
		array(
			'label' => 'Pinterest',
			'icon'  => '<svg viewBox="0 0 16 16" aria-hidden="true"><path d="M8 0a8 8 0 0 0-2.915 15.452c-.07-.633-.134-1.606.027-2.297.146-.625.938-3.977.938-3.977s-.239-.479-.239-1.187c0-1.113.645-1.943 1.448-1.943.682 0 1.012.512 1.012 1.127 0 .686-.437 1.712-.663 2.663-.188.796.4 1.446 1.185 1.446 1.422 0 2.515-1.5 2.515-3.664 0-1.915-1.377-3.254-3.342-3.254-2.276 0-3.612 1.707-3.612 3.471 0 .688.265 1.425.595 1.826a.24.24 0 0 1 .056.23c-.061.252-.196.796-.222.907-.035.146-.116.177-.268.107-1-.465-1.624-1.926-1.624-3.1 0-2.523 1.834-4.84 5.286-4.84 2.775 0 4.932 1.977 4.932 4.62 0 2.757-1.739 4.976-4.151 4.976-.811 0-1.573-.421-1.834-.919l-.498 1.902c-.181.695-.669 1.566-.995 2.097A8 8 0 1 0 8 0"/></svg>',
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
