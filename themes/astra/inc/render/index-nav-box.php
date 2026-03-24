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
 * Render the homepage index nav box.
 *
 * @param array $args Shortcode attributes.
 * @return string
 */
function muukal_render_index_nav_box( $args ) {
	$link_base       = isset( $args['link_base'] ) ? untrailingslashit( esc_url_raw( $args['link_base'] ) ) : 'https://muukal.com';
	$container_width = isset( $args['container_width'] ) ? sanitize_text_field( $args['container_width'] ) : '1900px';
	$padding_top     = isset( $args['padding_top'] ) ? sanitize_text_field( $args['padding_top'] ) : '55px';
	$use_local       = isset( $args['use_local_images'] ) ? filter_var( $args['use_local_images'], FILTER_VALIDATE_BOOLEAN ) : true;

	$items = array(
		array(
			'href'  => '/lenses/block-blue-light.html',
			'image' => 'bluelight_2.png',
			'remote'=> 'https://img.muukal.com/img/index/bluelight_2.png',
			'label' => 'Blue Light Glasses',
		),
		array(
			'href'  => '/eyeglasses/index/set/floral/color/1.html',
			'image' => 'floral.png',
			'remote'=> 'https://img.muukal.com/img/index/floral.png',
			'label' => 'Floral Glasses',
		),
		array(
			'href'  => '/eyeglasses/index/set/sunglasses.html',
			'image' => 'rxsunglasses_1.png',
			'remote'=> 'https://img.muukal.com/img/index/rxsunglasses_1.png',
			'label' => 'Prescription Sunglasses',
		),
		array(
			'href'  => '/lenses/progressive.html',
			'image' => 'progressive_2.png',
			'remote'=> 'https://img.muukal.com/img/index/progressive_2.png',
			'label' => 'Progressive',
		),
		array(
			'href'  => '/eyeglasses/index/set/rainbow/color/22.html',
			'image' => 'bycolor_2.png',
			'remote'=> 'https://img.muukal.com/img/index/bycolor_2.png',
			'label' => 'Multicolor Glasses',
		),
		array(
			'href'  => '/eyeglasses/index/set/flashsale.html',
			'image' => 'sale_2.png',
			'remote'=> 'https://img.muukal.com/img/index/sale_2.png',
			'label' => 'Flash Sale',
		),
	);

	ob_start();
	?>
	<div class="pt-55 index-nav-box muukal-index-nav-box" style="--muukal-index-nav-pt: <?php echo esc_attr( $padding_top ); ?>;">
		<div class="container-fluid pl-55 pr-55" style="max-width: <?php echo esc_attr( $container_width ); ?>;">
			<div class="row">
				<?php foreach ( $items as $item ) : ?>
					<?php
					$item_url = esc_url( $link_base . $item['href'] );
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

