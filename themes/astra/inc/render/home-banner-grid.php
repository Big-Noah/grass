<?php
/**
 * Muukal home banner grid markup.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get default banner items for the home banner grid.
 *
 * @return array<int, array<string, string>>
 */
function muukal_home_banner_grid_default_items() {
	return array(
		array(
			'link'  => home_url( '/' ),
			'image' => ASTRA_THEME_URI . 'assets/images/home-banner-grid/banner-1.svg',
			'alt'   => 'New in eyewear banner',
		),
		array(
			'link'  => home_url( '/' ),
			'image' => ASTRA_THEME_URI . 'assets/images/home-banner-grid/banner-2.svg',
			'alt'   => 'Progressive lens banner',
		),
		array(
			'link'  => home_url( '/' ),
			'image' => ASTRA_THEME_URI . 'assets/images/home-banner-grid/banner-3.svg',
			'alt'   => 'Trending frame styles banner',
		),
	);
}

/**
 * Build a banner item from shortcode arguments.
 *
 * @param array<string, mixed>  $args    Shortcode arguments.
 * @param int                   $index   One-based banner index.
 * @param array<string, string> $default Default banner values.
 * @return array<string, string>
 */
function muukal_home_banner_grid_build_item( $args, $index, $default ) {
	$link_key  = 'link_' . $index;
	$image_key = 'image_' . $index;
	$alt_key   = 'alt_' . $index;

	$link  = ! empty( $args[ $link_key ] ) ? esc_url_raw( $args[ $link_key ] ) : $default['link'];
	$image = ! empty( $args[ $image_key ] ) ? esc_url_raw( $args[ $image_key ] ) : $default['image'];
	$alt   = ! empty( $args[ $alt_key ] ) ? sanitize_text_field( $args[ $alt_key ] ) : $default['alt'];

	return array(
		'link'  => '' !== $link ? $link : '#',
		'image' => $image,
		'alt'   => $alt,
	);
}

/**
 * Render the homepage banner grid.
 *
 * @param array<string, mixed> $args Shortcode attributes.
 * @return string
 */
function muukal_render_home_banner_grid( $args ) {
	$container_width = isset( $args['container_width'] ) ? sanitize_text_field( $args['container_width'] ) : '1900px';
	$padding_top     = isset( $args['padding_top'] ) ? sanitize_text_field( $args['padding_top'] ) : '75px';
	$items           = array();

	foreach ( muukal_home_banner_grid_default_items() as $index => $default ) {
		$items[] = muukal_home_banner_grid_build_item( $args, $index + 1, $default );
	}

	ob_start();
	?>
	<div class="pt-75 muukal-home-banner-grid" style="--muukal-home-banner-grid-pt: <?php echo esc_attr( $padding_top ); ?>;">
		<div class="container-fluid pl-55 pr-55" style="max-width: <?php echo esc_attr( $container_width ); ?>;">
			<div class="row muukal-home-banner-grid__row">
				<?php foreach ( $items as $item ) : ?>
					<div class="col-4 muukal-home-banner-grid__col">
						<a class="muukal-home-banner-grid__card" href="<?php echo esc_url( $item['link'] ); ?>">
							<img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['alt'] ); ?>" loading="lazy" decoding="async">
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php

	return ob_get_clean();
}
