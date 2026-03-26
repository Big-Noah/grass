<?php
/**
 * Plugin Name: Muukal Buyer Showcase
 * Description: Buyer showcase grid with editable default images, modal quick look, and product links via shortcode.
 * Version: 0.1.1
 * Author: Codex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MUUKAL_BUYER_SHOWCASE_VERSION', '0.1.1' );
define( 'MUUKAL_BUYER_SHOWCASE_FILE', __FILE__ );
define( 'MUUKAL_BUYER_SHOWCASE_DIR', plugin_dir_path( __FILE__ ) );
define( 'MUUKAL_BUYER_SHOWCASE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Return a cache-busting asset version.
 *
 * @param string $relative_path Path relative to the plugin root.
 * @return string
 */
function muukal_buyer_showcase_asset_version( $relative_path ) {
	$absolute_path = MUUKAL_BUYER_SHOWCASE_DIR . ltrim( $relative_path, '/\\' );

	if ( file_exists( $absolute_path ) ) {
		$modified = filemtime( $absolute_path );

		if ( false !== $modified ) {
			return (string) $modified;
		}
	}

	return MUUKAL_BUYER_SHOWCASE_VERSION;
}

/**
 * Build the seeded buyershow rows.
 *
 * @return array<int, array<string, mixed>>
 */
function muukal_buyer_showcase_default_items() {
	$rows = array(
		'526',
		'844',
		'732',
		'1636',
		'557',
		'696',
		'538',
		'511',
		'871',
		'690',
		'435',
		'418',
		'1039',
		'869',
		'1133',
		'1177',
	);

	return array_map(
		static function ( $gid ) {
			return array(
				'gid'          => $gid,
				'image_url'    => MUUKAL_BUYER_SHOWCASE_URL . 'assets/defaults/' . $gid . '.webp',
				'product_id'   => 0,
				'product_url'  => 'https://muukal.com/frame/goods/gid/' . $gid . '.html',
				'quickview_url'=> 'https://muukal.com/frame/quickview/?gid=' . $gid,
				'title'        => 'Buyer Favorite #' . $gid,
				'price'        => '',
				'description'  => 'Real-customer style inspiration. Connect this card to a local WooCommerce product ID or custom product URL anytime.',
				'button_label' => 'View Product',
			);
		},
		$rows
	);
}

/**
 * Default plugin settings.
 *
 * @return array<string, mixed>
 */
function muukal_buyer_showcase_default_settings() {
	return array(
		'heading'       => 'Buyer Show',
		'subheading'    => 'Real-customer looks with a quick product path built in.',
		'columns'       => 6,
		'button_label'  => 'View Product',
		'items'         => muukal_buyer_showcase_default_items(),
	);
}

/**
 * Read persisted settings merged with defaults.
 *
 * @return array<string, mixed>
 */
function muukal_buyer_showcase_get_settings() {
	$settings = get_option( 'muukal_buyer_showcase_settings', array() );
	$settings = is_array( $settings ) ? $settings : array();
	$defaults = muukal_buyer_showcase_default_settings();

	$settings = wp_parse_args( $settings, $defaults );

	if ( empty( $settings['items'] ) || ! is_array( $settings['items'] ) ) {
		$settings['items'] = $defaults['items'];
	}

	return $settings;
}

/**
 * Set defaults when the plugin is first activated.
 */
function muukal_buyer_showcase_activate() {
	if ( false === get_option( 'muukal_buyer_showcase_settings', false ) ) {
		add_option( 'muukal_buyer_showcase_settings', muukal_buyer_showcase_default_settings() );
	}
}
register_activation_hook( __FILE__, 'muukal_buyer_showcase_activate' );

/**
 * Sanitize settings before save.
 *
 * @param mixed $input Raw input.
 * @return array<string, mixed>
 */
function muukal_buyer_showcase_sanitize_settings( $input ) {
	$defaults = muukal_buyer_showcase_default_settings();
	$input    = is_array( $input ) ? $input : array();
	$output   = array();

	$output['heading']      = sanitize_text_field( $input['heading'] ?? $defaults['heading'] );
	$output['subheading']   = sanitize_text_field( $input['subheading'] ?? $defaults['subheading'] );
	$output['columns']      = min( 6, max( 2, absint( $input['columns'] ?? $defaults['columns'] ) ) );
	$output['button_label'] = sanitize_text_field( $input['button_label'] ?? $defaults['button_label'] );
	$output['items']        = array();

	$rows = isset( $input['items'] ) && is_array( $input['items'] ) ? $input['items'] : array();

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$clean = array(
			'gid'           => sanitize_text_field( $row['gid'] ?? '' ),
			'image_url'     => esc_url_raw( trim( (string) ( $row['image_url'] ?? '' ) ) ),
			'product_id'    => absint( $row['product_id'] ?? 0 ),
			'product_url'   => esc_url_raw( trim( (string) ( $row['product_url'] ?? '' ) ) ),
			'quickview_url' => esc_url_raw( trim( (string) ( $row['quickview_url'] ?? '' ) ) ),
			'title'         => sanitize_text_field( $row['title'] ?? '' ),
			'price'         => sanitize_text_field( $row['price'] ?? '' ),
			'description'   => sanitize_textarea_field( $row['description'] ?? '' ),
			'button_label'  => sanitize_text_field( $row['button_label'] ?? '' ),
		);

		if ( '' === $clean['image_url'] && 0 === $clean['product_id'] && '' === $clean['product_url'] && '' === $clean['title'] ) {
			continue;
		}

		$output['items'][] = $clean;
	}

	if ( empty( $output['items'] ) ) {
		$output['items'] = $defaults['items'];
	}

	return $output;
}

/**
 * Register settings.
 */
function muukal_buyer_showcase_register_settings() {
	register_setting(
		'muukal_buyer_showcase_group',
		'muukal_buyer_showcase_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'muukal_buyer_showcase_sanitize_settings',
			'default'           => muukal_buyer_showcase_default_settings(),
		)
	);
}
add_action( 'admin_init', 'muukal_buyer_showcase_register_settings' );

/**
 * Add the settings page.
 */
function muukal_buyer_showcase_admin_menu() {
	add_menu_page(
		'Buyer Showcase',
		'Buyer Showcase',
		'manage_options',
		'muukal-buyer-showcase',
		'muukal_buyer_showcase_render_admin_page',
		'dashicons-format-gallery',
		59
	);
}
add_action( 'admin_menu', 'muukal_buyer_showcase_admin_menu' );

/**
 * Load admin assets.
 *
 * @param string $hook Current admin hook.
 */
function muukal_buyer_showcase_admin_assets( $hook ) {
	if ( false === strpos( $hook, 'muukal-buyer-showcase' ) ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style( 'muukal-buyer-showcase-style', MUUKAL_BUYER_SHOWCASE_URL . 'assets/buyer-showcase.css', array(), muukal_buyer_showcase_asset_version( 'assets/buyer-showcase.css' ) );
	wp_enqueue_script( 'muukal-buyer-showcase-admin', MUUKAL_BUYER_SHOWCASE_URL . 'assets/admin.js', array( 'jquery' ), muukal_buyer_showcase_asset_version( 'assets/admin.js' ), true );
}
add_action( 'admin_enqueue_scripts', 'muukal_buyer_showcase_admin_assets' );

/**
 * Register front-end assets.
 */
function muukal_buyer_showcase_register_assets() {
	wp_register_style( 'muukal-buyer-showcase-style', MUUKAL_BUYER_SHOWCASE_URL . 'assets/buyer-showcase.css', array(), muukal_buyer_showcase_asset_version( 'assets/buyer-showcase.css' ) );
	wp_register_script( 'muukal-buyer-showcase-script', MUUKAL_BUYER_SHOWCASE_URL . 'assets/buyer-showcase.js', array(), muukal_buyer_showcase_asset_version( 'assets/buyer-showcase.js' ), true );
}
add_action( 'init', 'muukal_buyer_showcase_register_assets' );

/**
 * Resolve product-backed data for one showcase row.
 *
 * @param array<string, mixed> $item Saved item configuration.
 * @param string               $default_button Default CTA label.
 * @return array<string, string>
 */
function muukal_buyer_showcase_resolve_item( $item, $default_button ) {
	$product_id   = absint( $item['product_id'] ?? 0 );
	$image_url    = esc_url_raw( $item['image_url'] ?? '' );
	$product_url  = esc_url_raw( $item['product_url'] ?? '' );
	$quickview    = esc_url_raw( $item['quickview_url'] ?? '' );
	$title        = sanitize_text_field( $item['title'] ?? '' );
	$price        = sanitize_text_field( $item['price'] ?? '' );
	$description  = sanitize_textarea_field( $item['description'] ?? '' );
	$button_label = sanitize_text_field( $item['button_label'] ?? '' );
	$gid          = sanitize_text_field( $item['gid'] ?? '' );

	if ( $product_id && function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( $product_id );

		if ( $product ) {
			$product_url = $product->get_permalink();
			$title       = $product->get_name();

			if ( '' === $price ) {
				$price = wp_strip_all_tags( $product->get_price_html() );
			}

			if ( '' === $description ) {
				$description = wp_strip_all_tags( $product->get_short_description() );
			}

			if ( '' === $image_url ) {
				$image_id = $product->get_image_id();

				if ( $image_id ) {
					$image_url = wp_get_attachment_image_url( $image_id, 'large' );
				}
			}
		}
	}

	if ( '' === $title ) {
		$title = '' !== $gid ? 'Buyer Favorite #' . $gid : 'Buyer Favorite';
	}

	if ( '' === $description ) {
		$description = 'Open the product page to shop this look.';
	}

	if ( '' === $product_url && '' !== $quickview ) {
		$product_url = $quickview;
	}

	if ( '' === $button_label ) {
		$button_label = $default_button;
	}

	return array(
		'gid'           => $gid,
		'image_url'     => $image_url,
		'title'         => $title,
		'price'         => $price,
		'description'   => $description,
		'product_url'   => $product_url,
		'quickview_url' => $quickview,
		'button_label'  => $button_label,
	);
}

/**
 * Render the admin page row markup.
 *
 * @param int                  $index Row index.
 * @param array<string, mixed> $item Item config.
 */
function muukal_buyer_showcase_render_admin_row( $index, $item ) {
	$image_url = esc_url( $item['image_url'] ?? '' );
	?>
	<tr class="muukal-buyer-showcase-admin__row">
		<td class="muukal-buyer-showcase-admin__preview-cell">
			<div class="muukal-buyer-showcase-admin__preview">
				<?php if ( $image_url ) : ?>
					<img src="<?php echo $image_url; ?>" alt="" />
				<?php endif; ?>
			</div>
			<div class="muukal-buyer-showcase-admin__image-controls">
				<input type="url" class="regular-text muukal-buyer-showcase-image-field" name="muukal_buyer_showcase_settings[items][<?php echo esc_attr( $index ); ?>][image_url]" value="<?php echo $image_url; ?>" placeholder="https://..." />
				<button type="button" class="button muukal-buyer-showcase-upload"><?php esc_html_e( 'Select Image', 'muukal-buyer-showcase' ); ?></button>
			</div>
		</td>
		<td><input type="text" class="small-text" name="muukal_buyer_showcase_settings[items][<?php echo esc_attr( $index ); ?>][gid]" value="<?php echo esc_attr( $item['gid'] ?? '' ); ?>" /></td>
		<td><input type="number" class="small-text" min="0" name="muukal_buyer_showcase_settings[items][<?php echo esc_attr( $index ); ?>][product_id]" value="<?php echo esc_attr( $item['product_id'] ?? 0 ); ?>" /></td>
		<td><input type="url" class="regular-text" name="muukal_buyer_showcase_settings[items][<?php echo esc_attr( $index ); ?>][product_url]" value="<?php echo esc_attr( $item['product_url'] ?? '' ); ?>" placeholder="Local or external product URL" /></td>
		<td><input type="url" class="regular-text" name="muukal_buyer_showcase_settings[items][<?php echo esc_attr( $index ); ?>][quickview_url]" value="<?php echo esc_attr( $item['quickview_url'] ?? '' ); ?>" placeholder="Optional quickview URL" /></td>
		<td><input type="text" class="regular-text" name="muukal_buyer_showcase_settings[items][<?php echo esc_attr( $index ); ?>][title]" value="<?php echo esc_attr( $item['title'] ?? '' ); ?>" /></td>
		<td><input type="text" class="small-text" name="muukal_buyer_showcase_settings[items][<?php echo esc_attr( $index ); ?>][price]" value="<?php echo esc_attr( $item['price'] ?? '' ); ?>" placeholder="$29.95" /></td>
		<td><textarea rows="3" class="large-text" name="muukal_buyer_showcase_settings[items][<?php echo esc_attr( $index ); ?>][description]"><?php echo esc_textarea( $item['description'] ?? '' ); ?></textarea></td>
		<td><input type="text" class="regular-text" name="muukal_buyer_showcase_settings[items][<?php echo esc_attr( $index ); ?>][button_label]" value="<?php echo esc_attr( $item['button_label'] ?? '' ); ?>" /></td>
		<td><button type="button" class="button-link-delete muukal-buyer-showcase-remove-row"><?php esc_html_e( 'Remove', 'muukal-buyer-showcase' ); ?></button></td>
	</tr>
	<?php
}

/**
 * Render the admin settings page.
 */
function muukal_buyer_showcase_render_admin_page() {
	$settings = muukal_buyer_showcase_get_settings();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Muukal Buyer Showcase', 'muukal-buyer-showcase' ); ?></h1>
		<p><?php esc_html_e( 'Use shortcode [muukal_buyer_showcase] anywhere you want this grid to appear. Product ID takes priority over Product URL, so you can link each card to a local WooCommerce product when available.', 'muukal-buyer-showcase' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'muukal_buyer_showcase_group' ); ?>

			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><label for="muukal-buyer-showcase-heading"><?php esc_html_e( 'Heading', 'muukal-buyer-showcase' ); ?></label></th>
						<td><input id="muukal-buyer-showcase-heading" name="muukal_buyer_showcase_settings[heading]" type="text" class="regular-text" value="<?php echo esc_attr( $settings['heading'] ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="muukal-buyer-showcase-subheading"><?php esc_html_e( 'Subheading', 'muukal-buyer-showcase' ); ?></label></th>
						<td><input id="muukal-buyer-showcase-subheading" name="muukal_buyer_showcase_settings[subheading]" type="text" class="large-text" value="<?php echo esc_attr( $settings['subheading'] ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="muukal-buyer-showcase-columns"><?php esc_html_e( 'Desktop columns', 'muukal-buyer-showcase' ); ?></label></th>
						<td><input id="muukal-buyer-showcase-columns" name="muukal_buyer_showcase_settings[columns]" type="number" min="2" max="6" class="small-text" value="<?php echo esc_attr( $settings['columns'] ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="muukal-buyer-showcase-button"><?php esc_html_e( 'Default button label', 'muukal-buyer-showcase' ); ?></label></th>
						<td><input id="muukal-buyer-showcase-button" name="muukal_buyer_showcase_settings[button_label]" type="text" class="regular-text" value="<?php echo esc_attr( $settings['button_label'] ); ?>" /></td>
					</tr>
				</tbody>
			</table>

			<h2><?php esc_html_e( 'Showcase Items', 'muukal-buyer-showcase' ); ?></h2>

			<div class="muukal-buyer-showcase-admin__table-wrap">
				<table class="widefat striped muukal-buyer-showcase-admin__table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Image', 'muukal-buyer-showcase' ); ?></th>
							<th><?php esc_html_e( 'GID', 'muukal-buyer-showcase' ); ?></th>
							<th><?php esc_html_e( 'Product ID', 'muukal-buyer-showcase' ); ?></th>
							<th><?php esc_html_e( 'Product URL', 'muukal-buyer-showcase' ); ?></th>
							<th><?php esc_html_e( 'Quickview URL', 'muukal-buyer-showcase' ); ?></th>
							<th><?php esc_html_e( 'Title', 'muukal-buyer-showcase' ); ?></th>
							<th><?php esc_html_e( 'Price', 'muukal-buyer-showcase' ); ?></th>
							<th><?php esc_html_e( 'Description', 'muukal-buyer-showcase' ); ?></th>
							<th><?php esc_html_e( 'Button Label', 'muukal-buyer-showcase' ); ?></th>
							<th><?php esc_html_e( 'Action', 'muukal-buyer-showcase' ); ?></th>
						</tr>
					</thead>
					<tbody id="muukal-buyer-showcase-rows">
						<?php foreach ( $settings['items'] as $index => $item ) : ?>
							<?php muukal_buyer_showcase_render_admin_row( (int) $index, $item ); ?>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<p>
				<button type="button" class="button" id="muukal-buyer-showcase-add-row"><?php esc_html_e( 'Add Item', 'muukal-buyer-showcase' ); ?></button>
			</p>

			<?php submit_button(); ?>
		</form>

		<script type="text/html" id="tmpl-muukal-buyer-showcase-row">
			<?php muukal_buyer_showcase_render_admin_row( '{{{data.index}}}', array() ); ?>
		</script>
	</div>
	<?php
}

/**
 * Render the shortcode output.
 *
 * @param array<string, string> $atts Shortcode attributes.
 * @return string
 */
function muukal_buyer_showcase_shortcode( $atts = array() ) {
	$settings = muukal_buyer_showcase_get_settings();
	$atts     = shortcode_atts(
		array(
			'heading'    => $settings['heading'],
			'subheading' => $settings['subheading'],
			'columns'    => (string) $settings['columns'],
			'limit'      => '16',
		),
		$atts,
		'muukal_buyer_showcase'
	);

	$columns = min( 6, max( 2, absint( $atts['columns'] ) ) );
	$limit   = max( 1, absint( $atts['limit'] ) );
	$items   = array_slice( $settings['items'], 0, $limit );

	if ( empty( $items ) ) {
		return '';
	}

	wp_enqueue_style( 'muukal-buyer-showcase-style' );
	wp_enqueue_script( 'muukal-buyer-showcase-script' );

	$resolved_items = array();
	foreach ( $items as $item ) {
		$resolved_items[] = muukal_buyer_showcase_resolve_item( $item, $settings['button_label'] );
	}

	$instance_id = 'muukal-buyer-showcase-' . wp_generate_uuid4();

	ob_start();
	?>
	<section class="muukal-buyer-showcase" style="--muukal-buyer-showcase-columns: <?php echo esc_attr( $columns ); ?>;" data-showcase-id="<?php echo esc_attr( $instance_id ); ?>">
		<div class="muukal-buyer-showcase__header">
			<h2><?php echo esc_html( $atts['heading'] ); ?></h2>
			<?php if ( '' !== trim( $atts['subheading'] ) ) : ?>
				<p><?php echo esc_html( $atts['subheading'] ); ?></p>
			<?php endif; ?>
		</div>

		<div class="muukal-buyer-showcase__grid">
			<?php foreach ( $resolved_items as $item ) : ?>
				<?php $payload = wp_json_encode( $item ); ?>
				<button type="button" class="muukal-buyer-showcase__card" data-item="<?php echo esc_attr( $payload ); ?>">
					<span class="muukal-buyer-showcase__image-wrap">
						<?php if ( '' !== $item['image_url'] ) : ?>
							<img src="<?php echo esc_url( $item['image_url'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" loading="lazy" decoding="async" />
						<?php endif; ?>
						<span class="muukal-buyer-showcase__overlay">
							<span class="muukal-buyer-showcase__overlay-label"><?php esc_html_e( 'Quick Look', 'muukal-buyer-showcase' ); ?></span>
						</span>
					</span>
				</button>
			<?php endforeach; ?>
		</div>

		<div class="muukal-buyer-showcase__modal" hidden>
			<div class="muukal-buyer-showcase__backdrop" data-close="1"></div>
			<div class="muukal-buyer-showcase__dialog" role="dialog" aria-modal="true" aria-labelledby="<?php echo esc_attr( $instance_id ); ?>-title">
				<button type="button" class="muukal-buyer-showcase__close" aria-label="<?php esc_attr_e( 'Close', 'muukal-buyer-showcase' ); ?>" data-close="1">&times;</button>
				<div class="muukal-buyer-showcase__modal-media">
					<img src="" alt="" />
				</div>
				<div class="muukal-buyer-showcase__modal-content">
					<div class="muukal-buyer-showcase__eyebrow"><?php esc_html_e( 'Buyer Show', 'muukal-buyer-showcase' ); ?></div>
					<h3 id="<?php echo esc_attr( $instance_id ); ?>-title"></h3>
					<div class="muukal-buyer-showcase__price"></div>
					<p class="muukal-buyer-showcase__description"></p>
					<div class="muukal-buyer-showcase__actions">
						<a class="muukal-buyer-showcase__button" href="#" target="_self" rel="noopener"></a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php

	return ob_get_clean();
}
add_shortcode( 'muukal_buyer_showcase', 'muukal_buyer_showcase_shortcode' );
