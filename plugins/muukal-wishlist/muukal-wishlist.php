<?php
/**
 * Plugin Name: Muukal Wishlist
 * Description: Wishlist service layer for existing Muukal render templates, with area toggles, shortcodes, and a dedicated wishlist page.
 * Version: 0.2.0
 * Author: Codex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MUUKAL_WISHLIST_VERSION', '0.2.0' );
define( 'MUUKAL_WISHLIST_FILE', __FILE__ );
define( 'MUUKAL_WISHLIST_DIR', plugin_dir_path( __FILE__ ) );
define( 'MUUKAL_WISHLIST_URL', plugin_dir_url( __FILE__ ) );
define( 'MUUKAL_WISHLIST_OPTION', 'muukal_wishlist_settings' );
define( 'MUUKAL_WISHLIST_USER_META', 'muukal_wishlist_items' );
define( 'MUUKAL_WISHLIST_COOKIE', 'muukal_wishlist_items' );
define( 'MUUKAL_WISHLIST_ACCOUNT_ENDPOINT', 'wishlist' );
define( 'MUUKAL_WISHLIST_VERSION_OPTION', 'muukal_wishlist_plugin_version' );

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
 * Get plugin defaults.
 *
 * @return array<string, mixed>
 */
function muukal_wishlist_default_settings() {
	return array(
		'button_label_add'       => 'ADD TO WISHLIST',
		'button_label_added'     => 'SAVED',
		'page_title'             => 'Wishlist',
		'page_slug'              => 'wishlist',
		'empty_heading'          => 'Your wishlist is empty.',
		'empty_text'             => 'Save frames you love and come back when you are ready to shop.',
		'primary_color'          => '#ff7f90',
		'wishlist_page_id'       => 0,
		'enable_product_detail'  => 'yes',
		'enable_product_cards'   => 'yes',
		'enable_shortcodes'      => 'yes',
	);
}

/**
 * Get settings merged with defaults.
 *
 * @return array<string, mixed>
 */
function muukal_wishlist_get_settings() {
	$settings = get_option( MUUKAL_WISHLIST_OPTION, array() );
	$settings = is_array( $settings ) ? $settings : array();

	return wp_parse_args( $settings, muukal_wishlist_default_settings() );
}

/**
 * Sanitize settings.
 *
 * @param mixed $input Raw option value.
 * @return array<string, mixed>
 */
function muukal_wishlist_sanitize_settings( $input ) {
	$defaults = muukal_wishlist_default_settings();
	$input    = is_array( $input ) ? $input : array();
	$output   = $defaults;
	$color    = sanitize_hex_color( $input['primary_color'] ?? $defaults['primary_color'] );

	$output['button_label_add']      = sanitize_text_field( $input['button_label_add'] ?? $defaults['button_label_add'] );
	$output['button_label_added']    = sanitize_text_field( $input['button_label_added'] ?? $defaults['button_label_added'] );
	$output['page_title']            = sanitize_text_field( $input['page_title'] ?? $defaults['page_title'] );
	$output['page_slug']             = 'wishlist';
	$output['empty_heading']         = sanitize_text_field( $input['empty_heading'] ?? $defaults['empty_heading'] );
	$output['empty_text']            = sanitize_text_field( $input['empty_text'] ?? $defaults['empty_text'] );
	$output['primary_color']         = $color ? $color : $defaults['primary_color'];
	$output['wishlist_page_id']      = absint( $input['wishlist_page_id'] ?? $defaults['wishlist_page_id'] );
	$output['enable_product_detail'] = ! empty( $input['enable_product_detail'] ) ? 'yes' : 'no';
	$output['enable_product_cards']  = ! empty( $input['enable_product_cards'] ) ? 'yes' : 'no';
	$output['enable_shortcodes']     = ! empty( $input['enable_shortcodes'] ) ? 'yes' : 'no';

	return $output;
}

/**
 * Register plugin settings.
 *
 * @return void
 */
function muukal_wishlist_register_settings() {
	register_setting(
		'muukal_wishlist_group',
		MUUKAL_WISHLIST_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'muukal_wishlist_sanitize_settings',
			'default'           => muukal_wishlist_default_settings(),
		)
	);
}
add_action( 'admin_init', 'muukal_wishlist_register_settings' );

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
 *
 * @return void
 */
function muukal_wishlist_activate() {
	if ( false === get_option( MUUKAL_WISHLIST_OPTION, false ) ) {
		add_option( MUUKAL_WISHLIST_OPTION, muukal_wishlist_default_settings() );
	}

	muukal_wishlist_ensure_page();
	muukal_wishlist_add_account_endpoint();
	update_option( MUUKAL_WISHLIST_VERSION_OPTION, MUUKAL_WISHLIST_VERSION );
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'muukal_wishlist_activate' );

/**
 * Deactivation callback.
 *
 * @return void
 */
function muukal_wishlist_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'muukal_wishlist_deactivate' );

/**
 * Register the WooCommerce account endpoint.
 *
 * @return void
 */
function muukal_wishlist_add_account_endpoint() {
	add_rewrite_endpoint( MUUKAL_WISHLIST_ACCOUNT_ENDPOINT, EP_ROOT | EP_PAGES );
}
add_action( 'init', 'muukal_wishlist_add_account_endpoint' );

/**
 * Flush rewrite rules once after plugin updates.
 *
 * @return void
 */
function muukal_wishlist_maybe_upgrade() {
	$stored_version = get_option( MUUKAL_WISHLIST_VERSION_OPTION, '' );

	if ( MUUKAL_WISHLIST_VERSION === $stored_version ) {
		return;
	}

	muukal_wishlist_ensure_page();
	update_option( MUUKAL_WISHLIST_VERSION_OPTION, MUUKAL_WISHLIST_VERSION );
	flush_rewrite_rules( false );
}
add_action( 'init', 'muukal_wishlist_maybe_upgrade', 20 );

/**
 * Add the wishlist endpoint query var.
 *
 * @param array<int, string> $vars Public query vars.
 * @return array<int, string>
 */
function muukal_wishlist_query_vars( $vars ) {
	$vars[] = MUUKAL_WISHLIST_ACCOUNT_ENDPOINT;

	return $vars;
}
add_filter( 'query_vars', 'muukal_wishlist_query_vars' );

/**
 * Insert wishlist into the My Account navigation.
 *
 * @param array<string, string> $items Account menu items.
 * @return array<string, string>
 */
function muukal_wishlist_account_menu_items( $items ) {
	$updated = array();
	$added   = false;

	foreach ( $items as $endpoint => $label ) {
		$updated[ $endpoint ] = $label;

		if ( 'dashboard' === $endpoint ) {
			$updated[ MUUKAL_WISHLIST_ACCOUNT_ENDPOINT ] = __( 'My Wishlist', 'muukal-wishlist' );
			$added = true;
		}
	}

	if ( ! $added ) {
		$updated[ MUUKAL_WISHLIST_ACCOUNT_ENDPOINT ] = __( 'My Wishlist', 'muukal-wishlist' );
	}

	return $updated;
}
add_filter( 'woocommerce_account_menu_items', 'muukal_wishlist_account_menu_items' );

/**
 * Render My Account wishlist endpoint content.
 *
 * @return void
 */
function muukal_wishlist_account_content() {
	echo do_shortcode( '[muukal_wishlist]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'woocommerce_account_' . MUUKAL_WISHLIST_ACCOUNT_ENDPOINT . '_endpoint', 'muukal_wishlist_account_content' );

/**
 * Get the preferred account wishlist URL.
 *
 * @return string
 */
function muukal_wishlist_get_account_url() {
	if ( function_exists( 'wc_get_account_endpoint_url' ) ) {
		return wc_get_account_endpoint_url( MUUKAL_WISHLIST_ACCOUNT_ENDPOINT );
	}

	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$account_url = wc_get_page_permalink( 'myaccount' );

		if ( $account_url && 'myaccount' !== $account_url ) {
			return trailingslashit( $account_url ) . MUUKAL_WISHLIST_ACCOUNT_ENDPOINT . '/';
		}
	}

	return muukal_wishlist_get_page_url();
}

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
 * Return the wishlist page URL.
 *
 * @return string
 */
function muukal_wishlist_get_page_url() {
	$page_id = muukal_wishlist_get_page_id();

	if ( $page_id > 0 ) {
		return get_permalink( $page_id ) ?: home_url( '/wishlist/' );
	}

	return home_url( '/wishlist/' );
}

/**
 * Normalize wishlist item IDs.
 *
 * @param mixed $items Raw item IDs.
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
 * Get guest items from cookie.
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
 * Save items for current visitor.
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
 * Merge guest wishlist items into user account after login.
 *
 * @param string  $user_login User login.
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
 * Check whether a wishlist region is enabled.
 *
 * @param string $region Region key.
 * @return bool
 */
function muukal_wishlist_region_enabled( $region ) {
	$settings = muukal_wishlist_get_settings();

	switch ( $region ) {
		case 'product_detail':
			return 'yes' === $settings['enable_product_detail'];
		case 'product_cards':
			return 'yes' === $settings['enable_product_cards'];
		case 'shortcodes':
			return 'yes' === $settings['enable_shortcodes'];
		default:
			return false;
	}
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
		$items = muukal_wishlist_get_items();

		return array(
			'added' => false,
			'items' => $items,
			'count' => count( $items ),
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
 * Render a manual wishlist button for shortcode usage.
 *
 * @param int    $product_id Product ID.
 * @param string $class_name Optional extra class names.
 * @return string
 */
function muukal_wishlist_get_manual_button_html( $product_id, $class_name = '' ) {
	$product_id = absint( $product_id );

	if ( $product_id < 1 || 'product' !== get_post_type( $product_id ) ) {
		return '';
	}

	$settings = muukal_wishlist_get_settings();
	$items    = muukal_wishlist_get_items();
	$is_saved = in_array( $product_id, $items, true );
	$classes  = trim( 'muukal-wishlist-manual-button ' . $class_name . ( $is_saved ? ' is-saved' : '' ) );

	ob_start();
	?>
	<button
		type="button"
		class="<?php echo esc_attr( $classes ); ?>"
		data-muukal-wishlist-button="manual"
		data-product-id="<?php echo esc_attr( $product_id ); ?>"
		data-label-add="<?php echo esc_attr( $settings['button_label_add'] ); ?>"
		data-label-added="<?php echo esc_attr( $settings['button_label_added'] ); ?>"
		aria-pressed="<?php echo $is_saved ? 'true' : 'false'; ?>"
	>
		<span class="muukal-wishlist-manual-button__heart" aria-hidden="true">&#10084;</span>
		<span class="muukal-wishlist-manual-button__label"><?php echo esc_html( $is_saved ? $settings['button_label_added'] : $settings['button_label_add'] ); ?></span>
	</button>
	<?php

	return (string) ob_get_clean();
}

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

	wp_enqueue_style(
		'muukal-wishlist',
		MUUKAL_WISHLIST_URL . 'assets/muukal-wishlist.css',
		array(),
		muukal_wishlist_asset_version( 'assets/muukal-wishlist.css' )
	);

	wp_add_inline_style(
		'muukal-wishlist',
		':root{--muukal-wishlist-primary:' . esc_html( $settings['primary_color'] ) . ';}'
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
			'items'        => muukal_wishlist_get_items(),
			'labels'       => array(
				'add'   => $settings['button_label_add'],
				'added' => $settings['button_label_added'],
			),
			'regions'      => array(
				'productDetail' => muukal_wishlist_region_enabled( 'product_detail' ),
				'productCards'  => muukal_wishlist_region_enabled( 'product_cards' ),
				'shortcodes'    => muukal_wishlist_region_enabled( 'shortcodes' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'muukal_wishlist_enqueue_assets' );

/**
 * Render CSS to hide disabled regions without changing template markup.
 *
 * @return void
 */
function muukal_wishlist_render_disabled_region_css() {
	$selectors = array();

	if ( ! muukal_wishlist_region_enabled( 'product_detail' ) ) {
		$selectors[] = '.add-wishlist-btn';
	}

	if ( ! muukal_wishlist_region_enabled( 'product_cards' ) ) {
		$selectors[] = '.muukal-card-wishlist';
	}

	if ( empty( $selectors ) ) {
		return;
	}

	echo '<style id="muukal-wishlist-disabled-regions">' . implode( ',', array_map( 'esc_html', $selectors ) ) . '{display:none !important;}</style>';
}
add_action( 'wp_head', 'muukal_wishlist_render_disabled_region_css', 120 );

/**
 * Handle AJAX toggle.
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
			'productId' => $product_id,
			'added'     => $result['added'],
			'count'     => $result['count'],
			'items'     => $result['items'],
		)
	);
}
add_action( 'wp_ajax_muukal_wishlist_toggle', 'muukal_wishlist_ajax_toggle' );
add_action( 'wp_ajax_nopriv_muukal_wishlist_toggle', 'muukal_wishlist_ajax_toggle' );

/**
 * Shortcode for manual wishlist button.
 *
 * @param array<string, mixed> $atts Shortcode attributes.
 * @return string
 */
function muukal_wishlist_button_shortcode( $atts ) {
	if ( ! muukal_wishlist_region_enabled( 'shortcodes' ) ) {
		return '';
	}

	$atts = shortcode_atts(
		array(
			'product_id' => 0,
			'class'      => '',
		),
		$atts,
		'muukal_wishlist_button'
	);

	$product_id = absint( $atts['product_id'] );

	if ( ! $product_id ) {
		$product_id = get_the_ID();
	}

	return muukal_wishlist_get_manual_button_html( $product_id, sanitize_text_field( $atts['class'] ) );
}
add_shortcode( 'muukal_wishlist_button', 'muukal_wishlist_button_shortcode' );

/**
 * Shortcode for wishlist count.
 *
 * @return string
 */
function muukal_wishlist_count_shortcode() {
	return '<span class="muukal-wishlist-count" data-muukal-wishlist-count>' . esc_html( (string) count( muukal_wishlist_get_items() ) ) . '</span>';
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
			'class' => '',
		),
		$atts,
		'muukal_wishlist_link'
	);

	return sprintf(
		'<a class="%1$s" href="%2$s">%3$s <span class="muukal-wishlist-count" data-muukal-wishlist-count>%4$s</span></a>',
		esc_attr( trim( 'muukal-wishlist-link ' . sanitize_text_field( $atts['class'] ) ) ),
		esc_url( muukal_wishlist_get_page_url() ),
		esc_html( $atts['label'] ),
		esc_html( (string) count( muukal_wishlist_get_items() ) )
	);
}
add_shortcode( 'muukal_wishlist_link', 'muukal_wishlist_link_shortcode' );

/**
 * Shortcode that prints the wishlist URL.
 *
 * @return string
 */
function muukal_wishlist_url_shortcode() {
	return esc_url( muukal_wishlist_get_page_url() );
}
add_shortcode( 'muukal_wishlist_url', 'muukal_wishlist_url_shortcode' );

/**
 * Shortcode that prints the My Account wishlist URL.
 *
 * @return string
 */
function muukal_wishlist_account_url_shortcode() {
	return esc_url( muukal_wishlist_get_account_url() );
}
add_shortcode( 'muukal_wishlist_account_url', 'muukal_wishlist_account_url_shortcode' );

/**
 * Render wishlist page shortcode.
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
			<p class="muukal-wishlist-page__count"><?php echo esc_html( sprintf( _n( '%d saved item', '%d saved items', count( $products ), 'muukal-wishlist' ), count( $products ) ) ); ?></p>
		</div>

		<?php if ( empty( $products ) ) : ?>
			<div class="muukal-wishlist-empty">
				<h3><?php echo esc_html( $settings['empty_heading'] ); ?></h3>
				<p><?php echo esc_html( $settings['empty_text'] ); ?></p>
				<a class="muukal-wishlist-empty__cta" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>">
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
								<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
							</h3>
							<?php if ( $price_html ) : ?>
								<p class="muukal-wishlist-card__price"><?php echo wp_kses_post( $price_html ); ?></p>
							<?php endif; ?>
							<div class="muukal-wishlist-card__actions">
								<?php echo wp_kses_post( muukal_wishlist_get_manual_button_html( $product_id, 'muukal-wishlist-page__remove' ) ); ?>
								<a class="muukal-wishlist-card__view" href="<?php echo esc_url( get_permalink( $product_id ) ); ?>">
									<?php esc_html_e( 'View product', 'muukal-wishlist' ); ?>
								</a>
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

/**
 * Add admin page.
 *
 * @return void
 */
function muukal_wishlist_admin_menu() {
	add_options_page(
		'Muukal Wishlist',
		'Muukal Wishlist',
		'manage_options',
		'muukal-wishlist',
		'muukal_wishlist_render_admin_page'
	);
}
add_action( 'admin_menu', 'muukal_wishlist_admin_menu' );

/**
 * Render the admin settings page.
 *
 * @return void
 */
function muukal_wishlist_render_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings = muukal_wishlist_get_settings();
	$page_url = muukal_wishlist_get_page_url();
	?>
	<div class="wrap">
		<h1>Muukal Wishlist</h1>
		<p>Use this page to keep the existing render templates connected without injecting new wishlist UI into WooCommerce defaults.</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'muukal_wishlist_group' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="muukal_wishlist_page_title">Wishlist page title</label></th>
					<td><input id="muukal_wishlist_page_title" name="<?php echo esc_attr( MUUKAL_WISHLIST_OPTION ); ?>[page_title]" type="text" class="regular-text" value="<?php echo esc_attr( $settings['page_title'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row">Wishlist page slug</th>
					<td>
						<input type="text" class="regular-text" value="wishlist" readonly>
						<p class="description">Kept fixed at <code>/wishlist/</code> so your existing render links continue working. Current URL: <a href="<?php echo esc_url( $page_url ); ?>" target="_blank" rel="noreferrer"><?php echo esc_html( $page_url ); ?></a></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="muukal_wishlist_button_label_add">Default add label</label></th>
					<td><input id="muukal_wishlist_button_label_add" name="<?php echo esc_attr( MUUKAL_WISHLIST_OPTION ); ?>[button_label_add]" type="text" class="regular-text" value="<?php echo esc_attr( $settings['button_label_add'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="muukal_wishlist_button_label_added">Saved label</label></th>
					<td><input id="muukal_wishlist_button_label_added" name="<?php echo esc_attr( MUUKAL_WISHLIST_OPTION ); ?>[button_label_added]" type="text" class="regular-text" value="<?php echo esc_attr( $settings['button_label_added'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="muukal_wishlist_primary_color">Primary color</label></th>
					<td><input id="muukal_wishlist_primary_color" name="<?php echo esc_attr( MUUKAL_WISHLIST_OPTION ); ?>[primary_color]" type="text" class="regular-text" value="<?php echo esc_attr( $settings['primary_color'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row">Enable regions</th>
					<td>
						<label><input name="<?php echo esc_attr( MUUKAL_WISHLIST_OPTION ); ?>[enable_product_detail]" type="checkbox" value="1" <?php checked( 'yes', $settings['enable_product_detail'] ); ?>> Product detail render button (`.add-wishlist-btn`)</label><br>
						<label><input name="<?php echo esc_attr( MUUKAL_WISHLIST_OPTION ); ?>[enable_product_cards]" type="checkbox" value="1" <?php checked( 'yes', $settings['enable_product_cards'] ); ?>> Product card render button (`.muukal-card-wishlist`)</label><br>
						<label><input name="<?php echo esc_attr( MUUKAL_WISHLIST_OPTION ); ?>[enable_shortcodes]" type="checkbox" value="1" <?php checked( 'yes', $settings['enable_shortcodes'] ); ?>> Manual shortcode buttons</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="muukal_wishlist_empty_heading">Empty heading</label></th>
					<td><input id="muukal_wishlist_empty_heading" name="<?php echo esc_attr( MUUKAL_WISHLIST_OPTION ); ?>[empty_heading]" type="text" class="regular-text" value="<?php echo esc_attr( $settings['empty_heading'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="muukal_wishlist_empty_text">Empty text</label></th>
					<td><input id="muukal_wishlist_empty_text" name="<?php echo esc_attr( MUUKAL_WISHLIST_OPTION ); ?>[empty_text]" type="text" class="large-text" value="<?php echo esc_attr( $settings['empty_text'] ); ?>"></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>

		<h2>Shortcodes</h2>
		<ul style="list-style:disc;padding-left:20px;">
			<li><code>[muukal_wishlist]</code> renders the wishlist page contents.</li>
			<li><code>[muukal_wishlist_link label="Wishlist"]</code> outputs a link to the wishlist page.</li>
			<li><code>[muukal_wishlist_url]</code> prints the wishlist URL only.</li>
			<li><code>[muukal_wishlist_account_url]</code> prints the My Account wishlist URL.</li>
			<li><code>[muukal_wishlist_count]</code> prints the current saved count.</li>
			<li><code>[muukal_wishlist_button product_id="123"]</code> renders a manual toggle button.</li>
		</ul>
	</div>
	<?php
}
