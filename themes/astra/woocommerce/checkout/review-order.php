<?php
/**
 * Muukal-style checkout order review.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;

$item_count      = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
$subtotal_html   = WC()->cart ? WC()->cart->get_cart_subtotal() : wc_price( 0 );
$selection       = muukal_astra_get_checkout_shipping_selection();
$shipping_total  = ! empty( $selection['price'] ) ? (float) $selection['price'] : ( WC()->cart ? (float) WC()->cart->get_fee_total() : 0 );
$tax_total       = WC()->cart ? (float) WC()->cart->get_total_tax() : 0;
$discount_total  = WC()->cart ? (float) WC()->cart->get_discount_total() : 0;
$grand_total     = WC()->cart ? (float) WC()->cart->get_total( 'edit' ) : 0;
?>
<div class="muukal-checkout-review woocommerce-checkout-review-order-table">
	<div class="muukal-checkout-review__head">
		<span><?php echo esc_html( sprintf( __( 'Items: %d', 'astra' ), $item_count ) ); ?></span>
		<a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'Back to cart', 'astra' ); ?></a>
	</div>

	<div class="muukal-checkout-review__items">
		<?php
		do_action( 'woocommerce_review_order_before_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$summary = muukal_astra_get_checkout_item_summary( $cart_item, $cart_item_key );

			if ( empty( $summary ) ) {
				continue;
			}
			?>
			<article class="muukal-checkout-review__item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
				<div class="muukal-checkout-review__item-media">
					<?php echo $summary['image_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<div class="muukal-checkout-review__item-variant">
						<?php if ( '' !== trim( wp_strip_all_tags( (string) $summary['frame_size'] ) ) ) : ?>
							<span><?php echo wp_kses_post( $summary['frame_size'] ); ?></span>
						<?php endif; ?>
						<?php if ( '' !== trim( wp_strip_all_tags( (string) $summary['frame_color'] ) ) ) : ?>
							<span><?php echo wp_kses_post( $summary['frame_color'] ); ?></span>
						<?php endif; ?>
					</div>
				</div>

				<div class="muukal-checkout-review__item-content">
					<div class="muukal-checkout-review__item-title"><?php echo esc_html( $summary['name'] ); ?></div>

					<?php if ( '' !== trim( wp_strip_all_tags( (string) $summary['lens_type'] ) ) ) : ?>
						<div class="muukal-checkout-review__item-row">
							<span><?php esc_html_e( 'Lens:', 'astra' ); ?></span>
							<span><?php echo wp_kses_post( $summary['lens_type'] ); ?></span>
						</div>
					<?php endif; ?>

					<div class="muukal-checkout-review__item-row">
						<span><?php esc_html_e( 'Quantity:', 'woocommerce' ); ?></span>
						<span><?php echo esc_html( (string) $summary['quantity'] ); ?></span>
					</div>

					<div class="muukal-checkout-review__item-row">
						<span><?php esc_html_e( 'Amount:', 'astra' ); ?></span>
						<span><?php echo wp_kses_post( $summary['subtotal_html'] ); ?></span>
					</div>
				</div>
			</article>
			<?php
		}

		do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</div>

	<div class="muukal-checkout-review__totals">
		<div class="muukal-checkout-review__total-row">
			<span><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
			<span><?php echo wp_kses_post( $subtotal_html ); ?></span>
		</div>

		<div class="muukal-checkout-review__total-row">
			<span><?php esc_html_e( 'Shipping Fee', 'astra' ); ?></span>
			<span><?php echo wp_kses_post( wc_price( $shipping_total ) ); ?></span>
		</div>

		<div class="muukal-checkout-review__total-row">
			<span><?php esc_html_e( 'Tax Free (Limited-Time)', 'astra' ); ?></span>
			<span><?php echo wp_kses_post( wc_price( $tax_total ) ); ?></span>
		</div>

		<div class="muukal-checkout-review__total-row muukal-checkout-review__total-row--discount">
			<span><?php esc_html_e( 'Discount', 'woocommerce' ); ?></span>
			<span>-<?php echo wp_kses_post( wc_price( $discount_total ) ); ?></span>
		</div>
	</div>

	<div class="muukal-checkout-review__grand-total">
		<span class="muukal-checkout-review__grand-label"><?php esc_html_e( 'Order Total', 'astra' ); ?></span>
		<span class="muukal-checkout-review__grand-value"><span class="muukal-checkout-review__currency"><?php esc_html_e( 'US', 'astra' ); ?></span> <?php echo wp_kses_post( wc_price( $grand_total ) ); ?></span>
	</div>
</div>
