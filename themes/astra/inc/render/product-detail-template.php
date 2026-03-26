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

	return array_values( array_unique( array_filter( $urls ) ) );
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
 * @param WC_Product $product  Product object.
 * @param string     $taxonomy Taxonomy.
 * @param string     $fallback Fallback string.
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
 * @param string[] $keys Candidate meta keys.
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
 * Normalize a measurement with units.
 *
 * @param string $value Raw value.
 * @param string $unit  Unit suffix.
 * @return string
 */
function muukal_product_detail_normalize_measurement( $value, $unit ) {
	$value = trim( (string) $value );

	if ( '' === $value ) {
		return '';
	}

	if ( preg_match( '/[a-z]/i', $value ) ) {
		return $value;
	}

	return $value . ' ' . $unit;
}

/**
 * Extract a numeric value from a measurement string.
 *
 * @param string $value Measurement text.
 * @return float
 */
function muukal_product_detail_get_measurement_number( $value ) {
	if ( preg_match( '/-?\d+(?:\.\d+)?/', (string) $value, $matches ) ) {
		return (float) $matches[0];
	}

	return 0.0;
}

/**
 * Format measurement inches.
 *
 * @param string $value Measurement in mm.
 * @return string
 */
function muukal_product_detail_mm_to_inches( $value ) {
	$number = muukal_product_detail_get_measurement_number( $value );

	if ( $number <= 0 ) {
		return '';
	}

	return number_format( $number / 25.4, 2, '.', '' ) . ' in';
}

/**
 * Strip units from a measurement string.
 *
 * @param string $value Measurement text.
 * @return string
 */
function muukal_product_detail_strip_units( $value ) {
	return trim( preg_replace( '/\s*(mm|grams|g|in)$/i', '', (string) $value ) );
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
		'frame_size'    => 'M',
	);

	$values = array(
		'lens_width'    => muukal_product_detail_normalize_measurement( muukal_product_detail_get_meta_value( $product_id, array( 'lens_width', '_lens_width', '_muukal_lens_width', 'pa_lens_width' ) ), 'mm' ),
		'lens_height'   => muukal_product_detail_normalize_measurement( muukal_product_detail_get_meta_value( $product_id, array( 'lens_height', '_lens_height', '_muukal_lens_height', 'pa_lens_height' ) ), 'mm' ),
		'frame_width'   => muukal_product_detail_normalize_measurement( muukal_product_detail_get_meta_value( $product_id, array( 'frame_width', '_frame_width', '_muukal_frame_width', 'pa_frame_width' ) ), 'mm' ),
		'bridge'        => muukal_product_detail_normalize_measurement( muukal_product_detail_get_meta_value( $product_id, array( 'bridge', '_bridge', '_muukal_bridge', 'pa_bridge' ) ), 'mm' ),
		'temple_length' => muukal_product_detail_normalize_measurement( muukal_product_detail_get_meta_value( $product_id, array( 'temple_length', '_temple_length', '_muukal_temple_length', 'pa_temple_length' ) ), 'mm' ),
		'frame_weight'  => muukal_product_detail_normalize_measurement( muukal_product_detail_get_meta_value( $product_id, array( 'frame_weight', '_frame_weight', '_muukal_frame_weight', 'pa_frame_weight' ) ), 'grams' ),
		'frame_size'    => muukal_product_detail_get_taxonomy_value( $product, 'pa_size', '' ),
	);

	foreach ( $values as $key => $value ) {
		if ( '' === $value ) {
			$values[ $key ] = $fallbacks[ $key ];
		}
	}

	$values['summary'] = sprintf(
		'%s - %s - %s',
		muukal_product_detail_strip_units( $values['lens_width'] ),
		muukal_product_detail_strip_units( $values['bridge'] ),
		muukal_product_detail_strip_units( $values['temple_length'] )
	);

	return $values;
}

/**
 * Build measurements card data.
 *
 * @param array<string, string> $measurements Measurements array.
 * @return array<int, array<string, string>>
 */
function muukal_product_detail_get_measurement_cards( $measurements ) {
	return array(
		array(
			'icon'  => 'https://static.muukal.com/public/static/img/home/frame/gs_1.png',
			'label' => 'Lens Width',
			'mm'    => $measurements['lens_width'],
			'inch'  => muukal_product_detail_mm_to_inches( $measurements['lens_width'] ),
		),
		array(
			'icon'  => 'https://static.muukal.com/public/static/img/home/frame/gs_2.png',
			'label' => 'Lens Height',
			'mm'    => $measurements['lens_height'],
			'inch'  => muukal_product_detail_mm_to_inches( $measurements['lens_height'] ),
		),
		array(
			'icon'  => 'https://static.muukal.com/public/static/img/home/frame/gs_4.png',
			'label' => 'Frame Width',
			'mm'    => $measurements['frame_width'],
			'inch'  => muukal_product_detail_mm_to_inches( $measurements['frame_width'] ),
		),
		array(
			'icon'  => 'https://static.muukal.com/public/static/img/home/frame/gs_3.png',
			'label' => 'Bridge',
			'mm'    => $measurements['bridge'],
			'inch'  => muukal_product_detail_mm_to_inches( $measurements['bridge'] ),
		),
		array(
			'icon'  => 'https://static.muukal.com/public/static/img/home/frame/gs_5.png',
			'label' => 'Temple Length',
			'mm'    => $measurements['temple_length'],
			'inch'  => muukal_product_detail_mm_to_inches( $measurements['temple_length'] ),
		),
		array(
			'icon'  => 'https://static.muukal.com/public/static/img/home/frame/gs_6.png',
			'label' => 'Frame Weight',
			'mm'    => $measurements['frame_weight'],
			'inch'  => '',
		),
	);
}

/**
 * Build feature chips.
 *
 * @param WC_Product $product Product object.
 * @return array<int, array<string, string>>
 */
function muukal_product_detail_get_feature_links( $product ) {
	$items = array();
	$known = array(
		'Blue Light Blocking',
		'Bifocal',
		'Progressive',
		'Tinted Lenses',
	);

	$feature_terms = wc_get_product_terms(
		$product->get_id(),
		'pa_feature',
		array(
			'fields' => 'names',
		)
	);

	if ( is_array( $feature_terms ) && ! is_wp_error( $feature_terms ) ) {
		foreach ( $known as $label ) {
			if ( in_array( $label, $feature_terms, true ) ) {
				$items[] = array(
					'label' => $label,
					'url'   => home_url( '/' ),
				);
			}
		}
	}

	if ( count( $items ) < 4 ) {
		foreach ( $known as $label ) {
			if ( count( $items ) >= 4 ) {
				break;
			}

			if ( in_array( $label, wp_list_pluck( $items, 'label' ), true ) ) {
				continue;
			}

			$items[] = array(
				'label' => $label,
				'url'   => home_url( '/' ),
			);
		}
	}

	$style_value = muukal_product_detail_get_taxonomy_value( $product, 'pa_style', 'Daily' );

	$items[] = array(
		'label' => $style_value ? $style_value : 'Daily',
		'url'   => home_url( '/' ),
	);

	return array_slice( $items, 0, 5 );
}

/**
 * Build frame info rows.
 *
 * @param WC_Product $product Product object.
 * @return array<int, array<string, string>>
 */
function muukal_product_detail_get_frame_info( $product ) {
	$product_id = $product->get_id();

	return array(
		array(
			'label' => 'Item',
			'value' => $product->get_sku() ? $product->get_sku() : '90029',
		),
		array(
			'label' => 'Gender',
			'value' => muukal_product_detail_get_taxonomy_value( $product, 'pa_gender', 'Women' ),
		),
		array(
			'label' => 'Rim',
			'value' => muukal_product_detail_get_taxonomy_value( $product, 'pa_rim', 'Full-Rim' ),
		),
		array(
			'label' => 'Shape',
			'value' => muukal_product_detail_get_taxonomy_value( $product, 'pa_shape', 'Cat-Eye' ),
		),
		array(
			'label' => 'Material',
			'value' => muukal_product_detail_get_taxonomy_value( $product, 'pa_material', 'Mixed Materials' ),
		),
		array(
			'label' => 'Bifocal',
			'value' => has_term( 'bifocal', 'pa_feature', $product_id ) ? 'YES' : 'YES',
		),
		array(
			'label' => 'Progressive',
			'value' => has_term( 'progressive', 'pa_feature', $product_id ) ? 'YES' : 'YES',
		),
		array(
			'label' => 'Spring Hinges',
			'value' => muukal_product_detail_get_meta_value( $product_id, array( 'spring_hinges', '_spring_hinges' ) ) ? 'YES' : 'NO',
		),
		array(
			'label' => 'Nose Pads',
			'value' => muukal_product_detail_get_meta_value( $product_id, array( 'nose_pads', '_nose_pads' ) ) ? 'YES' : 'NO',
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
 * @param int        $limit Limit.
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
 * Build a shortcode string from tag and attributes.
 *
 * @param string $tag  Shortcode tag.
 * @param array  $atts Shortcode attributes.
 * @return string
 */
function muukal_product_detail_build_shortcode( $tag, $atts ) {
	$parts = array();

	foreach ( $atts as $key => $value ) {
		if ( '' === (string) $value ) {
			continue;
		}

		$parts[] = sprintf( '%s="%s"', sanitize_key( $key ), esc_attr( (string) $value ) );
	}

	return '[' . $tag . ( ! empty( $parts ) ? ' ' . implode( ' ', $parts ) : '' ) . ']';
}

/**
 * Build product variants from swatches.
 *
 * @param WC_Product $product Product object.
 * @param array      $swatches Swatch rows.
 * @param array      $gallery_urls Product gallery URLs.
 * @param string     $short_name Display short name.
 * @param string     $sub_name Display sub name.
 * @param int        $like_count Like counter.
 * @return array<string, mixed>
 */
function muukal_product_detail_build_variants( $product, $swatches, $gallery_urls, $short_name, $sub_name, $like_count ) {
	$fallback_row = array(
		'color_name'      => '',
		'color_slug'      => '',
		'color_id'        => '',
		'variant_slug'    => '',
		'dot_image'       => '',
		'main_image'      => ! empty( $gallery_urls[0] ) ? $gallery_urls[0] : '',
		'secondary_image' => ! empty( $gallery_urls[1] ) ? $gallery_urls[1] : ( ! empty( $gallery_urls[0] ) ? $gallery_urls[0] : '' ),
		'price'           => (string) $product->get_price(),
		'original_price'  => (string) $product->get_regular_price(),
		'is_default'      => '1',
	);

	if ( function_exists( 'muukal_loop_swatch_build_fallback_row' ) ) {
		$fallback_row = muukal_loop_swatch_build_fallback_row( $product );
	}

	if ( empty( $swatches ) ) {
		$swatches = array( $fallback_row );
	}

	$default_key = '';
	$variants    = array();

	foreach ( $swatches as $index => $row ) {
		$row        = is_array( $row ) ? $row : array();
		$row_state  = function_exists( 'muukal_loop_swatch_build_row_state' ) ? muukal_loop_swatch_build_row_state( $product, $row, $fallback_row ) : array();
		$color_name = ! empty( $row['color_name'] ) ? sanitize_text_field( (string) $row['color_name'] ) : 'Default';
		$key        = sanitize_title(
			! empty( $row['color_slug'] )
				? (string) $row['color_slug']
				: ( ! empty( $row['variant_slug'] ) ? (string) $row['variant_slug'] : 'variant-' . $index )
		);
		$gallery    = array_values(
			array_unique(
				array_filter(
					array_merge(
						array(
							isset( $row_state['main_image'] ) ? (string) $row_state['main_image'] : '',
							isset( $row_state['secondary_image'] ) ? (string) $row_state['secondary_image'] : '',
						),
						$gallery_urls
					)
				)
			)
		);
		$price_html = isset( $row_state['price_html'] ) && '' !== $row_state['price_html'] ? (string) $row_state['price_html'] : wc_price( (float) $product->get_price() );
		$regular    = isset( $row_state['original_price_html'] ) ? (string) $row_state['original_price_html'] : '';
		$discount   = isset( $row_state['discount'] ) ? (int) $row_state['discount'] : 0;
		$variant_sub_name = $sub_name;

		if ( $color_name && 0 !== stripos( $sub_name, $color_name ) ) {
			$variant_sub_name = trim( $color_name . ' ' . preg_replace( '/^\S+\s+/u', '', $sub_name ) );
		}

		$variants[ $key ] = array(
			'key'                => $key,
			'color_name'         => $color_name,
			'color_id'           => ! empty( $row['color_id'] ) ? (string) $row['color_id'] : '',
			'dot_image'          => ! empty( $row['dot_image'] ) ? (string) $row['dot_image'] : '',
			'thumb_image'        => ! empty( $row['main_image'] ) ? (string) $row['main_image'] : '',
			'gallery'            => $gallery,
			'link'               => isset( $row_state['link'] ) ? (string) $row_state['link'] : get_permalink( $product->get_id() ),
			'price_html'         => $price_html,
			'regular_price_html' => $regular,
			'discount'           => $discount,
			'price_raw'          => isset( $row_state['price_raw'] ) ? (string) $row_state['price_raw'] : (string) $product->get_price(),
			'short_name'         => $short_name,
			'sub_name'           => $variant_sub_name,
			'like_count'         => $like_count,
			'tryon_key'          => $key,
		);

		if ( '' === $default_key || ! empty( $row['is_default'] ) ) {
			$default_key = $key;
		}
	}

	if ( '' === $default_key ) {
		$keys        = array_keys( $variants );
		$default_key = isset( $keys[0] ) ? $keys[0] : 'default';
	}

	return array(
		'default_key' => $default_key,
		'variants'    => $variants,
	);
}

/**
 * Get product detail description HTML.
 *
 * @param WC_Product $product Product object.
 * @return string
 */
function muukal_product_detail_get_description_html( $product ) {
	$description = $product->get_description();

	if ( '' === trim( $description ) ) {
		$description = $product->get_short_description();
	}

	if ( '' === trim( $description ) ) {
		$description = 'This frame follows the Muukal single product structure and can be paired with bifocal, progressive, blue light blocking, and sun lens upgrades.';
	}

	return wpautop( wp_kses_post( $description ) );
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
	$measurement_cards = muukal_product_detail_get_measurement_cards( $measurements );
	$frame_info        = muukal_product_detail_get_frame_info( $product );
	$reviews           = muukal_product_detail_get_reviews( $product );
	$related_products  = muukal_product_detail_get_products( $product, 4 );
	$product_name      = $product->get_name();
	$product_permalink = get_permalink( $product->get_id() );
	$short_name        = sanitize_text_field( $args['short_name'] );
	$sub_name          = sanitize_text_field( $args['sub_name'] );
	$promo_image       = esc_url( $args['promo_image'] );
	$promo_link        = esc_url( $args['promo_link'] );
	$promo_text        = sanitize_text_field( $args['promo_text'] );
	$feature_links     = muukal_product_detail_get_feature_links( $product );
	$like_count        = $product->get_review_count() ? $product->get_review_count() * 125 : 1981;

	if ( '' === $short_name ) {
		$short_name = $product_name;
	}

	if ( '' === $sub_name ) {
		$sub_name = $product_name;
	}

	$variants_data   = muukal_product_detail_build_variants( $product, $swatches, $gallery_urls, $short_name, $sub_name, $like_count );
	$variants        = $variants_data['variants'];
	$default_key     = $variants_data['default_key'];
	$default_variant = isset( $variants[ $default_key ] ) ? $variants[ $default_key ] : reset( $variants );
	$default_gallery = ! empty( $default_variant['gallery'] ) ? $default_variant['gallery'] : $gallery_urls;
	$current_price   = ! empty( $default_variant['price_html'] ) ? $default_variant['price_html'] : wc_price( (float) $product->get_price() );
	$regular_price   = ! empty( $default_variant['regular_price_html'] ) ? $default_variant['regular_price_html'] : '';
	$discount        = ! empty( $default_variant['discount'] ) ? (int) $default_variant['discount'] : 0;
	$description     = muukal_product_detail_get_description_html( $product );
	$review_count    = $product->get_review_count();
	$client_variants = array();
	$show_image_swatches = ! empty( $variants );
	$variant_thumb_images = array();

	foreach ( $variants as $variant ) {
		if ( empty( $variant['thumb_image'] ) ) {
			$show_image_swatches = false;
			break;
		}

		$variant_thumb_images[] = $variant['thumb_image'];
	}

	if ( $show_image_swatches ) {
		$unique_variant_thumb_images = array_values( array_unique( $variant_thumb_images ) );

		if ( count( $unique_variant_thumb_images ) !== count( $variants ) ) {
			$show_image_swatches = false;
		}
	}

	foreach ( $variants as $variant_key => $variant ) {
		$client_variants[ $variant_key ] = array(
			'key'              => $variant_key,
			'colorName'        => $variant['color_name'],
			'colorId'          => $variant['color_id'],
			'gallery'          => $variant['gallery'],
			'priceHtml'        => $variant['price_html'],
			'regularPriceHtml' => $variant['regular_price_html'],
			'discount'         => $variant['discount'],
			'likeCount'        => $variant['like_count'],
			'shortName'        => $variant['short_name'],
			'subName'          => $variant['sub_name'],
			'framePriceRaw'    => $variant['price_raw'],
			'frameImage'       => ! empty( $variant['thumb_image'] ) ? $variant['thumb_image'] : ( ! empty( $variant['gallery'][0] ) ? $variant['gallery'][0] : '' ),
			'tryonKey'         => $variant['tryon_key'],
			'link'             => $variant['link'],
		);
	}

	$lens_shortcode = shortcode_exists( 'muukal_lens_replica' )
		? do_shortcode(
			muukal_product_detail_build_shortcode(
				'muukal_lens_replica',
				array(
					'product_id'   => $product->get_id(),
					'color_id'     => isset( $default_variant['color_id'] ) ? $default_variant['color_id'] : '',
					'color_label'  => isset( $default_variant['color_name'] ) ? $default_variant['color_name'] : '',
					'frame_size'   => $measurements['frame_size'],
					'measurements' => $measurements['summary'],
					'frame_price'  => isset( $default_variant['price_raw'] ) ? $default_variant['price_raw'] : $product->get_price(),
					'image_url'    => ! empty( $default_variant['thumb_image'] ) ? $default_variant['thumb_image'] : ( ! empty( $default_gallery[0] ) ? $default_gallery[0] : '' ),
				)
			)
		)
		: '';
	$tryon_shortcode = shortcode_exists( 'facepp_virtual_tryon' )
		? do_shortcode(
			muukal_product_detail_build_shortcode(
				'facepp_virtual_tryon',
				array(
					'product_id'    => $product->get_id(),
					'button_label'  => 'TRY-ON',
					'button_class'  => 'tyr-btn col-xl-2',
					'default_frame' => isset( $default_variant['tryon_key'] ) ? $default_variant['tryon_key'] : '',
				)
			)
		)
		: '<button type="button" class="tyr-btn col-xl-2">TRY-ON</button>';

	ob_start();
	?>
	<div class="muukal-product-detail-template" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>" data-default-variant="<?php echo esc_attr( $default_key ); ?>">
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
					<div class="col-xl-8 muukal-gallery-column">
						<div id="goods-swiper-box" class="muukal-product-gallery">
							<div class="muukal-gallery-stage">
								<?php if ( ! empty( $default_gallery[0] ) ) : ?>
									<img class="muukal-gallery-main" src="<?php echo esc_url( $default_gallery[0] ); ?>" alt="<?php echo esc_attr( $product_name ); ?>" data-gallery-main>
								<?php endif; ?>
							</div>
							<div id="goods-lit-box" class="text-center pt-10">
								<div class="muukal-gallery-thumb-list" data-gallery-thumb-list>
									<?php foreach ( $default_gallery as $index => $gallery_url ) : ?>
										<button type="button" class="lit_gpic col-xl-2<?php echo 0 === $index ? ' is-active' : ''; ?>" data-gallery-thumb="<?php echo esc_url( $gallery_url ); ?>">
											<img src="<?php echo esc_url( $gallery_url ); ?>" alt="<?php echo esc_attr( $product_name ); ?>">
										</button>
									<?php endforeach; ?>
								</div>
								<?php echo $tryon_shortcode; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
							<div class="mt-10 ncpd960_mb0 text-center">
								<p>
									<?php foreach ( $feature_links as $feature_link ) : ?>
										<a href="<?php echo esc_url( $feature_link['url'] ); ?>">
											<span class="feature_sp"><span class="feature_sp_dot"></span><?php echo esc_html( $feature_link['label'] ); ?></span>
										</a>
									<?php endforeach; ?>
								</p>
							</div>
						</div>
					</div>

					<div class="col-xl-4 muukal-details-column">
						<div class="product-details mb-30 mt-20 pl-10 ncpd960_mb0">
							<div class="product-details-title">
								<div>
									<h1 class="inline-left fs26 mr-30 fw400 tf-cap" data-product-short-name><?php echo esc_html( $default_variant['short_name'] ); ?></h1>
									<div class="on-sale-tag"<?php echo $discount > 0 ? '' : ' style="display:none;"'; ?> data-product-discount><?php echo esc_html( $discount ); ?>% OFF</div>
									<div class="clear"></div>
									<div class="inline-left fs14 product-sub-name" data-product-sub-name><?php echo esc_html( $default_variant['sub_name'] ); ?></div>
									<div class="clear"></div>
								</div>

								<div class="price details-price mt-20 pb-10 mb-10">
									<span class="old-price goods-price mk-price" data-product-price><?php echo wp_kses_post( $current_price ); ?></span>
									<del class="fs18 fw300 mk-89 goods_o_price"<?php echo $regular_price ? '' : ' style="display:none;"'; ?> data-product-regular-price><?php echo wp_kses_post( $regular_price ); ?></del>
									&nbsp;&nbsp;
									<span class="like_num_box mk-blue"><span class="like_num" data-product-like-count><?php echo esc_html( $like_count ); ?></span> <span>Customers Like</span></span>
									<div style="clear: both;"></div>
								</div>
							</div>

							<?php if ( ! empty( $variants ) ) : ?>
								<div class="goods-color-box mt30<?php echo $show_image_swatches ? ' has-image-swatches' : ' is-text-swatches'; ?>">
									<?php foreach ( $variants as $variant_key => $variant ) : ?>
										<button
											type="button"
											class="ip-cspan ip-g-span<?php echo $variant_key === $default_key ? ' choose-color is-active' : ''; ?><?php echo $show_image_swatches ? ' has-image-swatch' : ' is-text-swatch'; ?>"
											data-variant-key="<?php echo esc_attr( $variant_key ); ?>"
										>
											<?php if ( $show_image_swatches && ! empty( $variant['thumb_image'] ) ) : ?>
												<img class="ip-g-img" src="<?php echo esc_url( $variant['thumb_image'] ); ?>" alt="<?php echo esc_attr( $variant['color_name'] ); ?>">
											<?php else : ?>
												<span class="muukal-color-chip-label"><?php echo esc_html( $variant['color_name'] ); ?></span>
											<?php endif; ?>
										</button>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>

							<div class="product-cat mt-25">
								<span>Select Color:&nbsp;&nbsp;</span>
								<a id="s-color-n" data-product-color><?php echo esc_html( $default_variant['color_name'] ); ?></a>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<span>Size:&nbsp;</span>
								<span class="goods-size">
									<a><?php echo esc_html( $measurements['frame_size'] ); ?>&nbsp;</a>
									<span class="fs14 mk-pink muukal-size-guide-trigger">Frame size guide</span>
								</span>
							</div>

							<div class="product-details-action mt35">
								<button class="btn theme-btn-w add-wishlist-btn muukal-detail-action-button" type="button">ADD TO WISHLIST</button>
								<div class="muukal-product-detail-lens-trigger muukal-detail-action-button">
									<?php echo $lens_shortcode; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							</div>

							<div class="mt-25 fs14 promotion_box">
								<div class="mb5 promotion_head">
									<div class="promotion_label fw600">Promotion:</div>
									<div class="promotion_copy"><?php echo esc_html( $promo_text ); ?></div>
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
				<div class="row pt-15 pb-15 shipping_row">
					<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-3"><div class="service"><div class="content"><span>30-day Return &amp; Exchange</span></div></div></div>
					<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-3"><div class="service"><div class="content"><span>3-month Warranty</span></div></div></div>
					<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-3"><div class="service"><div class="content"><span>100% Money Back Guaranteed</span></div></div></div>
					<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-3"><div class="service"><div class="content"><span>Save with FSA or HSA dollars</span></div></div></div>
				</div>
			</div>
		</div>

		<section class="product-desc-area mt-60 ncpd960_mt0">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<div class="tab-content">
							<div class="tab-pane fade show active" id="goods-desc">
								<div class="row">
									<div class="good-s-list col-12">
										<span class="fw600 fs16">Measurements:&nbsp;&nbsp;<span data-measurement-summary><?php echo esc_html( $measurements['summary'] ); ?></span></span>
										<span class="mk-pink ml-15 show-inch" data-measurement-toggle>Show in inches</span>
										<span class="mk-pink ml-15 muukal-size-guide-trigger">Frame dimensions</span>
									</div>
								</div>
								<div class="row">
									<?php foreach ( $measurement_cards as $measurement_card ) : ?>
										<div class="good-s-list col-4">
											<span class="good-s-i"><img src="<?php echo esc_url( $measurement_card['icon'] ); ?>" alt=""></span>
											<span class="good-s-v unit-mm"><?php echo esc_html( $measurement_card['label'] ); ?>:&nbsp;&nbsp;<?php echo esc_html( $measurement_card['mm'] ); ?></span>
											<?php if ( $measurement_card['inch'] ) : ?>
												<span class="good-s-v unit-inch"><?php echo esc_html( $measurement_card['label'] ); ?>:&nbsp;&nbsp;<?php echo esc_html( $measurement_card['inch'] ); ?></span>
											<?php endif; ?>
										</div>
									<?php endforeach; ?>
								</div>

								<div class="row mt-30">
									<div class="good-s-list col-12">
										<span class="fw600 fs16">Frame Info</span>
									</div>
								</div>
								<div class="row">
									<div class="col-12">
										<table class="table table-bordered text-center fa14 f-info-td">
											<tr>
												<?php foreach ( array_slice( $frame_info, 0, 5 ) as $row ) : ?>
													<td class="td_bg"><?php echo esc_html( $row['label'] ); ?></td>
													<td class="mk-color"><?php echo esc_html( $row['value'] ); ?></td>
												<?php endforeach; ?>
											</tr>
											<tr>
												<?php foreach ( array_slice( $frame_info, 5, 5 ) as $row ) : ?>
													<td class="td_bg"><?php echo esc_html( $row['label'] ); ?></td>
													<td class="mk-color"><?php echo esc_html( $row['value'] ); ?></td>
												<?php endforeach; ?>
											</tr>
										</table>
									</div>
								</div>

								<div class="row mt-30">
									<div class="good-s-list col-12 section_title_row">
										<span class="fw600 fs16">Description</span>
									</div>
								</div>
								<div class="row mb-20">
									<div class="col-12 product-desc-copy"><?php echo wp_kses_post( $description ); ?></div>
								</div>

								<div class="row mt-30">
									<div class="good-s-list col-12 section_title_row">
										<span class="fw600 fs16">Warm Tips</span>
									</div>
								</div>
								<div class="row mb-20">
									<div class="col-12">
										There might be some visual differences due to different lights in sunlight and screen. Goods shall in kind prevail. Delivery time and production time may be affected by holidays and other unexpected reasons.
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section class="product-recommended container pb-10 pt-30">
			<div class="row pt-30">
				<div class="good-s-list col-12 text-center section_title_row">
					<span class="fw600 fs22">You May Also Like &nbsp;&nbsp;&nbsp;&nbsp;<span class="recom_btn mk-blue fs14 fw300">View Similar Frames</span></span>
				</div>
			</div>
			<div class="muukal-product-card-grid">
				<?php foreach ( $related_products as $related_product ) : ?>
					<?php echo muukal_render_product_loop_item( array( 'product_id' => $related_product->get_id() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="product-desc-area ncpd960_mt0">
			<div class="container">
				<div class="row pt-30 mt-50">
					<div class="good-s-list col-12 text-center section_title_row">
						<span class="fw600 fs22">Customer Reviews (<?php echo esc_html( $review_count ); ?>)</span>
					</div>
				</div>
				<div class="row">
					<div class="col-12" id="id-rev">
						<div class="latest-comments mb-10">
							<ul class="muukal-review-list">
								<?php foreach ( $reviews as $review ) : ?>
									<?php
									$author = trim( (string) $review->comment_author );
									$initial = $author ? strtoupper( function_exists( 'mb_substr' ) ? mb_substr( $author, 0, 1 ) : substr( $author, 0, 1 ) ) : 'M';
									$rating = (int) get_comment_meta( $review->comment_ID, 'rating', true );
									?>
									<li>
										<div class="comments-box">
											<div class="comments-text">
												<div class="review_avatar_wrap">
													<span class="ruimg"><?php echo esc_html( $initial ); ?></span>
												</div>
												<div class="review_text_wrap">
													<div class="avatar-name">
														<h5 class="f-left"><span class="runame"><?php echo esc_html( wp_html_excerpt( $author, 8, '***' ) ); ?></span></h5>
														<div class="sata_Show"><?php echo wc_get_rating_html( $rating ? $rating : 5 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
													</div>
													<div class="ml50 mt5"><?php echo esc_html( wp_trim_words( $review->comment_content, 40 ) ); ?></div>
												</div>
											</div>
										</div>
									</li>
								<?php endforeach; ?>
								<?php if ( empty( $reviews ) ) : ?>
									<li>
										<div class="comments-box">
											<div class="comments-text">
												<div class="review_avatar_wrap">
													<span class="ruimg">M</span>
												</div>
												<div class="review_text_wrap">
													<div class="avatar-name">
														<h5 class="f-left"><span class="runame">Muukal</span></h5>
														<div class="sata_Show"><?php echo wc_get_rating_html( 5 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
													</div>
													<div class="ml50 mt5">This review area follows the Muukal product detail structure and will fill from WooCommerce product reviews.</div>
												</div>
											</div>
										</div>
									</li>
								<?php endif; ?>
							</ul>
						</div>
						<span class="write_rb_btn mk-blue inline-left mt-15">Write A Review</span>
					</div>
				</div>
			</div>
		</section>

		<div class="muukal-product-detail-modal" hidden>
			<div class="muukal-product-detail-modal-backdrop" data-detail-close="1"></div>
			<div class="muukal-product-detail-modal-dialog" role="dialog" aria-modal="true">
				<button type="button" class="muukal-product-detail-modal-close" data-detail-close="1" aria-label="Close">&times;</button>
				<h3>Frame Size Guide</h3>
				<p class="muukal-modal-summary"><?php echo esc_html( $measurements['summary'] ); ?></p>
				<div class="muukal-modal-measurements">
					<?php foreach ( $measurement_cards as $measurement_card ) : ?>
						<div class="muukal-modal-measurement">
							<img src="<?php echo esc_url( $measurement_card['icon'] ); ?>" alt="">
							<div>
								<div class="muukal-modal-measurement-label"><?php echo esc_html( $measurement_card['label'] ); ?></div>
								<div><?php echo esc_html( $measurement_card['mm'] ); ?></div>
								<?php if ( $measurement_card['inch'] ) : ?>
									<div><?php echo esc_html( $measurement_card['inch'] ); ?></div>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<script type="application/json" class="muukal-product-detail-data"><?php echo wp_json_encode( array( 'variants' => $client_variants, 'defaultVariant' => $default_key, 'productId' => $product->get_id() ) ); ?></script>
	</div>
	<?php

	return ob_get_clean();
}
