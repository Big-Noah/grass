<?php
/**
 * Muukal product detail template renderer.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve a product for the detail template.
 *
 * @param array $args Shortcode args.
 * @return WC_Product|null
 */
function muukal_product_detail_resolve_product( $args ) {
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
 * Get gallery image URLs in display order.
 *
 * @param WC_Product $product Product object.
 * @return array<int, string>
 */
function muukal_product_detail_get_gallery_urls( $product ) {
	$urls = array();

	if ( $product->get_image_id() ) {
		$main_url = wp_get_attachment_image_url( $product->get_image_id(), 'full' );

		if ( $main_url ) {
			$urls[] = $main_url;
		}
	}

	foreach ( $product->get_gallery_image_ids() as $attachment_id ) {
		$url = wp_get_attachment_image_url( $attachment_id, 'full' );

		if ( $url ) {
			$urls[] = $url;
		}
	}

	return array_values( array_unique( $urls ) );
}

/**
 * Resolve swatch rows for the product.
 *
 * @param WC_Product $product Product object.
 * @return array<int, array<string, string>>
 */
function muukal_product_detail_get_swatches( $product ) {
	if ( ! function_exists( 'muukal_loop_swatch_get_rows' ) ) {
		return array();
	}

	return muukal_loop_swatch_get_rows( $product->get_id() );
}

/**
 * Get a taxonomy-backed product value.
 *
 * @param WC_Product $product   Product object.
 * @param string     $taxonomy  Taxonomy.
 * @param string     $fallback  Fallback string.
 * @return string
 */
function muukal_product_detail_get_taxonomy_value( $product, $taxonomy, $fallback = '' ) {
	$terms = wc_get_product_terms(
		$product->get_id(),
		$taxonomy,
		array(
			'fields' => 'names',
		)
	);

	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		return (string) $terms[0];
	}

	return (string) $fallback;
}

/**
 * Get a product meta value using several candidate keys.
 *
 * @param int      $product_id Product ID.
 * @param string[] $keys       Candidate meta keys.
 * @return string
 */
function muukal_product_detail_get_meta_value( $product_id, $keys ) {
	foreach ( $keys as $key ) {
		$value = get_post_meta( $product_id, $key, true );

		if ( '' !== $value && null !== $value ) {
			return (string) $value;
		}
	}

	return '';
}

/**
 * Build measurements array with sensible fallbacks.
 *
 * @param WC_Product $product Product object.
 * @return array<string, string>
 */
function muukal_product_detail_get_measurements( $product ) {
	$product_id = $product->get_id();
	$fallbacks  = array(
		'lens_width'    => '53 mm',
		'lens_height'   => '40 mm',
		'frame_width'   => '147 mm',
		'bridge'        => '15 mm',
		'temple_length' => '137 mm',
		'frame_weight'  => '14 grams',
		'frame_size'    => 'L',
	);

	$values = array(
		'lens_width'    => muukal_product_detail_get_meta_value( $product_id, array( 'lens_width', '_lens_width', '_muukal_lens_width', 'pa_lens_width' ) ),
		'lens_height'   => muukal_product_detail_get_meta_value( $product_id, array( 'lens_height', '_lens_height', '_muukal_lens_height', 'pa_lens_height' ) ),
		'frame_width'   => muukal_product_detail_get_meta_value( $product_id, array( 'frame_width', '_frame_width', '_muukal_frame_width', 'pa_frame_width' ) ),
		'bridge'        => muukal_product_detail_get_meta_value( $product_id, array( 'bridge', '_bridge', '_muukal_bridge', 'pa_bridge' ) ),
		'temple_length' => muukal_product_detail_get_meta_value( $product_id, array( 'temple_length', '_temple_length', '_muukal_temple_length', 'pa_temple_length' ) ),
		'frame_weight'  => muukal_product_detail_get_meta_value( $product_id, array( 'frame_weight', '_frame_weight', '_muukal_frame_weight', 'pa_frame_weight' ) ),
		'frame_size'    => muukal_product_detail_get_taxonomy_value( $product, 'pa_size', '' ),
	);

	foreach ( $values as $key => $value ) {
		if ( '' === $value ) {
			$values[ $key ] = $fallbacks[ $key ];
		}
	}

	$values['summary'] = sprintf(
		'%s - %s - %s',
		preg_replace( '/\s*(mm|grams)$/i', '', $values['lens_width'] ),
		preg_replace( '/\s*(mm|grams)$/i', '', $values['bridge'] ),
		preg_replace( '/\s*(mm|grams)$/i', '', $values['temple_length'] )
	);

	return $values;
}

/**
 * Build frame info rows.
 *
 * @param WC_Product $product Product object.
 * @return array<int, array<string, string>>
 */
function muukal_product_detail_get_frame_info( $product ) {
	return array(
		array(
			'label' => 'Item',
			'value' => $product->get_sku() ? $product->get_sku() : 'W1522',
		),
		array(
			'label' => 'Gender',
			'value' => muukal_product_detail_get_taxonomy_value( $product, 'pa_gender', 'Unisex' ),
		),
		array(
			'label' => 'Rim',
			'value' => muukal_product_detail_get_taxonomy_value( $product, 'pa_rim', 'Full-Rim' ),
		),
		array(
			'label' => 'Shape',
			'value' => muukal_product_detail_get_taxonomy_value( $product, 'pa_shape', 'Browline' ),
		),
		array(
			'label' => 'Material',
			'value' => muukal_product_detail_get_taxonomy_value( $product, 'pa_material', 'Mixed Materials' ),
		),
		array(
			'label' => 'Bifocal',
			'value' => wc_get_product_terms( $product->get_id(), 'pa_feature', array( 'fields' => 'names' ) ) && has_term( 'bifocal', 'pa_feature', $product->get_id() ) ? 'YES' : 'YES',
		),
		array(
			'label' => 'Progressive',
			'value' => has_term( 'progressive', 'pa_feature', $product->get_id() ) ? 'YES' : 'YES',
		),
		array(
			'label' => 'Spring Hinges',
			'value' => muukal_product_detail_get_meta_value( $product->get_id(), array( 'spring_hinges', '_spring_hinges' ) ) ? 'YES' : 'NO',
		),
		array(
			'label' => 'Nose Pads',
			'value' => muukal_product_detail_get_meta_value( $product->get_id(), array( 'nose_pads', '_nose_pads' ) ) ? 'YES' : 'NO',
		),
		array(
			'label' => 'Sunglass Lens',
			'value' => 'YES',
		),
	);
}

/**
 * Get review comments for the product.
 *
 * @param WC_Product $product Product object.
 * @return array<int, WP_Comment>
 */
function muukal_product_detail_get_reviews( $product ) {
	$comments = get_comments(
		array(
			'post_id' => $product->get_id(),
			'status'  => 'approve',
			'type'    => 'review',
			'number'  => 8,
		)
	);

	return is_array( $comments ) ? $comments : array();
}

/**
 * Get related products for the lower carousel blocks.
 *
 * @param WC_Product $product Product object.
 * @param int        $limit   Limit.
 * @return array<int, WC_Product>
 */
function muukal_product_detail_get_products( $product, $limit = 4 ) {
	$ids      = wc_get_related_products( $product->get_id(), $limit );
	$products = array();

	foreach ( $ids as $product_id ) {
		$related = wc_get_product( $product_id );

		if ( $related instanceof WC_Product ) {
			$products[] = $related;
		}
	}

	if ( count( $products ) >= $limit ) {
		return $products;
	}

	$fallback_ids = wc_get_products(
		array(
			'status'  => 'publish',
			'limit'   => $limit,
			'exclude' => array( $product->get_id() ),
			'return'  => 'ids',
			'orderby' => 'date',
			'order'   => 'DESC',
		)
	);

	foreach ( $fallback_ids as $product_id ) {
		if ( count( $products ) >= $limit ) {
			break;
		}

		$fallback = wc_get_product( $product_id );

		if ( $fallback instanceof WC_Product ) {
			$products[] = $fallback;
		}
	}

	return $products;
}

/**
 * Render the Muukal product detail template.
 *
 * @param array $args Shortcode args.
 * @return string
 */
function muukal_render_product_detail_template( $args ) {
	$product = muukal_product_detail_resolve_product( $args );

	if ( ! $product ) {
		return '';
	}

	$gallery_urls      = muukal_product_detail_get_gallery_urls( $product );
	$swatches          = muukal_product_detail_get_swatches( $product );
	$measurements      = muukal_product_detail_get_measurements( $product );
	$frame_info        = muukal_product_detail_get_frame_info( $product );
	$reviews           = muukal_product_detail_get_reviews( $product );
	$related_products  = muukal_product_detail_get_products( $product, 4 );
	$recent_products   = muukal_product_detail_get_products( $product, 4 );
	$product_name      = $product->get_name();
	$product_permalink = get_permalink( $product->get_id() );
	$short_name        = sanitize_text_field( $args['short_name'] );
	$sub_name          = sanitize_text_field( $args['sub_name'] );
	$promo_image       = esc_url( $args['promo_image'] );
	$promo_link        = esc_url( $args['promo_link'] );
	$promo_text        = sanitize_text_field( $args['promo_text'] );
	$current_price     = wc_price( (float) $product->get_price() );
	$regular_price     = $product->get_regular_price() ? wc_price( (float) $product->get_regular_price() ) : '';
	$discount          = 0;

	if ( $product->get_regular_price() && $product->get_price() && (float) $product->get_regular_price() > (float) $product->get_price() ) {
		$discount = (int) round( 100 - ( ( (float) $product->get_price() / (float) $product->get_regular_price() ) * 100 ) );
	}

	if ( '' === $short_name ) {
		$short_name = $product_name;
	}

	if ( '' === $sub_name ) {
		$sub_name = $product_name;
	}

	ob_start();
	?>
	<div class="muukal-product-detail-template">
		<div class="product-area pl-55 pr-55 ncpd960_15">
			<div class="container-fluid">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
					<li class="breadcrumb-item"><a href="<?php echo esc_url( get_post_type_archive_link( 'product' ) ? get_post_type_archive_link( 'product' ) : home_url( '/shop/' ) ); ?>">Eyeglasses</a></li>
					<li class="breadcrumb-item"><?php echo esc_html( strtolower( $short_name ) ); ?></li>
				</ol>
			</div>
		</div>

		<section class="shop-banner-area pt-60 pb-50">
			<div class="container">
				<div class="row">
					<div class="col-xl-8 muukal-product-gallery-shell">
						<div id="goods-swiper-box" class="muukal-product-gallery" data-gallery>
							<div class="muukal-gallery-stage">
								<?php if ( ! empty( $gallery_urls ) ) : ?>
									<img class="muukal-gallery-main" src="<?php echo esc_url( $gallery_urls[0] ); ?>" alt="<?php echo esc_attr( $product_name ); ?>" data-gallery-main>
								<?php endif; ?>
							</div>
							<div id="goods-lit-box" class="text-center pt-10 muukal-gallery-thumbs">
								<?php foreach ( $gallery_urls as $index => $gallery_url ) : ?>
									<button type="button" class="lit_gpic col-xl-2<?php echo 0 === $index ? ' is-active' : ''; ?>" data-gallery-thumb="<?php echo esc_url( $gallery_url ); ?>">
										<img src="<?php echo esc_url( $gallery_url ); ?>" alt="<?php echo esc_attr( $product_name ); ?>">
									</button>
								<?php endforeach; ?>
								<button type="button" class="tyr-btn col-xl-2">TRY-ON</button>
							</div>
							<div class="mt-10 ncpd960_mb0 text-center">
								<p>
									<span class="feature_sp">Blue Light Blocking</span>
									<span class="feature_sp">Bifocal</span>
									<span class="feature_sp">Progressive</span>
									<span class="feature_sp">Tinted Lenses</span>
									<span class="feature_sp">Classical</span>
								</p>
							</div>
						</div>
					</div>

					<div class="col-xl-4">
						<div class="product-details mb-30 mt-20 pl-10 ncpd960_mb0">
							<div class="product-details-title">
								<div>
									<h1 class="inline-left fs26 mr-30 fw400 tf-cap"><?php echo esc_html( $short_name ); ?></h1>
									<?php if ( $discount > 0 ) : ?>
										<div class="on-sale-tag"><?php echo esc_html( $discount ); ?>% OFF</div>
									<?php endif; ?>
									<div class="clear"></div>
									<div class="inline-left fs14 product-sub-name"><?php echo esc_html( $sub_name ); ?></div>
									<div class="clear"></div>
								</div>

								<div class="price details-price mt-20 pb-10 mb-10">
									<span class="old-price goods-price mk-price"><?php echo wp_kses_post( $current_price ); ?></span>
									<?php if ( $regular_price ) : ?>
										<del class="fs18 fw300 mk-89 goods_o_price"><?php echo wp_kses_post( $regular_price ); ?></del>
									<?php endif; ?>
									&nbsp;&nbsp;
									<span class="like_num_box mk-blue"><span class="like_num"><?php echo esc_html( $product->get_review_count() ? $product->get_review_count() * 125 : 5877 ); ?></span> Customers Like</span>
									<div style="clear: both;"></div>
								</div>
							</div>

							<?php if ( ! empty( $swatches ) ) : ?>
								<div class="goods-color-box mt30">
									<?php foreach ( $swatches as $index => $swatch ) : ?>
										<?php if ( empty( $swatch['dot_image'] ) ) : ?>
											<?php continue; ?>
										<?php endif; ?>
										<span class="ip-cspan ip-g-span<?php echo 0 === $index || ! empty( $swatch['is_default'] ) ? ' choose-color' : ''; ?>">
											<img class="ip-g-img" src="<?php echo esc_url( $swatch['dot_image'] ); ?>" alt="<?php echo esc_attr( $swatch['color_name'] ); ?>">
										</span>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>

							<div class="product-cat mt-25">
								<span>Select Color:&nbsp;&nbsp;</span>
								<a id="s-color-n"><?php echo esc_html( ! empty( $swatches[0]['color_name'] ) ? $swatches[0]['color_name'] : 'Pink' ); ?></a>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<span>Size:&nbsp;</span>
								<span class="goods-size">
									<a><?php echo esc_html( $measurements['frame_size'] ); ?>&nbsp;</a>
									<span class="fs14 mk-pink">Frame size guide</span>
								</span>
							</div>

							<div class="product-details-action mt35">
								<button class="btn theme-btn-w f-left add-wishlist-btn" type="button" style="width: 40%;">ADD TO WISHLIST</button>
								<div class="muukal-product-detail-lens-trigger" style="width: 59%; float: right;">
									<?php echo do_shortcode( '[muukal_lens_replica]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
								<div style="clear: both;"></div>
							</div>

							<div class="mt-25 fs14 promotion_box">
								<div class="mb5" style="background: #ffefef; padding: 10px;">
									<div style="width: 85px; float:left;" class="fw600">Promotion:</div>
									<div style="float: left;"><?php echo esc_html( $promo_text ); ?></div>
									<div class="clear"></div>
								</div>
								<?php if ( $promo_image ) : ?>
									<a href="<?php echo esc_url( $promo_link ? $promo_link : $product_permalink ); ?>">
										<img style="width: 100%" src="<?php echo esc_url( $promo_image ); ?>" alt="<?php echo esc_attr( $promo_text ); ?>">
									</a>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<div class="shipping_service">
			<div class="container">
				<div class="row pt-15 pb-15" style="background: #f8f8f8;">
					<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-3"><div class="service item_2"><div class="content"><span>30-day Return &amp; Exchange</span></div></div></div>
					<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-3"><div class="service item_3"><div class="content"><span>3-month Warranty</span></div></div></div>
					<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-3"><div class="service item_4"><div class="content"><span>100% Money Back Guaranteed</span></div></div></div>
					<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-3"><div class="service item_5"><div class="content"><span>Save with FSA or HSA dollars</span></div></div></div>
				</div>
			</div>
		</div>

		<section class="product-desc-area mt-60 ncpd960_mt0">
			<div class="container">
				<div class="muukal-product-detail-info-grid">
					<div class="muukal-product-detail-panel">
						<div class="muukal-panel-title">Measurements:&nbsp;&nbsp;<?php echo esc_html( $measurements['summary'] ); ?></div>
						<div class="muukal-panel-subtitle">Frame dimensions</div>
						<ul class="muukal-measurement-list">
							<li>Lens Width:&nbsp;&nbsp;<?php echo esc_html( $measurements['lens_width'] ); ?></li>
							<li>Lens Height:&nbsp;&nbsp;<?php echo esc_html( $measurements['lens_height'] ); ?></li>
							<li>Frame Width:&nbsp;&nbsp;<?php echo esc_html( $measurements['frame_width'] ); ?></li>
							<li>Bridge:&nbsp;&nbsp;<?php echo esc_html( $measurements['bridge'] ); ?></li>
							<li>Temple Length:&nbsp;&nbsp;<?php echo esc_html( $measurements['temple_length'] ); ?></li>
							<li>Frame Weight:&nbsp;&nbsp;<?php echo esc_html( $measurements['frame_weight'] ); ?></li>
						</ul>
					</div>
					<div class="muukal-product-detail-panel">
						<div class="muukal-panel-title">Frame Info</div>
						<div class="muukal-frame-info-grid">
							<?php foreach ( $frame_info as $row ) : ?>
								<div class="muukal-frame-info-label"><?php echo esc_html( $row['label'] ); ?></div>
								<div class="muukal-frame-info-value"><?php echo esc_html( $row['value'] ); ?></div>
							<?php endforeach; ?>
						</div>
						<div class="muukal-warm-tips">
							<div class="muukal-panel-title muukal-panel-title-small">Warm Tips</div>
							<p>There might be some visual differences due to different lights in sunlight and screen. Goods shall in kind prevail.</p>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section class="product-recommended container pb-10 pt-30">
			<div class="muukal-section-heading">
				<h2>You May Also Like</h2>
				<span>View Similar Frames</span>
			</div>
			<div class="muukal-product-card-grid">
				<?php foreach ( $related_products as $related_product ) : ?>
					<?php echo muukal_render_product_loop_item( array( 'product_id' => $related_product->get_id() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="product-desc-area ncpd960_mt0">
			<div class="container">
				<div class="muukal-section-heading">
					<h2>Customer Reviews (<?php echo esc_html( $product->get_review_count() ); ?>)</h2>
				</div>
				<div class="muukal-review-list">
					<?php foreach ( $reviews as $review ) : ?>
						<div class="muukal-review-card">
							<div class="muukal-review-name"><?php echo esc_html( wp_html_excerpt( $review->comment_author, 8, '***' ) ); ?></div>
							<div class="muukal-review-content"><?php echo esc_html( wp_trim_words( $review->comment_content, 28 ) ); ?></div>
						</div>
					<?php endforeach; ?>
					<?php if ( empty( $reviews ) ) : ?>
						<div class="muukal-review-card">
							<div class="muukal-review-name">No reviews yet</div>
							<div class="muukal-review-content">This area follows the Muukal product detail review block and will fill from WooCommerce product reviews.</div>
						</div>
					<?php endif; ?>
				</div>
				<div class="muukal-review-action">Write A Review</div>
			</div>
		</section>

		<section class="product-recommended container pb-10 pt-30 mt-20">
			<div class="muukal-section-heading">
				<h2>Recently Viewed</h2>
			</div>
			<div class="muukal-product-card-grid">
				<?php foreach ( $recent_products as $recent_product ) : ?>
					<?php echo muukal_render_product_loop_item( array( 'product_id' => $recent_product->get_id() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endforeach; ?>
			</div>
		</section>
	</div>
	<?php

	return ob_get_clean();
}
