<?php
/**
 * Muukal product loop swatch markup.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build a fallback swatch row from the current product.
 *
 * @param WC_Product $product Product object.
 * @return array<string, string>
 */
function muukal_loop_swatch_build_fallback_row( $product ) {
	$main_image      = wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' );
	$gallery_ids     = $product->get_gallery_image_ids();
	$secondary_image = ! empty( $gallery_ids[0] ) ? wp_get_attachment_image_url( $gallery_ids[0], 'woocommerce_thumbnail' ) : $main_image;

	return array(
		'color_name'      => '',
		'color_slug'      => '',
		'color_id'        => '',
		'variant_slug'    => '',
		'dot_image'       => '',
		'main_image'      => $main_image ? $main_image : '',
		'secondary_image' => $secondary_image ? $secondary_image : '',
		'price'           => (string) $product->get_price(),
		'original_price'  => (string) $product->get_regular_price(),
		'is_default'      => '1',
	);
}

/**
 * Get the default row from a swatch list.
 *
 * @param array<int, array<string, string>> $rows Swatch rows.
 * @return array<string, string>
 */
function muukal_loop_swatch_get_default_row( $rows ) {
	$default_row = ! empty( $rows[0] ) && is_array( $rows[0] ) ? $rows[0] : array();

	foreach ( $rows as $row ) {
		if ( ! empty( $row['is_default'] ) ) {
			$default_row = $row;
			break;
		}
	}

	return $default_row;
}

/**
 * Build normalized state for a swatch row.
 *
 * @param WC_Product            $product       Product object.
 * @param array<string, string> $row           Swatch row.
 * @param array<string, string> $fallback_row  Product fallback row.
 * @return array<string, mixed>
 */
function muukal_loop_swatch_build_row_state( $product, $row, $fallback_row ) {
	$row = wp_parse_args(
		$row,
		array(
			'color_name'      => '',
			'color_slug'      => '',
			'color_id'        => '',
			'variant_slug'    => '',
			'dot_image'       => '',
			'main_image'      => '',
			'secondary_image' => '',
			'price'           => '',
			'original_price'  => '',
			'is_default'      => '',
		)
	);

	$product_url = get_permalink( $product->get_id() );
	$main_image  = ! empty( $row['main_image'] ) ? $row['main_image'] : $fallback_row['main_image'];
	$sec_image   = ! empty( $row['secondary_image'] ) ? $row['secondary_image'] : $main_image;
	$price       = '' !== $row['price'] ? $row['price'] : $fallback_row['price'];
	$origin      = '' !== $row['original_price'] ? $row['original_price'] : $fallback_row['original_price'];
	$discount    = 0;

	if ( '' !== $origin && '' !== $price && (float) $origin > (float) $price ) {
		$discount = (int) round( 100 - ( ( (float) $price / (float) $origin ) * 100 ) );
	}

	$link = $product_url;
	if ( ! empty( $row['variant_slug'] ) ) {
		$link = add_query_arg( 'mu_color', sanitize_title( $row['variant_slug'] ), $product_url );
	}

	return array(
		'main_image'          => $main_image,
		'secondary_image'     => $sec_image,
		'price_raw'           => $price,
		'original_price_raw'  => $origin,
		'price_html'          => '' !== $price ? wc_price( (float) $price ) : '',
		'original_price_html' => '' !== $origin ? wc_price( (float) $origin ) : '',
		'discount'            => $discount,
		'link'                => $link,
	);
}

/**
 * Resolve a product from shortcode args or the current loop context.
 *
 * @param array $args Shortcode args.
 * @return WC_Product|null
 */
function muukal_loop_swatch_resolve_product( $args ) {
	if ( ! function_exists( 'wc_get_product' ) ) {
		return null;
	}

	$product_id = isset( $args['product_id'] ) ? absint( $args['product_id'] ) : 0;

	if ( ! $product_id ) {
		$product_id = get_the_ID();
	}

	if ( ! $product_id && isset( $GLOBALS['product'] ) && $GLOBALS['product'] instanceof WC_Product ) {
		$product_id = $GLOBALS['product']->get_id();
	}

	if ( ! $product_id ) {
		return null;
	}

	$product = wc_get_product( $product_id );

	return $product instanceof WC_Product ? $product : null;
}

/**
 * Build query args for loop products.
 *
 * @param array $args Shortcode args.
 * @return array
 */
function muukal_loop_swatch_build_query_args( $args ) {
	$limit       = isset( $args['limit'] ) ? absint( $args['limit'] ) : 8;
	$product_ids = isset( $args['product_ids'] ) ? array_filter( array_map( 'absint', explode( ',', $args['product_ids'] ) ) ) : array();
	$category    = isset( $args['category'] ) ? sanitize_title( $args['category'] ) : '';
	$orderby     = isset( $args['orderby'] ) ? sanitize_key( $args['orderby'] ) : 'date';
	$order       = isset( $args['order'] ) ? strtoupper( sanitize_key( $args['order'] ) ) : 'DESC';

	$query_args = array(
		'status' => 'publish',
		'limit'  => $limit > 0 ? min( $limit, 24 ) : 8,
		'return' => 'ids',
		'order'  => in_array( $order, array( 'ASC', 'DESC' ), true ) ? $order : 'DESC',
	);

	if ( ! empty( $product_ids ) ) {
		$query_args['include'] = $product_ids;
	}

	if ( $category ) {
		$query_args['category'] = array( $category );
	}

	if ( in_array( $orderby, array( 'date', 'popularity', 'price', 'title', 'rand' ), true ) ) {
		$query_args['orderby'] = $orderby;
	}

	return $query_args;
}

/**
 * Get swatch rows from product meta.
 *
 * @param int $product_id Product ID.
 * @return array
 */
function muukal_loop_swatch_get_rows( $product_id ) {
	$rows = get_post_meta( $product_id, '_muukal_color_swatches', true );
	return is_array( $rows ) ? array_values( $rows ) : array();
}

/**
 * Render one product card for Elementor Loop Item.
 *
 * @param array $args Shortcode args.
 * @return string
 */
function muukal_render_product_loop_item( $args ) {
	$product = muukal_loop_swatch_resolve_product( $args );

	if ( ! $product ) {
		return '';
	}

	$product_id   = $product->get_id();
	$product_name = $product->get_name();
	$rows         = muukal_loop_swatch_get_rows( $product_id );
	$fallback_row = muukal_loop_swatch_build_fallback_row( $product );

	if ( empty( $rows ) ) {
		$rows = array( $fallback_row );
	}

	$default_row   = muukal_loop_swatch_get_default_row( $rows );
	$default_state = muukal_loop_swatch_build_row_state( $product, $default_row, $fallback_row );

	ob_start();
	?>
	<div class="muukal-loop-item" data-product-id="<?php echo esc_attr( $product_id ); ?>">
		<div id="goods_<?php echo esc_attr( $product_id ); ?>" class="product-wrapper muukal-product-card">
			<a class="product-img pro_a muukal-product-link" href="<?php echo esc_url( $default_state['link'] ); ?>">
				<img
					class="lazyload ip-img fir_pimg muukal-product-image muukal-product-image-primary"
					data-original="<?php echo esc_url( $default_state['main_image'] ); ?>"
					data-default-src="<?php echo esc_url( $default_state['main_image'] ); ?>"
					alt="<?php echo esc_attr( $product_name ); ?>"
					src="<?php echo esc_url( $default_state['main_image'] ); ?>"
				>
				<img
					class="lazyload ip-img sec_pimg muukal-product-image muukal-product-image-secondary"
					data-original="<?php echo esc_url( $default_state['secondary_image'] ); ?>"
					data-default-src="<?php echo esc_url( $default_state['secondary_image'] ); ?>"
					alt="<?php echo esc_attr( $product_name ); ?>"
					src="<?php echo esc_url( $default_state['secondary_image'] ); ?>"
				>
			</a>
			<div class="product-content">
				<div class="ip-colors">
					<div class="ip-colors-box text-left">
						<?php foreach ( $rows as $row_index => $row ) : ?>
							<?php
							$row_state = muukal_loop_swatch_build_row_state( $product, $row, $fallback_row );
							$is_active = ( 0 === $row_index && empty( $default_row['is_default'] ) ) || ! empty( $row['is_default'] );
							?>
							<?php if ( ! empty( $row['dot_image'] ) ) : ?>
								<button
									type="button"
									gid="<?php echo esc_attr( $product_id ); ?>"
									cid="<?php echo esc_attr( $row['color_id'] ); ?>"
									gcn="<?php echo esc_attr( $row['color_slug'] ); ?>"
									gname="<?php echo esc_attr( $row['variant_slug'] ); ?>"
									gp="<?php echo esc_attr( $row_state['price_raw'] ); ?>"
									gpr="<?php echo esc_attr( $row_state['discount'] ); ?>"
									ipimg="<?php echo esc_url( $row_state['main_image'] ); ?>"
									class="ip-cspan muukal-swatch-button<?php echo $is_active ? ' choose-color is-active' : ''; ?>"
									data-main="<?php echo esc_url( $row_state['main_image'] ); ?>"
									data-secondary="<?php echo esc_url( $row_state['secondary_image'] ); ?>"
									data-price="<?php echo esc_attr( $row_state['price_raw'] ); ?>"
									data-price-html="<?php echo esc_attr( wp_strip_all_tags( $row_state['price_html'] ) ); ?>"
									data-original-price="<?php echo esc_attr( $row_state['original_price_raw'] ); ?>"
									data-original-price-html="<?php echo esc_attr( wp_strip_all_tags( $row_state['original_price_html'] ) ); ?>"
									data-discount="<?php echo esc_attr( $row_state['discount'] ); ?>"
									data-link="<?php echo esc_url( $row_state['link'] ); ?>"
									aria-label="<?php echo esc_attr( $row['color_name'] ? $row['color_name'] : $product_name ); ?>"
								>
									<img class="ip-cimg" src="<?php echo esc_url( $row['dot_image'] ); ?>" alt="<?php echo esc_attr( $row['color_name'] ); ?>">
								</button>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
					<span class="pro-price fs16">
						<del class="fs16 fw300 o_price"<?php echo $default_state['discount'] > 0 ? '' : ' style="display: none;"'; ?>><?php echo wp_kses_post( $default_state['original_price_html'] ); ?></del>
						&nbsp;<span class="g-price"><?php echo wp_kses_post( $default_state['price_html'] ); ?></span>
					</span>
				</div>
				<div class="clear"></div>
			</div>
			<div class="sale-flag-side buyout-p"<?php echo $default_state['discount'] > 0 ? '' : ' style="display: none;"'; ?>>
				<span class="sale-text"><?php echo esc_html( $default_state['discount'] ); ?>% OFF</span>
			</div>
		</div>
	</div>
	<?php

	return ob_get_clean();
}

/**
 * Render product loop with color swatches.
 *
 * @param array $args Shortcode args.
 * @return string
 */
function muukal_render_product_loop_swatch( $args ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return '';
	}

	$query_args  = muukal_loop_swatch_build_query_args( $args );
	$product_ids = wc_get_products( $query_args );

	if ( empty( $product_ids ) ) {
		return '';
	}

	ob_start();
	?>
	<div class="muukal-loop-products">
		<div class="row">
			<?php foreach ( $product_ids as $product_id ) : ?>
				<?php
				$product = wc_get_product( $product_id );
				if ( ! $product ) {
					continue;
				}

				$rows = muukal_loop_swatch_get_rows( $product_id );
				$fallback_row = muukal_loop_swatch_build_fallback_row( $product );
				if ( empty( $rows ) ) {
					$rows = array( $fallback_row );
				}

				$default_row   = muukal_loop_swatch_get_default_row( $rows );
				$default_state = muukal_loop_swatch_build_row_state( $product, $default_row, $fallback_row );
				?>
				<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-sxl-4 col-xl-3 pb10 mb-30">
					<div id="goods_<?php echo esc_attr( $product_id ); ?>" class="product-wrapper">
						<a class="product-img pro_a" href="<?php echo esc_url( $default_state['link'] ); ?>">
							<img class="lazyload ip-img fir_pimg" data-original="<?php echo esc_url( $default_state['main_image'] ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" src="<?php echo esc_url( $default_state['main_image'] ); ?>" style="display: block;">
							<img class="lazyload ip-img sec_pimg" data-original="<?php echo esc_url( $default_state['secondary_image'] ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" src="<?php echo esc_url( $default_state['secondary_image'] ); ?>" style="display: inline;">
						</a>
						<div class="product-content">
							<div class="ip-colors">
								<div class="ip-colors-box text-left">
									<?php foreach ( $rows as $row_index => $row ) : ?>
										<?php
										$row_state = muukal_loop_swatch_build_row_state( $product, $row, $fallback_row );
										?>
										<?php if ( ! empty( $row['dot_image'] ) ) : ?>
											<span
												gid="<?php echo esc_attr( $product_id ); ?>"
												cid="<?php echo esc_attr( $row['color_id'] ); ?>"
												gcn="<?php echo esc_attr( $row['color_slug'] ); ?>"
												gname="<?php echo esc_attr( $row['variant_slug'] ); ?>"
												gp="<?php echo esc_attr( $row_state['price_raw'] ); ?>"
												gpr="<?php echo esc_attr( $row_state['discount'] ); ?>"
												ipimg="<?php echo esc_url( $row_state['main_image'] ); ?>"
												class="ip-cspan<?php echo 0 === $row_index || ! empty( $row['is_default'] ) ? ' choose-color' : ''; ?>"
												data-main="<?php echo esc_url( $row_state['main_image'] ); ?>"
												data-secondary="<?php echo esc_url( $row_state['secondary_image'] ); ?>"
												data-price="<?php echo esc_attr( $row_state['price_raw'] ); ?>"
												data-original-price="<?php echo esc_attr( $row_state['original_price_raw'] ); ?>"
												data-discount="<?php echo esc_attr( $row_state['discount'] ); ?>"
												data-link="<?php echo esc_url( $row_state['link'] ); ?>"
											>
												<img class="ip-cimg" src="<?php echo esc_url( $row['dot_image'] ); ?>" alt="<?php echo esc_attr( $row['color_name'] ); ?>">
											</span>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
								<span class="pro-price fs16">
									<del class="fs16 fw300 o_price"<?php echo $default_state['discount'] > 0 ? '' : ' style="display: none;"'; ?>><?php echo wp_kses_post( $default_state['original_price_html'] ); ?></del>
									&nbsp;<span class="g-price"><?php echo wp_kses_post( $default_state['price_html'] ); ?></span>
								</span>
							</div>
							<div class="clear"></div>
						</div>
						<div class="sale-flag-side buyout-p"<?php echo $default_state['discount'] > 0 ? '' : ' style="display: none;"'; ?>><span class="sale-text"><?php echo esc_html( $default_state['discount'] ); ?>% OFF</span></div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php

	return ob_get_clean();
}
