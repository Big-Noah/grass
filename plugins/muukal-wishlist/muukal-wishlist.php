<?php
/**
 * Plugin Name: Muukal Wishlist
 * Description: Lightweight WooCommerce wishlist with auto-injected buttons, a dedicated wishlist page, and guest/login persistence.
 * Version: 0.1.0
 * Author: Codex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MUUKAL_WISHLIST_VERSION', '0.1.0' );
define( 'MUUKAL_WISHLIST_FILE', __FILE__ );
define( 'MUUKAL_WISHLIST_DIR', plugin_dir_path( __FILE__ ) );
define( 'MUUKAL_WISHLIST_URL', plugin_dir_url( __FILE__ ) );
define( 'MUUKAL_WISHLIST_OPTION', 'muukal_wishlist_settings' );
define( 'MUUKAL_WISHLIST_USER_META', 'muukal_wishlist_items' );
define( 'MUUKAL_WISHLIST_COOKIE', 'muukal_wishlist_items' );

/**
 * Get an asset version string.
 *
 * @param string $relative_path Asset path relative to plugin root.
 * @return string
 */
function muukal_wishlist_asset_version( $relative_path ) {
	$absolute_path = MUUKAL_WISHLIST_DIR . ltrim( $relative_path, '/\\' );

	if ( file_exists( $absolute_path ) ) {
		$modified = filemtime( $absolute_path );

		if ( false !== $modified ) {
			return (string) $modified;
		}
	}

	return MUUKAL_WISHLIST_VERSION;
}

/**
 * Default settings.
 *
 * @return array<string, mixed>
 */
function muukal_wishlist_default_settings() {
	return array(
		'button_label_add'      => 'Add to Wishlist',
		'button_label_added'    => 'Saved',
		'empty_heading'         => 'Your wishlist is empty.',
		'empty_text'            => 'Save frames you love and come back when you are ready to shop.',
		'page_title'            => 'Wishlist',
		'page_slug'             => 'wishlist',
		'auto_add_single'       => 'yes',
		'auto_add_loop'         => 'yes',
		'primary_color'         => '#ff7f90',
		'wishlist_page_id'      => 0,
	);
}

/**
 * Get plugin settings merged with defaults.
 *
 * @return array<string, mixed>
 */
function muukal_wishlist_get_settings() {
	$settings = get_option( MUUKAL_WISHLIST_OPTION, array() );
	$settings = is_array( $settings ) ? $settings : array();

	return wp_parse_args( $settings, muukal_wishlist_default_settings() );
}

/**
 * Sanitize wishlist item IDs.
 *
 * @param mixed $items Raw item list.
 * @return array<int, int>
 */
function muukal_wishlist_normalize_items( $items ) {
	if ( ! is_array( $items ) ) {
		return array();
	}

	$items = array_map( 'absint', $items );
	$items = array_filter(
		$items,
		static function ( $item_id ) {
			return $item_id > 0 && 'product' === get_post_type( $item_id );
		}
	);

	return array_values( array_unique( $items ) );
}

/**
 * Create or locate the wishlist page.
 *
 * @return int
 */
function muukal_wishlist_ensure_page() {
	$settings = muukal_wishlist_get_settings();
	$page_id  = absint( $settings['wishlist_page_id'] );

	if ( $page_id > 0 ) {
		$page = get_post( $page_id );

		if ( $page instanceof WP_Post && 'page' === $page->post_type && 'trash' !== $page->post_status ) {
			return $page_id;
		}
	}

	$existing = get_page_by_path( $settings['page_slug'] );

	if ( $existing instanceof WP_Post ) {
		$page_id = (int) $existing->ID;
	} else {
		$page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $settings['page_title'],
				'post_name'    => $settings['page_slug'],
				'post_content' => '[muukal_wishlist]',
			),
			true
		);

		if ( is_wp_error( $page_id ) ) {
			return 0;
		}
	}

	$settings['wishlist_page_id'] = absint( $page_id );
	update_option( MUUKAL_WISHLIST_OPTION, $settings );

	return absint( $page_id );
}

/**
 * Activation callback.
 */
function muukal_wishlist_activate() {
	if ( false === get_option( MUUKAL_WISHLIST_OPTION, false ) ) {
		add_option( MUUKAL_WISHLIST_OPTION, muukal_wishlist_default_settings() );
	}

	muukal_wishlist_ensure_page();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'muukal_wishlist_activate' );

/**
 * Deactivation callback.
 */
function muukal_wishlist_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'muukal_wishlist_deactivate' );

/**
 * Return the wishlist page ID.
 *
 * @return int
 */
function muukal_wishlist_get_page_id() {
	$settings = muukal_wishlist_get_settings();
	$page_id  = absint( $settings['wishlist_page_id'] );

	if ( $page_id > 0 ) {
		return $page_id;
	}

	return muukal_wishlist_ensure_page();
}

/**
 * Return the wishlist URL.
 *
 * @return string
 */
function muukal_wishlist_get_page_url() {
	$page_id = muukal_wishlist_get_page_id();

	if ( $page_id > 0 ) {
		return get_permalink( $page_id ) ?: home_url( '/' );
	}

	return home_url( '/' );
}

/**
 * Read items for guest users.
 *
 * @return array<int, int>
 */
function muukal_wishlist_get_guest_items() {
	if ( empty( $_COOKIE[ MUUKAL_WISHLIST_COOKIE ] ) ) {
		return array();
	}

	$raw   = wp_unslash( $_COOKIE[ MUUKAL_WISHLIST_COOKIE ] );
	$items = json_decode( $raw, true );

	return muukal_wishlist_normalize_items( $items );
}

/**
 * Persist guest items in a cookie.
 *
 * @param array<int, int> $items Product IDs.
 * @return void
 */
function muukal_wishlist_set_guest_items( $items ) {
	$items = muukal_wishlist_normalize_items( $items );
	$value = wp_json_encode( $items );

	if ( ! headers_sent() ) {
		setcookie(
			MUUKAL_WISHLIST_COOKIE,
			(string) $value,
			time() + YEAR_IN_SECONDS,
			COOKIEPATH ? COOKIEPATH : '/',
			COOKIE_DOMAIN,
			is_ssl(),
			true
		);
	}

	$_COOKIE[ MUUKAL_WISHLIST_COOKIE ] = (string) $value;
}

/**
 * Get items for the current visitor.
 *
 * @return array<int, int>
 */
function muukal_wishlist_get_items() {
	if ( is_user_logged_in() ) {
		$items = get_user_meta( get_current_user_id(), MUUKAL_WISHLIST_USER_META, true );

		return muukal_wishlist_normalize_items( $items );
	}

	return muukal_wishlist_get_guest_items();
}

/**
 * Persist items for the current visitor.
 *
 * @param array<int, int> $items Product IDs.
 * @return array<int, int>
 */
function muukal_wishlist_set_items( $items ) {
	$items = muukal_wishlist_normalize_items( $items );

	if ( is_user_logged_in() ) {
		update_user_meta( get_current_user_id(), MUUKAL_WISHLIST_USER_META, $items );
	} else {
		muukal_wishlist_set_guest_items( $items );
	}

	return $items;
}

/**
 * Merge guest items after login.
 *
 * @param string $user_login User login.
 * @param WP_User $user User object.
 * @return void
 */
function muukal_wishlist_merge_guest_items_on_login( $user_login, $user ) {
	unset( $user_login );

	if ( ! $user instanceof WP_User ) {
		return;
	}

	$user_items  = get_user_meta( $user->ID, MUUKAL_WISHLIST_USER_META, true );
	$user_items  = muukal_wishlist_normalize_items( $user_items );
	$guest_items = muukal_wishlist_get_guest_items();

	if ( empty( $guest_items ) ) {
		return;
	}

	$merged = muukal_wishlist_normalize_items( array_merge( $user_items, $guest_items ) );
	update_user_meta( $user->ID, MUUKAL_WISHLIST_USER_META, $merged );
	muukal_wishlist_set_guest_items( array() );
}
add_action( 'wp_login', 'muukal_wishlist_merge_guest_items_on_login', 10, 2 );

/**
 * Whether a product is in the wishlist.
 *
 * @param int $product_id Product ID.
 * @return bool
 */
function muukal_wishlist_has_item( $product_id ) {
	return in_array( absint( $product_id ), muukal_wishlist_get_items(), true );
}

/**
 * Toggle a product in the wishlist.
 *
 * @param int $product_id Product ID.
 * @return array<string, mixed>
 */
function muukal_wishlist_toggle_item( $product_id ) {
	$product_id = absint( $product_id );

	if ( $product_id < 1 || 'product' !== get_post_type( $product_id ) ) {
		return array(
			'added' => false,
			'items' => muukal_wishlist_get_items(),
			'count' => count( muukal_wishlist_get_items() ),
		);
	}

	$items = muukal_wishlist_get_items();
	$index = array_search( $product_id, $items, true );
	$added = false;

	if ( false === $index ) {
		$items[] = $product_id;
		$added   = true;
	} else {
		unset( $items[ $index ] );
	}

	$items = muukal_wishlist_set_items( $items );

	return array(
		'added' => $added,
		'items' => $items,
		'count' => count( $items ),
	);
}

/**
 * Render the wishlist button.
 *
 * @param int $product_id Product ID.
 * @param string $context Render context.
 * @return string
 */
function muukal_wishlist_get_button_html( $product_id, $context = 'single' ) {
	$product_id = absint( $product_id );

	if ( $product_id < 1 || 'product' !== get_post_type( $product_id ) ) {
		return '';
	}

	$settings = muukal_wishlist_get_settings();
	$is_saved = muukal_wishlist_has_item( $product_id );
	$classes  = array(
		'muukal-wishlist-button',
		'muukal-wishlist-button--' . sanitize_html_class( $context ),
	);

	if ( $is_saved ) {
		$classes[] = 'is-saved';
	}

	ob_start();
	?>
	<button
		type="button"
		class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
		data-product-id="<?php echo esc_attr( $product_id ); ?>"
		data-label-add="<?php echo esc_attr( $settings['button_label_add'] ); ?>"
		data-label-added="<?php echo esc_attr( $settings['button_label_added'] ); ?>"
		aria-pressed="<?php echo $is_saved ? 'true' : 'false'; ?>"
	>
		<span class="muukal-wishlist-button__heart" aria-hidden="true">&#10084;</span>
		<span class="muukal-wishlist-button__label"><?php echo esc_html( $is_saved ? $settings['button_label_added'] : $settings['button_label_add'] ); ?></span>
	</button>
	<?php

	return (string) ob_get_clean();
}

/**
 * Print single-product button.
 *
 * @return void
 */
function muukal_wishlist_render_single_button() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}

	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	echo '<div class="muukal-wishlist-entry muukal-wishlist-entry--single">';
	echo wp_kses_post( muukal_wishlist_get_button_html( $product->get_id(), 'single' ) );
	echo '</div>';
}

/**
 * Print archive button.
 *
 * @return void
 */
function muukal_wishlist_render_loop_button() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	echo '<div class="muukal-wishlist-entry muukal-wishlist-entry--loop">';
	echo wp_kses_post( muukal_wishlist_get_button_html( $product->get_id(), 'loop' ) );
	echo '</div>';
}

/**
 * Register hooks once plugins load.
 *
 * @return void
 */
function muukal_wishlist_register_hooks() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	$settings = muukal_wishlist_get_settings();

	if ( 'yes' === $settings['auto_add_single'] ) {
		add_action( 'woocommerce_single_product_summary', 'muukal_wishlist_render_single_button', 31 );
	}

	if ( 'yes' === $settings['auto_add_loop'] ) {
		add_action( 'woocommerce_after_shop_loop_item', 'muukal_wishlist_render_loop_button', 15 );
	}
}
add_action( 'plugins_loaded', 'muukal_wishlist_register_hooks', 20 );

/**
 * Enqueue frontend assets.
 *
 * @return void
 */
function muukal_wishlist_enqueue_assets() {
	if ( is_admin() ) {
		return;
	}

	$settings = muukal_wishlist_get_settings();
	$color    = sanitize_hex_color( $settings['primary_color'] );

	if ( ! $color ) {
		$color = '#ff7f90';
	}

	wp_enqueue_style(
		'muukal-wishlist',
		MUUKAL_WISHLIST_URL . 'assets/muukal-wishlist.css',
		array(),
		muukal_wishlist_asset_version( 'assets/muukal-wishlist.css' )
	);

	wp_add_inline_style(
		'muukal-wishlist',
		':root{--muukal-wishlist-primary:' . $color . ';}'
	);

	wp_enqueue_script(
		'muukal-wishlist',
		MUUKAL_WISHLIST_URL . 'assets/muukal-wishlist.js',
		array(),
		muukal_wishlist_asset_version( 'assets/muukal-wishlist.js' ),
		true
	);

	wp_localize_script(
		'muukal-wishlist',
		'muukalWishlist',
		array(
			'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
			'nonce'        => wp_create_nonce( 'muukal_wishlist_toggle' ),
			'wishlistUrl'  => muukal_wishlist_get_page_url(),
			'viewLabel'    => __( 'View wishlist', 'muukal-wishlist' ),
			'count'        => count( muukal_wishlist_get_items() ),
			'removeNotice' => __( 'Removed from wishlist.', 'muukal-wishlist' ),
			'addNotice'    => __( 'Added to wishlist.', 'muukal-wishlist' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'muukal_wishlist_enqueue_assets' );

/**
 * Handle AJAX toggle requests.
 *
 * @return void
 */
function muukal_wishlist_ajax_toggle() {
	check_ajax_referer( 'muukal_wishlist_toggle', 'nonce' );

	$product_id = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0;

	if ( $product_id < 1 || 'product' !== get_post_type( $product_id ) ) {
		wp_send_json_error(
			array(
				'message' => __( 'Invalid product.', 'muukal-wishlist' ),
			),
			400
		);
	}

	$result = muukal_wishlist_toggle_item( $product_id );

	wp_send_json_success(
		array(
			'productId'   => $product_id,
			'added'       => $result['added'],
			'count'       => $result['count'],
			'wishlistUrl' => muukal_wishlist_get_page_url(),
			'buttonHtml'  => muukal_wishlist_get_button_html( $product_id, 'single' ),
		)
	);
}
add_action( 'wp_ajax_muukal_wishlist_toggle', 'muukal_wishlist_ajax_toggle' );
add_action( 'wp_ajax_nopriv_muukal_wishlist_toggle', 'muukal_wishlist_ajax_toggle' );

/**
 * Shortcode for an individual button.
 *
 * @param array<string, mixed> $atts Shortcode attributes.
 * @return string
 */
function muukal_wishlist_button_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'product_id' => 0,
			'context'    => 'shortcode',
		),
		$atts,
		'muukal_wishlist_button'
	);

	$product_id = absint( $atts['product_id'] );

	if ( $product_id < 1 && function_exists( 'is_product' ) && is_product() ) {
		$product_id = get_the_ID();
	}

	return muukal_wishlist_get_button_html( $product_id, sanitize_key( $atts['context'] ) );
}
add_shortcode( 'muukal_wishlist_button', 'muukal_wishlist_button_shortcode' );

/**
 * Shortcode for wishlist count.
 *
 * @return string
 */
function muukal_wishlist_count_shortcode() {
	$count = count( muukal_wishlist_get_items() );

	return '<span class="muukal-wishlist-count" data-muukal-wishlist-count>' . esc_html( (string) $count ) . '</span>';
}
add_shortcode( 'muukal_wishlist_count', 'muukal_wishlist_count_shortcode' );

/**
 * Shortcode for wishlist link.
 *
 * @param array<string, mixed> $atts Shortcode attributes.
 * @return string
 */
function muukal_wishlist_link_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'label' => 'Wishlist',
		),
		$atts,
		'muukal_wishlist_link'
	);

	return sprintf(
		'<a class="muukal-wishlist-link" href="%1$s">%2$s <span class="muukal-wishlist-count" data-muukal-wishlist-count>%3$s</span></a>',
		esc_url( muukal_wishlist_get_page_url() ),
		esc_html( $atts['label'] ),
		esc_html( (string) count( muukal_wishlist_get_items() ) )
	);
}
add_shortcode( 'muukal_wishlist_link', 'muukal_wishlist_link_shortcode' );

/**
 * Render the wishlist page.
 *
 * @return string
 */
function muukal_wishlist_shortcode() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return '<p>' . esc_html__( 'WooCommerce is required for wishlist items.', 'muukal-wishlist' ) . '</p>';
	}

	$settings = muukal_wishlist_get_settings();
	$item_ids = muukal_wishlist_get_items();
	$products = array();

	foreach ( $item_ids as $item_id ) {
		$product = wc_get_product( $item_id );

		if ( $product instanceof WC_Product ) {
			$products[] = $product;
		}
	}

	ob_start();
	?>
	<div class="muukal-wishlist-page" data-muukal-wishlist-page>
		<div class="muukal-wishlist-page__header">
			<h2 class="muukal-wishlist-page__title"><?php echo esc_html( $settings['page_title'] ); ?></h2>
			<p class="muukal-wishlist-page__count">
				<?php
				echo esc_html(
					sprintf(
						/* translators: %d product count */
						_n( '%d saved item', '%d saved items', count( $products ), 'muukal-wishlist' ),
						count( $products )
					)
				);
				?>
			</p>
		</div>

		<?php if ( empty( $products ) ) : ?>
			<div class="muukal-wishlist-empty">
				<h3><?php echo esc_html( $settings['empty_heading'] ); ?></h3>
				<p><?php echo esc_html( $settings['empty_text'] ); ?></p>
				<a class="muukal-wishlist-empty__cta" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
					<?php esc_html_e( 'Browse products', 'muukal-wishlist' ); ?>
				</a>
			</div>
		<?php else : ?>
			<div class="muukal-wishlist-grid">
				<?php foreach ( $products as $product ) : ?>
					<?php
					$product_id = $product->get_id();
					$image_html = $product->get_image( 'woocommerce_thumbnail' );
					$price_html = $product->get_price_html();
					?>
					<article class="muukal-wishlist-card" data-product-id="<?php echo esc_attr( $product_id ); ?>">
						<a class="muukal-wishlist-card__media" href="<?php echo esc_url( get_permalink( $product_id ) ); ?>">
							<?php echo wp_kses_post( $image_html ); ?>
						</a>
						<div class="muukal-wishlist-card__body">
							<h3 class="muukal-wishlist-card__title">
								<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>">
									<?php echo esc_html( $product->get_name() ); ?>
								</a>
							</h3>
							<?php if ( $price_html ) : ?>
								<p class="muukal-wishlist-card__price"><?php echo wp_kses_post( $price_html ); ?></p>
							<?php endif; ?>
							<div class="muukal-wishlist-card__actions">
								<?php echo wp_kses_post( muukal_wishlist_get_button_html( $product_id, 'wishlist-page' ) ); ?>
								<a class="muukal-wishlist-card__view" href="<?php echo esc_url( get_permalink( $product_id ) ); ?>">
									<?php esc_html_e( 'View product', 'muukal-wishlist' ); ?>
								</a>
								<?php if ( $product->is_purchasable() && $product->is_in_stock() && $product->supports( 'ajax_add_to_cart' ) && $product->is_type( 'simple' ) ) : ?>
									<a
										class="button product_type_simple add_to_cart_button ajax_add_to_cart"
										href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
										data-product_id="<?php echo esc_attr( $product_id ); ?>"
										data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
										aria-label="<?php echo esc_attr( $product->add_to_cart_description() ); ?>"
										data-quantity="1"
									>
										<?php echo esc_html( $product->add_to_cart_text() ); ?>
									</a>
								<?php endif; ?>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php

	return (string) ob_get_clean();
}
add_shortcode( 'muukal_wishlist', 'muukal_wishlist_shortcode' );
