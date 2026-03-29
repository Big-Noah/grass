<?php
/**
 * Muukal index nav box markup.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the primary internal product archive page URL.
 *
 * @return string
 */
function muukal_index_nav_box_get_archive_url() {
	static $archive_url = null;

	if ( null !== $archive_url ) {
		return $archive_url;
	}

	$archive_page = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'meta_query'     => array(
				array(
					'key'     => '_elementor_data',
					'value'   => 'muukal_product_filter_archive',
					'compare' => 'LIKE',
				),
			),
		)
	);

	if ( ! empty( $archive_page[0] ) && $archive_page[0] instanceof WP_Post ) {
		$page_link = get_permalink( $archive_page[0] );

		if ( $page_link ) {
			$archive_url = $page_link;
			return $archive_url;
		}
	}

	$archive_url = home_url( '/?page_id=172' );

	return $archive_url;
}

/**
 * Render the homepage index nav box.
 *
 * @param array $args Shortcode attributes.
 * @return string
 */
function muukal_render_index_nav_box( $args ) {
	$container_width = isset( $args['container_width'] ) ? sanitize_text_field( $args['container_width'] ) : '1900px';
	$padding_top     = isset( $args['padding_top'] ) ? sanitize_text_field( $args['padding_top'] ) : '55px';
	$use_local       = isset( $args['use_local_images'] ) ? filter_var( $args['use_local_images'], FILTER_VALIDATE_BOOLEAN ) : true;
	$archive_url     = muukal_index_nav_box_get_archive_url();

	$items = array(
		array(
			'image'      => 'bluelight_2.png',
			'remote'     => 'https://img.muukal.com/img/index/bluelight_2.png',
			'label'      => 'Blue Light Glasses',
			'query_args' => array(
				'feature' => 'block-blue-light',
			),
		),
		array(
			'image'      => 'floral.png',
			'remote'     => 'https://img.muukal.com/img/index/floral.png',
			'label'      => 'Floral Glasses',
			'query_args' => array(
				'color' => 'floral',
			),
		),
		array(
			'image'      => 'rxsunglasses_1.png',
			'remote'     => 'https://img.muukal.com/img/index/rxsunglasses_1.png',
			'label'      => 'Prescription Sunglasses',
			'query_args' => array(
				'feature' => 'sunglass-lens',
			),
		),
		array(
			'image'      => 'progressive_2.png',
			'remote'     => 'https://img.muukal.com/img/index/progressive_2.png',
			'label'      => 'Progressive',
			'query_args' => array(
				'feature' => 'progressive',
			),
		),
		array(
			'image'      => 'bycolor_2.png',
			'remote'     => 'https://img.muukal.com/img/index/bycolor_2.png',
			'label'      => 'Multicolor Glasses',
			'query_args' => array(
				'color' => 'multicolor',
			),
		),
		array(
			'image'      => 'sale_2.png',
			'remote'     => 'https://img.muukal.com/img/index/sale_2.png',
			'label'      => 'Flash Sale',
			'query_args' => array(
				'sort_by'   => 'price_low',
				'max_price' => '30',
			),
		),
	);

	ob_start();
	?>
	<div class="pt-55 index-nav-box muukal-index-nav-box" style="--muukal-index-nav-pt: <?php echo esc_attr( $padding_top ); ?>;">
		<div class="container-fluid pl-55 pr-55" style="max-width: <?php echo esc_attr( $container_width ); ?>;">
			<div class="row">
				<?php foreach ( $items as $item ) : ?>
					<?php
					$item_url = esc_url( add_query_arg( $item['query_args'], $archive_url ) );
					$img_url  = $use_local
						? esc_url( ASTRA_THEME_URI . 'assets/images/index-nav-box/' . $item['image'] )
						: esc_url( $item['remote'] );
					?>
					<a href="<?php echo $item_url; ?>" class="col-4 col-lg-2 pc_category_item">
						<img src="<?php echo $img_url; ?>" alt="<?php echo esc_attr( $item['label'] ); ?>" loading="lazy" decoding="async">
						<span><?php echo esc_html( $item['label'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php

	return ob_get_clean();
}

