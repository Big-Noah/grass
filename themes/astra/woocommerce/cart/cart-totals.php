<?php
/**
 * Muukal-style cart totals panel.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.3.6
 */

defined( 'ABSPATH' ) || exit;

$payments_image = get_theme_file_uri( 'assets/images/cart/muukal-payments.jpg' );
?>
<div class="cart_totals muukal-cart-totals <?php echo WC()->customer->has_calculated_shipping() ? 'calculated_shipping' : ''; ?>">
	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<div class="muukal-cart-summary">
		<h2><?php esc_html_e( 'Summary', 'astra' ); ?></h2>

		<div class="muukal-cart-summary__rows">
			<div class="muukal-cart-summary__row muukal-cart-summary__row--subtotal">
				<span class="muukal-cart-summary__label"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
				<span class="muukal-cart-summary__value"><?php wc_cart_totals_subtotal_html(); ?></span>
			</div>

			<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
				<div class="muukal-cart-summary__row muukal-cart-summary__row--discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
					<span class="muukal-cart-summary__label"><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
					<span class="muukal-cart-summary__value"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
				</div>
			<?php endforeach; ?>

			<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
				<?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>

				<div class="muukal-cart-summary__row muukal-cart-summary__row--shipping">
					<span class="muukal-cart-summary__label"><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></span>
					<span class="muukal-cart-summary__value"><?php wc_cart_totals_shipping_html(); ?></span>
				</div>

				<?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>
			<?php elseif ( WC()->cart->needs_shipping() && 'yes' === get_option( 'woocommerce_enable_shipping_calc' ) ) : ?>
				<div class="muukal-cart-summary__row muukal-cart-summary__row--shipping">
					<span class="muukal-cart-summary__label"><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></span>
					<span class="muukal-cart-summary__value"><?php woocommerce_shipping_calculator(); ?></span>
				</div>
			<?php endif; ?>

			<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
				<div class="muukal-cart-summary__row muukal-cart-summary__row--fee">
					<span class="muukal-cart-summary__label"><?php echo esc_html( $fee->name ); ?></span>
					<span class="muukal-cart-summary__value"><?php wc_cart_totals_fee_html( $fee ); ?></span>
				</div>
			<?php endforeach; ?>

			<?php
			if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) {
				$taxable_address = WC()->customer->get_taxable_address();
				$estimated_text  = '';

				if ( WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) {
					$estimated_text = sprintf(
						' <small>%s</small>',
						esc_html(
							sprintf(
								/* translators: %s location. */
								__( '(estimated for %s)', 'woocommerce' ),
								WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ]
							)
						)
					);
				}

				if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
					foreach ( WC()->cart->get_tax_totals() as $code => $tax ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						?>
						<div class="muukal-cart-summary__row muukal-cart-summary__row--tax tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
							<span class="muukal-cart-summary__label"><?php echo wp_kses_post( $tax->label . $estimated_text ); ?></span>
							<span class="muukal-cart-summary__value"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
						</div>
						<?php
					}
				} else {
					?>
					<div class="muukal-cart-summary__row muukal-cart-summary__row--tax">
						<span class="muukal-cart-summary__label"><?php echo wp_kses_post( WC()->countries->tax_or_vat() . $estimated_text ); ?></span>
						<span class="muukal-cart-summary__value"><?php wc_cart_totals_taxes_total_html(); ?></span>
					</div>
					<?php
				}
			}
			?>

			<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

			<div class="muukal-cart-summary__row muukal-cart-summary__row--total order-total">
				<span class="muukal-cart-summary__label"><?php esc_html_e( 'Grand total', 'astra' ); ?></span>
				<span class="muukal-cart-summary__value"><?php wc_cart_totals_order_total_html(); ?></span>
			</div>

			<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>
		</div>

		<?php if ( wc_coupons_enabled() ) : ?>
			<div class="muukal-cart-summary__coupon">
				<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>
				<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" />
				<button type="submit" class="button muukal-cart-summary__coupon-button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply', 'woocommerce' ); ?></button>
				<?php do_action( 'woocommerce_cart_coupon' ); ?>
			</div>
		<?php endif; ?>

		<div class="muukal-cart-summary__checkout">
			<div class="wc-proceed-to-checkout">
				<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
			</div>

			<div class="muukal-cart-summary__fast-checkout">
				<span><?php esc_html_e( 'Fast Checkout With', 'astra' ); ?></span>
			</div>

			<div class="muukal-cart-summary__paypal"><?php do_action( 'woocommerce_after_cart_totals' ); ?><?php do_action( 'muukal_astra_cart_paypal_buttons' ); ?></div>

			<div class="muukal-cart-summary__paypal-fallback">
				<img src="<?php echo esc_url( $payments_image ); ?>" alt="<?php esc_attr_e( 'PayPal and accepted payment methods', 'astra' ); ?>">
			</div>

			<ul class="muukal-cart-summary__benefits">
				<li><?php esc_html_e( 'Free standard shipping on orders over $65.00', 'astra' ); ?></li>
				<li><?php esc_html_e( '100% Money Back Guaranteed', 'astra' ); ?></li>
				<li><?php esc_html_e( '30-day Return & Exchange', 'astra' ); ?></li>
			</ul>
		</div>
	</div>
</div>
