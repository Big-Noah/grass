<?php
/**
 * Muukal product loop item price-first markup.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ASTRA_THEME_DIR . 'inc/render/product-loop-swatch.php';

/**
 * Render one product card with price emphasis for Elementor Loop Item.
 *
 * @param array $args Shortcode args.
 * @return string
 */
function muukal_render_product_loop_item_price( $args ) {
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
	<div class="muukal-loop-item muukal-loop-item-price" data-product-id="<?php echo esc_attr( $product_id ); ?>">
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
			<button type="button" class="muukal-card-wishlist" aria-label="<?php echo esc_attr__( 'Add to wishlist', 'astra' ); ?>">
				<span class="muukal-card-wishlist-icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" focusable="false">
						<path d="M12 21.35 10.55 20C5.4 15.24 2 12.09 2 8.25 2 5.41 4.24 3.25 7 3.25c1.56 0 3.06.73 4 1.88.94-1.15 2.44-1.88 4-1.88 2.76 0 5 2.16 5 5 0 3.84-3.4 6.99-8.55 11.76L12 21.35Z" />
					</svg>
				</span>
			</button>
			<div class="muukal-card-actions" aria-hidden="true">
				<button type="button" class="muukal-card-action muukal-card-action-primary">TRY ON</button>
				<a class="muukal-card-action muukal-card-action-secondary muukal-product-link" href="<?php echo esc_url( $default_state['link'] ); ?>">View Similar Frames</a>
			</div>
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
