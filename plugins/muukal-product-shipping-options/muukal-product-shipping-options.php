<?php
/**
 * Plugin Name: Muukal Product Shipping Options
 * Description: Adds per-product shipping option cards in WooCommerce admin and exposes them with a shortcode.
 * Version: 0.1.0
 * Author: Codex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MUUKAL_PRODUCT_SHIPPING_OPTIONS_VERSION', '0.1.0' );
define( 'MUUKAL_PRODUCT_SHIPPING_OPTIONS_FILE', __FILE__ );
define( 'MUUKAL_PRODUCT_SHIPPING_OPTIONS_DIR', plugin_dir_path( __FILE__ ) );
define( 'MUUKAL_PRODUCT_SHIPPING_OPTIONS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Get an asset version from filemtime when available.
 *
 * @param string $relative_path File path relative to the plugin root.
 * @return string
 */
function muukal_product_shipping_options_asset_version( $relative_path ) {
	$path = MUUKAL_PRODUCT_SHIPPING_OPTIONS_DIR . ltrim( $relative_path, '/\\' );

	if ( file_exists( $path ) ) {
		$mtime = filemtime( $path );

		if ( false !== $mtime ) {
			return (string) $mtime;
		}
	}

	return MUUKAL_PRODUCT_SHIPPING_OPTIONS_VERSION;
}

/**
 * Default saved row shape.
 *
 * @return array<string, string>
 */
function muukal_product_shipping_options_default_row() {
	return array(
		'label'       => '',
		'description' => '',
		'eta'         => '',
		'price'       => '',
		'icon'        => 'standard',
		'is_default'  => '',
		'is_enabled'  => '1',
	);
}

/**
 * Return available icon choices.
 *
 * @return array<string, string>
 */
function muukal_product_shipping_options_icon_choices() {
	return array(
		'standard' => 'Standard',
		'plane'    => 'Plane',
		'rocket'   => 'Rocket',
		'box'      => 'Box',
		'clock'    => 'Clock',
	);
}

/**
 * Register product metabox.
 */
function muukal_product_shipping_options_register_metabox() {
	add_meta_box(
		'muukal_product_shipping_options',
		'Product Shipping Options',
		'muukal_product_shipping_options_render_metabox',
		'product',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'muukal_product_shipping_options_register_metabox' );

/**
 * Load admin assets on WooCommerce product edit screens.
 *
 * @param string $hook Current admin hook.
 */
function muukal_product_shipping_options_admin_assets( $hook ) {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'product' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_style(
		'muukal-product-shipping-options-admin',
		MUUKAL_PRODUCT_SHIPPING_OPTIONS_URL . 'assets/admin.css',
		array(),
		muukal_product_shipping_options_asset_version( 'assets/admin.css' )
	);

	wp_enqueue_script(
		'muukal-product-shipping-options-admin',
		MUUKAL_PRODUCT_SHIPPING_OPTIONS_URL . 'assets/admin.js',
		array( 'jquery', 'wp-util' ),
		muukal_product_shipping_options_asset_version( 'assets/admin.js' ),
		true
	);
}
add_action( 'admin_enqueue_scripts', 'muukal_product_shipping_options_admin_assets' );

/**
 * Register frontend assets.
 */
function muukal_product_shipping_options_register_assets() {
	wp_register_style(
		'muukal-product-shipping-options',
		MUUKAL_PRODUCT_SHIPPING_OPTIONS_URL . 'assets/frontend.css',
		array(),
		muukal_product_shipping_options_asset_version( 'assets/frontend.css' )
	);
}
add_action( 'init', 'muukal_product_shipping_options_register_assets' );

/**
 * Render the admin metabox.
 *
 * @param WP_Post $post Product post object.
 */
function muukal_product_shipping_options_render_metabox( $post ) {
	wp_nonce_field( 'muukal_product_shipping_options_save', 'muukal_product_shipping_options_nonce' );

	$rows = get_post_meta( $post->ID, '_muukal_product_shipping_options', true );
	$rows = is_array( $rows ) ? array_values( $rows ) : array();
	?>
	<div class="muukal-shipping-admin" id="muukal-shipping-admin-root">
		<p class="description">Configure product-level shipping cards for frontend display. We can reuse the same data when you are ready to wire checkout logic later.</p>

		<div class="muukal-shipping-admin__table-wrap">
			<table class="widefat striped muukal-shipping-admin__table">
				<thead>
					<tr>
						<th>Label</th>
						<th>Description</th>
						<th>ETA</th>
						<th>Price</th>
						<th>Icon</th>
						<th>Default</th>
						<th>Enabled</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody id="muukal-shipping-admin-rows">
					<?php foreach ( $rows as $index => $row ) : ?>
						<?php muukal_product_shipping_options_render_admin_row( $index, $row ); ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<p class="muukal-shipping-admin__actions">
			<button type="button" class="button button-primary" id="muukal-shipping-admin-add-row">Add Shipping Option</button>
		</p>

		<p class="description">
			Shortcode:
			<code>[muukal_product_shipping_options]</code>
			or
			<code>[muukal_product_shipping_options product_id="123" title="Shipping method"]</code>
		</p>
	</div>

	<script type="text/html" id="tmpl-muukal-shipping-admin-row">
		<?php muukal_product_shipping_options_render_admin_row( '{{{data.index}}}', array() ); ?>
	</script>
	<?php
}

/**
 * Render one admin row.
 *
 * @param int|string          $index Row index.
 * @param array<string,mixed> $row Row data.
 */
function muukal_product_shipping_options_render_admin_row( $index, $row ) {
	$row        = wp_parse_args( is_array( $row ) ? $row : array(), muukal_product_shipping_options_default_row() );
	$base_key   = 'muukal_product_shipping_options[' . $index . ']';
	$icon_types = muukal_product_shipping_options_icon_choices();
	?>
	<tr class="muukal-shipping-admin__row">
		<td>
			<input type="text" class="regular-text" name="<?php echo esc_attr( $base_key ); ?>[label]" value="<?php echo esc_attr( $row['label'] ); ?>" placeholder="Standard Shipping" />
		</td>
		<td>
			<textarea rows="3" class="large-text" name="<?php echo esc_attr( $base_key ); ?>[description]" placeholder="Estimated time of arrival..."><?php echo esc_textarea( $row['description'] ); ?></textarea>
		</td>
		<td>
			<input type="text" class="regular-text" name="<?php echo esc_attr( $base_key ); ?>[eta]" value="<?php echo esc_attr( $row['eta'] ); ?>" placeholder="12-21 Business Days" />
		</td>
		<td>
			<input type="number" min="0" step="0.01" class="small-text" name="<?php echo esc_attr( $base_key ); ?>[price]" value="<?php echo esc_attr( $row['price'] ); ?>" placeholder="4.95" />
		</td>
		<td>
			<select name="<?php echo esc_attr( $base_key ); ?>[icon]">
				<?php foreach ( $icon_types as $icon_key => $icon_label ) : ?>
					<option value="<?php echo esc_attr( $icon_key ); ?>" <?php selected( $row['icon'], $icon_key ); ?>><?php echo esc_html( $icon_label ); ?></option>
				<?php endforeach; ?>
			</select>
		</td>
		<td class="muukal-shipping-admin__cell-center">
			<input type="radio" name="muukal_product_shipping_options_default" value="<?php echo esc_attr( $index ); ?>" <?php checked( ! empty( $row['is_default'] ) ); ?> />
		</td>
		<td class="muukal-shipping-admin__cell-center">
			<input type="checkbox" name="<?php echo esc_attr( $base_key ); ?>[is_enabled]" value="1" <?php checked( ! empty( $row['is_enabled'] ) ); ?> />
		</td>
		<td class="muukal-shipping-admin__cell-center">
			<button type="button" class="button-link-delete muukal-shipping-admin-remove-row">Remove</button>
		</td>
	</tr>
	<?php
}

/**
 * Save product shipping options.
 *
 * @param int $post_id Product ID.
 */
function muukal_product_shipping_options_save_product_meta( $post_id ) {
	if ( ! isset( $_POST['muukal_product_shipping_options_nonce'] ) ) {
		return;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['muukal_product_shipping_options_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'muukal_product_shipping_options_save' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$rows          = isset( $_POST['muukal_product_shipping_options'] ) ? (array) wp_unslash( $_POST['muukal_product_shipping_options'] ) : array();
	$default_index = isset( $_POST['muukal_product_shipping_options_default'] ) ? sanitize_text_field( wp_unslash( $_POST['muukal_product_shipping_options_default'] ) ) : '';
	$icon_choices  = muukal_product_shipping_options_icon_choices();
	$clean_rows    = array();

	foreach ( $rows as $row_index => $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$icon = isset( $row['icon'] ) ? sanitize_key( $row['icon'] ) : 'standard';
		if ( ! isset( $icon_choices[ $icon ] ) ) {
			$icon = 'standard';
		}

		$item = array(
			'label'       => sanitize_text_field( $row['label'] ?? '' ),
			'description' => sanitize_textarea_field( $row['description'] ?? '' ),
			'eta'         => sanitize_text_field( $row['eta'] ?? '' ),
			'price'       => '' !== (string) ( $row['price'] ?? '' ) ? wc_format_decimal( $row['price'] ) : '',
			'icon'        => $icon,
			'is_default'  => ( (string) $row_index === (string) $default_index ) ? '1' : '',
			'is_enabled'  => ! empty( $row['is_enabled'] ) ? '1' : '',
		);

		if ( '' === $item['label'] && '' === $item['description'] && '' === $item['eta'] && '' === $item['price'] ) {
			continue;
		}

		$clean_rows[] = $item;
	}

	if ( ! empty( $clean_rows ) && ! array_filter( $clean_rows, static function ( $row ) {
		return ! empty( $row['is_default'] );
	} ) ) {
		$clean_rows[0]['is_default'] = '1';
	}

	if ( empty( $clean_rows ) ) {
		delete_post_meta( $post_id, '_muukal_product_shipping_options' );
		return;
	}

	update_post_meta( $post_id, '_muukal_product_shipping_options', array_values( $clean_rows ) );
}
add_action( 'save_post_product', 'muukal_product_shipping_options_save_product_meta' );

/**
 * Get sanitized shipping options for a product.
 *
 * @param int $product_id Product ID.
 * @return array<int, array<string, string>>
 */
function muukal_product_shipping_options_get_product_options( $product_id ) {
	$product_id = absint( $product_id );
	if ( ! $product_id ) {
		return array();
	}

	$rows = get_post_meta( $product_id, '_muukal_product_shipping_options', true );
	if ( ! is_array( $rows ) ) {
		return array();
	}

	$defaults    = muukal_product_shipping_options_default_row();
	$icon_choices = muukal_product_shipping_options_icon_choices();
	$clean_rows  = array();

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) || empty( $row['is_enabled'] ) ) {
			continue;
		}

		$item = wp_parse_args( $row, $defaults );
		$item = array(
			'label'       => sanitize_text_field( $item['label'] ),
			'description' => sanitize_textarea_field( $item['description'] ),
			'eta'         => sanitize_text_field( $item['eta'] ),
			'price'       => '' !== (string) $item['price'] ? wc_format_decimal( $item['price'] ) : '',
			'icon'        => isset( $icon_choices[ $item['icon'] ] ) ? $item['icon'] : 'standard',
			'is_default'  => ! empty( $item['is_default'] ) ? '1' : '',
			'is_enabled'  => '1',
		);

		if ( '' === $item['label'] ) {
			continue;
		}

		$clean_rows[] = $item;
	}

	return array_values( $clean_rows );
}

/**
 * Resolve the selected product ID for the shortcode.
 *
 * @param array<string,string> $atts Shortcode attributes.
 * @return int
 */
function muukal_product_shipping_options_resolve_product_id( $atts ) {
	if ( ! empty( $atts['product_id'] ) ) {
		return absint( $atts['product_id'] );
	}

	if ( function_exists( 'is_product' ) && is_product() ) {
		return get_the_ID();
	}

	$post = get_post();
	if ( $post instanceof WP_Post && 'product' === $post->post_type ) {
		return (int) $post->ID;
	}

	return 0;
}

/**
 * Format the shipping price for display.
 *
 * @param string $price Raw decimal value.
 * @param string $free_text Text used for zero-valued shipping.
 * @return string
 */
function muukal_product_shipping_options_format_price( $price, $free_text ) {
	if ( '' === $price ) {
		return '';
	}

	$amount = (float) $price;
	if ( $amount <= 0 ) {
		return $free_text;
	}

	if ( function_exists( 'wc_price' ) ) {
		return wp_strip_all_tags( wc_price( $amount ) );
	}

	return '$' . number_format_i18n( $amount, 2 );
}

/**
 * Return inline SVG for one icon.
 *
 * @param string $icon Icon key.
 * @return string
 */
function muukal_product_shipping_options_get_icon_svg( $icon ) {
	$stroke = 'currentColor';

	$icons = array(
		'standard' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7.5h11v9H3z" fill="none" stroke="' . $stroke . '" stroke-width="1.6"/><path d="M14 10h3l2.5 2.5V16H14z" fill="none" stroke="' . $stroke . '" stroke-width="1.6"/><circle cx="7" cy="18" r="1.6" fill="none" stroke="' . $stroke . '" stroke-width="1.6"/><circle cx="17" cy="18" r="1.6" fill="none" stroke="' . $stroke . '" stroke-width="1.6"/></svg>',
		'plane'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 4 10 13" fill="none" stroke="' . $stroke . '" stroke-width="1.7" stroke-linecap="round"/><path d="m21 4-7 16-2.8-6.2L5 11z" fill="none" stroke="' . $stroke . '" stroke-width="1.7" stroke-linejoin="round"/></svg>',
		'rocket'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14.5 4.5c2.8 1 5 3.2 6 6-.9 2.3-2.5 4.5-4.7 6.7l-4.5-4.5c2.2-2.2 4.4-3.8 6.7-4.7Z" fill="none" stroke="' . $stroke . '" stroke-width="1.6" stroke-linejoin="round"/><path d="M9.5 14.5 6 18l-.5-4.5L9 10" fill="none" stroke="' . $stroke . '" stroke-width="1.6" stroke-linejoin="round"/><circle cx="15.8" cy="8.2" r="1.2" fill="none" stroke="' . $stroke . '" stroke-width="1.6"/></svg>',
		'box'      => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 7 4v10l-7 4-7-4V7z" fill="none" stroke="' . $stroke . '" stroke-width="1.6" stroke-linejoin="round"/><path d="m5 7 7 4 7-4M12 11v10" fill="none" stroke="' . $stroke . '" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		'clock'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8" fill="none" stroke="' . $stroke . '" stroke-width="1.6"/><path d="M12 8v4.5l3 2" fill="none" stroke="' . $stroke . '" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>',
	);

	return $icons[ $icon ] ?? $icons['standard'];
}

/**
 * Render the shipping options shortcode.
 *
 * @param array<string,string> $atts Shortcode attributes.
 * @return string
 */
function muukal_product_shipping_options_shortcode( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'product_id'   => '',
			'title'        => 'Shipping Options',
			'free_text'    => 'FREE',
			'currency_hint'=> '',
			'class'        => '',
		),
		$atts,
		'muukal_product_shipping_options'
	);

	$product_id = muukal_product_shipping_options_resolve_product_id( $atts );
	if ( ! $product_id ) {
		return '';
	}

	$options = muukal_product_shipping_options_get_product_options( $product_id );
	if ( empty( $options ) ) {
		return '';
	}

	wp_enqueue_style( 'muukal-product-shipping-options' );

	$instance = 'muukal-shipping-options-' . wp_generate_uuid4();
	$classes  = array( 'muukal-shipping-options' );

	if ( '' !== trim( $atts['class'] ) ) {
		$extra_classes = preg_split( '/\s+/', trim( $atts['class'] ) );
		if ( is_array( $extra_classes ) ) {
			foreach ( $extra_classes as $extra_class ) {
				$extra_class = sanitize_html_class( $extra_class );
				if ( '' !== $extra_class ) {
					$classes[] = $extra_class;
				}
			}
		}
	}

	ob_start();
	?>
	<div class="<?php echo esc_attr( implode( ' ', array_unique( $classes ) ) ); ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>">
		<?php if ( '' !== trim( $atts['title'] ) ) : ?>
			<h3 class="muukal-shipping-options__title"><?php echo esc_html( $atts['title'] ); ?></h3>
		<?php endif; ?>

		<div class="muukal-shipping-options__list" role="radiogroup" aria-label="<?php echo esc_attr( $atts['title'] ); ?>">
			<?php foreach ( $options as $index => $option ) : ?>
				<?php
				$is_default   = ! empty( $option['is_default'] ) || 0 === $index;
				$price_text   = muukal_product_shipping_options_format_price( $option['price'], $atts['free_text'] );
				$option_id    = $instance . '-' . $index;
				$description  = trim( $option['description'] );
				$eta          = trim( $option['eta'] );
				$hint         = trim( $atts['currency_hint'] );
				?>
				<label class="muukal-shipping-options__card">
					<input
						type="radio"
						name="<?php echo esc_attr( $instance ); ?>"
						value="<?php echo esc_attr( $option['label'] ); ?>"
						data-price="<?php echo esc_attr( $option['price'] ); ?>"
						data-product-id="<?php echo esc_attr( $product_id ); ?>"
						<?php checked( $is_default ); ?>
					/>
					<span class="muukal-shipping-options__card-inner" id="<?php echo esc_attr( $option_id ); ?>">
						<span class="muukal-shipping-options__icon" aria-hidden="true"><?php echo wp_kses( muukal_product_shipping_options_get_icon_svg( $option['icon'] ), array( 'svg' => array( 'viewBox' => true, 'aria-hidden' => true ), 'path' => array( 'd' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true ) ) ); ?></span>
						<span class="muukal-shipping-options__content">
							<span class="muukal-shipping-options__topline">
								<span class="muukal-shipping-options__label"><?php echo esc_html( $option['label'] ); ?></span>
								<?php if ( '' !== $price_text ) : ?>
									<span class="muukal-shipping-options__price"><?php echo esc_html( $price_text ); ?></span>
								<?php endif; ?>
							</span>
							<?php if ( '' !== $description ) : ?>
								<span class="muukal-shipping-options__meta"><?php echo esc_html( $description ); ?></span>
							<?php endif; ?>
							<?php if ( '' !== $eta || '' !== $hint ) : ?>
								<span class="muukal-shipping-options__meta"><?php echo esc_html( trim( $eta . ' ' . $hint ) ); ?></span>
							<?php endif; ?>
						</span>
						<span class="muukal-shipping-options__check" aria-hidden="true">
							<svg viewBox="0 0 20 20"><path d="m3.5 10.5 4 4 9-9" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</span>
					</span>
				</label>
			<?php endforeach; ?>
		</div>
	</div>
	<?php

	return ob_get_clean();
}
add_shortcode( 'muukal_product_shipping_options', 'muukal_product_shipping_options_shortcode' );
add_shortcode( 'muukal_shipping_options', 'muukal_product_shipping_options_shortcode' );
