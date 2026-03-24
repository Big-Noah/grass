<?php
/**
 * Plugin Name: Muukal Product Color Swatches
 * Description: Adds product-level color swatch data fields for custom frontend loop cards.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default color palette collected from muukal ip-cimg assets.
 *
 * @return array<int, array<string, string>>
 */
function muukal_swatch_default_palette() {
	return array(
		array( 'color_name' => 'Black',    'color_slug' => 'black',    'color_id' => '4',  'dot_image' => 'https://img.muukal.com/img/color/color-black.png' ),
		array( 'color_name' => 'Blue',     'color_slug' => 'blue',     'color_id' => '14', 'dot_image' => 'https://img.muukal.com/img/color/color-blue.png' ),
		array( 'color_name' => 'Brown',    'color_slug' => 'brown',    'color_id' => '10', 'dot_image' => 'https://img.muukal.com/img/color/color-brown.png' ),
		array( 'color_name' => 'Clear',    'color_slug' => 'clear',    'color_id' => '6',  'dot_image' => 'https://img.muukal.com/img/color/color-clear.png' ),
		array( 'color_name' => 'Colorful', 'color_slug' => 'colorful', 'color_id' => '22', 'dot_image' => 'https://img.muukal.com/img/color/color-colorful.png' ),
		array( 'color_name' => 'Gold',     'color_slug' => 'gold',     'color_id' => '16', 'dot_image' => 'https://img.muukal.com/img/color/color-gold.png' ),
		array( 'color_name' => 'Gray',     'color_slug' => 'gray',     'color_id' => '5',  'dot_image' => 'https://img.muukal.com/img/color/color-gray.png' ),
		array( 'color_name' => 'Green',    'color_slug' => 'green',    'color_id' => '13', 'dot_image' => 'https://img.muukal.com/img/color/color-green.png' ),
		array( 'color_name' => 'Mix',      'color_slug' => 'mix',      'color_id' => '21', 'dot_image' => 'https://img.muukal.com/img/color/color-mix.png' ),
		array( 'color_name' => 'Orange',   'color_slug' => 'orange',   'color_id' => '11', 'dot_image' => 'https://img.muukal.com/img/color/color-orange.png' ),
		array( 'color_name' => 'Pink',     'color_slug' => 'pink',     'color_id' => '8',  'dot_image' => 'https://img.muukal.com/img/color/color-pink.png' ),
		array( 'color_name' => 'Purple',   'color_slug' => 'purple',   'color_id' => '15', 'dot_image' => 'https://img.muukal.com/img/color/color-purple.png' ),
		array( 'color_name' => 'Red',      'color_slug' => 'red',      'color_id' => '9',  'dot_image' => 'https://img.muukal.com/img/color/color-red.png' ),
		array( 'color_name' => 'Silver',   'color_slug' => 'silver',   'color_id' => '17', 'dot_image' => 'https://img.muukal.com/img/color/color-silver.png' ),
		array( 'color_name' => 'Tortoise', 'color_slug' => 'tortoise', 'color_id' => '2',  'dot_image' => 'https://img.muukal.com/img/color/color-tortoise.png' ),
	);
}

/**
 * Register product metabox.
 */
function muukal_swatch_register_product_metabox() {
	add_meta_box(
		'muukal_product_swatches',
		'Muukal Color Swatches',
		'muukal_swatch_render_metabox',
		'product',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'muukal_swatch_register_product_metabox' );

/**
 * Enqueue media scripts for product edit.
 *
 * @param string $hook Current hook.
 */
function muukal_swatch_admin_assets( $hook ) {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'product' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'muukal_swatch_admin_assets' );

/**
 * Render swatch metabox.
 *
 * @param WP_Post $post Product post.
 */
function muukal_swatch_render_metabox( $post ) {
	wp_nonce_field( 'muukal_swatch_save', 'muukal_swatch_nonce' );

	$rows = get_post_meta( $post->ID, '_muukal_color_swatches', true );
	if ( ! is_array( $rows ) ) {
		$rows = array();
	}
	?>
	<div id="muukal-swatch-root">
		<p>Manage per-color data for custom loop cards (dot icon, main/secondary image, price override).</p>
		<p>
			<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=muukal-swatch-tools' ) ); ?>">Open Swatch Tools</a>
			<span style="margin-left:8px;color:#666;">Use tools page to bulk seed random 3 colors for all products.</span>
		</p>
		<table class="widefat striped" id="muukal-swatch-table">
			<thead>
				<tr>
					<th style="width:120px;">Color Name</th>
					<th style="width:90px;">Color Slug</th>
					<th style="width:70px;">Color ID</th>
					<th style="width:130px;">Variant Slug</th>
					<th>Dot Image</th>
					<th>Main Image</th>
					<th>Secondary Image</th>
					<th style="width:110px;">Price</th>
					<th style="width:110px;">Original Price</th>
					<th style="width:70px;">Default</th>
					<th style="width:60px;">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $rows as $index => $row ) : ?>
					<?php muukal_swatch_render_row( $index, $row ); ?>
				<?php endforeach; ?>
			</tbody>
		</table>
		<p style="margin-top:10px;">
			<button type="button" class="button button-primary" id="muukal-swatch-add-row">Add Swatch</button>
		</p>
	</div>

	<script type="text/html" id="tmpl-muukal-swatch-row">
		<?php muukal_swatch_render_row( '{{{data.index}}}', array() ); ?>
	</script>

	<style>
		.muukal-swatch-media{display:flex;gap:8px;align-items:center}
		.muukal-swatch-preview{width:36px;height:36px;object-fit:cover;border:1px solid #ddd;background:#fafafa}
		#muukal-swatch-table input[type="text"],#muukal-swatch-table input[type="number"]{width:100%}
	</style>

	<script>
	(function() {
	  const root = document.getElementById('muukal-swatch-root');
	  if (!root) return;

	  const tbody = root.querySelector('#muukal-swatch-table tbody');
	  const addBtn = root.querySelector('#muukal-swatch-add-row');
	  const templateEl = document.getElementById('tmpl-muukal-swatch-row');

	  function getNextIndex() {
	    return tbody.querySelectorAll('tr').length;
	  }

	  function openMedia(inputEl, previewEl) {
	    const frame = wp.media({
	      title: 'Select image',
	      button: { text: 'Use this image' },
	      multiple: false
	    });
	    frame.on('select', function() {
	      const att = frame.state().get('selection').first().toJSON();
	      inputEl.value = att.url || '';
	      previewEl.src = att.url || '';
	    });
	    frame.open();
	  }

	  addBtn.addEventListener('click', function() {
	    if (!templateEl) return;
	    const index = getNextIndex();
	    const html = templateEl.innerHTML.split('{{{data.index}}}').join(String(index));
	    tbody.insertAdjacentHTML('beforeend', html);
	  });

	  root.addEventListener('click', function(e) {
	    const removeBtn = e.target.closest('.muukal-swatch-remove');
	    if (removeBtn) {
	      const tr = removeBtn.closest('tr');
	      if (tr) tr.remove();
	      return;
	    }

	    const pickBtn = e.target.closest('.muukal-swatch-pick');
	    if (pickBtn) {
	      const wrap = pickBtn.closest('.muukal-swatch-media');
	      const input = wrap.querySelector('input[type="text"]');
	      const img = wrap.querySelector('.muukal-swatch-preview');
	      openMedia(input, img);
	    }
	  });
	})();
	</script>
	<?php
}

/**
 * Render one swatch row.
 *
 * @param int|string $index Row index.
 * @param array      $row   Row data.
 */
function muukal_swatch_render_row( $index, $row ) {
	$defaults = array(
		'color_name'     => '',
		'color_slug'     => '',
		'color_id'       => '',
		'variant_slug'   => '',
		'dot_image'      => '',
		'main_image'     => '',
		'secondary_image'=> '',
		'price'          => '',
		'original_price' => '',
		'is_default'     => '',
	);
	$row = wp_parse_args( $row, $defaults );
	$k   = 'muukal_swatches[' . $index . ']';
	?>
	<tr>
		<td><input type="text" name="<?php echo esc_attr( $k ); ?>[color_name]" value="<?php echo esc_attr( $row['color_name'] ); ?>" /></td>
		<td><input type="text" name="<?php echo esc_attr( $k ); ?>[color_slug]" value="<?php echo esc_attr( $row['color_slug'] ); ?>" /></td>
		<td><input type="text" name="<?php echo esc_attr( $k ); ?>[color_id]" value="<?php echo esc_attr( $row['color_id'] ); ?>" /></td>
		<td><input type="text" name="<?php echo esc_attr( $k ); ?>[variant_slug]" value="<?php echo esc_attr( $row['variant_slug'] ); ?>" /></td>
		<td><?php muukal_swatch_media_cell( $k, 'dot_image', $row['dot_image'] ); ?></td>
		<td><?php muukal_swatch_media_cell( $k, 'main_image', $row['main_image'] ); ?></td>
		<td><?php muukal_swatch_media_cell( $k, 'secondary_image', $row['secondary_image'] ); ?></td>
		<td><input type="number" step="0.01" min="0" name="<?php echo esc_attr( $k ); ?>[price]" value="<?php echo esc_attr( $row['price'] ); ?>" /></td>
		<td><input type="number" step="0.01" min="0" name="<?php echo esc_attr( $k ); ?>[original_price]" value="<?php echo esc_attr( $row['original_price'] ); ?>" /></td>
		<td style="text-align:center;"><input type="radio" name="muukal_swatches_default" value="<?php echo esc_attr( $index ); ?>" <?php checked( ! empty( $row['is_default'] ) ); ?> /></td>
		<td><button type="button" class="button-link-delete muukal-swatch-remove">Remove</button></td>
	</tr>
	<?php
}

/**
 * Render media picker cell.
 *
 * @param string $base Base key.
 * @param string $field Field key.
 * @param string $url Image URL.
 */
function muukal_swatch_media_cell( $base, $field, $url ) {
	?>
	<div class="muukal-swatch-media">
		<img class="muukal-swatch-preview" src="<?php echo esc_url( $url ); ?>" alt="" />
		<input type="text" name="<?php echo esc_attr( $base ); ?>[<?php echo esc_attr( $field ); ?>]" value="<?php echo esc_attr( $url ); ?>" />
		<button type="button" class="button muukal-swatch-pick">Pick</button>
	</div>
	<?php
}

/**
 * Save swatch meta.
 *
 * @param int $post_id Product ID.
 */
function muukal_swatch_save_product_meta( $post_id ) {
	if ( ! isset( $_POST['muukal_swatch_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['muukal_swatch_nonce'] ) ), 'muukal_swatch_save' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$rows          = isset( $_POST['muukal_swatches'] ) ? (array) wp_unslash( $_POST['muukal_swatches'] ) : array();
	$default_index = isset( $_POST['muukal_swatches_default'] ) ? sanitize_text_field( wp_unslash( $_POST['muukal_swatches_default'] ) ) : '';
	$clean         = array();

	foreach ( $rows as $idx => $row ) {
		$item = array(
			'color_name'      => isset( $row['color_name'] ) ? sanitize_text_field( $row['color_name'] ) : '',
			'color_slug'      => isset( $row['color_slug'] ) ? sanitize_title( $row['color_slug'] ) : '',
			'color_id'        => isset( $row['color_id'] ) ? sanitize_text_field( $row['color_id'] ) : '',
			'variant_slug'    => isset( $row['variant_slug'] ) ? sanitize_title( $row['variant_slug'] ) : '',
			'dot_image'       => isset( $row['dot_image'] ) ? esc_url_raw( $row['dot_image'] ) : '',
			'main_image'      => isset( $row['main_image'] ) ? esc_url_raw( $row['main_image'] ) : '',
			'secondary_image' => isset( $row['secondary_image'] ) ? esc_url_raw( $row['secondary_image'] ) : '',
			'price'           => isset( $row['price'] ) ? wc_format_decimal( $row['price'] ) : '',
			'original_price'  => isset( $row['original_price'] ) ? wc_format_decimal( $row['original_price'] ) : '',
			'is_default'      => ( (string) $idx === (string) $default_index ) ? '1' : '',
		);

		if ( '' === $item['color_name'] && '' === $item['color_slug'] && '' === $item['main_image'] ) {
			continue;
		}

		$clean[] = $item;
	}

	if ( empty( $clean ) ) {
		delete_post_meta( $post_id, '_muukal_color_swatches' );
	} else {
		update_post_meta( $post_id, '_muukal_color_swatches', array_values( $clean ) );
	}
}
add_action( 'save_post_product', 'muukal_swatch_save_product_meta' );

/**
 * Add tools subpage.
 */
function muukal_swatch_add_tools_page() {
	add_submenu_page(
		'woocommerce',
		'Muukal Swatch Tools',
		'Swatch Tools',
		'manage_woocommerce',
		'muukal-swatch-tools',
		'muukal_swatch_render_tools_page'
	);
}
add_action( 'admin_menu', 'muukal_swatch_add_tools_page' );

/**
 * Render tools UI.
 */
function muukal_swatch_render_tools_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}

	$palette = muukal_swatch_default_palette();
	$count   = count( $palette );
	$url     = wp_nonce_url(
		admin_url( 'admin-post.php?action=muukal_seed_demo_swatches' ),
		'muukal_seed_demo_swatches'
	);
	?>
	<div class="wrap">
		<h1>Muukal Swatch Tools</h1>
		<p>Default palette loaded: <strong><?php echo esc_html( $count ); ?></strong> colors.</p>
		<p>Click the button below to assign <strong>3 random colors</strong> to each published product.</p>
		<p>This writes product meta key <code>_muukal_color_swatches</code> and can be run multiple times.</p>
		<p><a class="button button-primary" href="<?php echo esc_url( $url ); ?>">Seed Random 3 Colors For All Products</a></p>
		<?php if ( isset( $_GET['seeded'] ) ) : ?>
			<p><strong>Done.</strong> Updated products: <?php echo esc_html( absint( $_GET['seeded'] ) ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Build demo swatches for a product.
 *
 * @param WC_Product $product Product.
 * @return array<int, array<string, string>>
 */
function muukal_swatch_build_demo_rows_for_product( $product ) {
	$palette = muukal_swatch_default_palette();
	shuffle( $palette );
	$picked = array_slice( $palette, 0, 3 );

	$main_image      = wp_get_attachment_image_url( $product->get_image_id(), 'full' );
	$gallery_ids     = $product->get_gallery_image_ids();
	$secondary_image = ! empty( $gallery_ids[0] ) ? wp_get_attachment_image_url( $gallery_ids[0], 'full' ) : $main_image;
	$price           = (string) $product->get_price();
	$original_price  = (string) $product->get_regular_price();
	$product_slug    = $product->get_slug();

	$rows = array();
	foreach ( $picked as $i => $sw ) {
		$rows[] = array(
			'color_name'      => $sw['color_name'],
			'color_slug'      => $sw['color_slug'],
			'color_id'        => $sw['color_id'],
			'variant_slug'    => $sw['color_slug'] . '-' . $product_slug,
			'dot_image'       => $sw['dot_image'],
			'main_image'      => $main_image ? esc_url_raw( $main_image ) : '',
			'secondary_image' => $secondary_image ? esc_url_raw( $secondary_image ) : '',
			'price'           => $price,
			'original_price'  => $original_price,
			'is_default'      => 0 === $i ? '1' : '',
		);
	}

	return $rows;
}

/**
 * Batch seed swatches for all published products.
 */
function muukal_swatch_seed_demo_swatches() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( 'No permission.' );
	}

	check_admin_referer( 'muukal_seed_demo_swatches' );

	if ( ! function_exists( 'wc_get_products' ) ) {
		wp_die( 'WooCommerce not available.' );
	}

	$product_ids = wc_get_products(
		array(
			'status' => 'publish',
			'limit'  => -1,
			'return' => 'ids',
		)
	);

	$updated = 0;
	foreach ( $product_ids as $product_id ) {
		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			continue;
		}
		$rows = muukal_swatch_build_demo_rows_for_product( $product );
		update_post_meta( $product_id, '_muukal_color_swatches', $rows );
		$updated++;
	}

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'   => 'muukal-swatch-tools',
				'seeded' => $updated,
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_post_muukal_seed_demo_swatches', 'muukal_swatch_seed_demo_swatches' );
