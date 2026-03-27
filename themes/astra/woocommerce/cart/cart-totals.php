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
$subtotal_html  = WC()->cart ? WC()->cart->get_cart_subtotal() : wc_price( 0 );
$shipping_total = WC()->cart ? (float) WC()->cart->get_shipping_total() : 0;
$shipping_label = WC()->cart && WC()->cart->needs_shipping() ? __( 'Standard Shipping(12-21 Days)', 'astra' ) : __( 'Shipping calculated at checkout', 'astra' );
$tax_total      = WC()->cart ? (float) WC()->cart->get_total_tax() : 0;
$discount_total = WC()->cart ? (float) WC()->cart->get_discount_total() : 0;
$grand_total    = WC()->cart ? WC()->cart->get_total( 'edit' ) : 0;
$paypal_url     = '#';
$card_url       = '#';
?>
<div class="cart_totals muukal-cart-totals <?php echo WC()->customer->has_calculated_shipping() ? 'calculated_shipping' : ''; ?>">
	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<div class="muukal-cart-summary" id="cart-right-box">
		<h2><?php esc_html_e( 'Summary', 'astra' ); ?></h2>

		<div class="muukal-cart-summary__rows">
			<div class="muukal-cart-summary__row muukal-cart-summary__row--subtotal">
				<span class="muukal-cart-summary__label"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
				<span class="muukal-cart-summary__value"><?php echo wp_kses_post( $subtotal_html ); ?></span>
			</div>
			<div class="muukal-cart-summary__row muukal-cart-summary__row--shipping">
				<span class="muukal-cart-summary__label"><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></span>
				<span class="muukal-cart-summary__value"><?php echo wp_kses_post( wc_price( $shipping_total ) ); ?></span>
			</div>

			<div class="muukal-cart-summary__shipping-note">
				<span class="muukal-cart-summary__shipping-text"><?php echo esc_html( $shipping_label ); ?></span>
				<span class="muukal-cart-summary__change"><?php esc_html_e( 'Change', 'astra' ); ?></span>
			</div>

			<div class="muukal-cart-summary__row muukal-cart-summary__row--tax">
				<span class="muukal-cart-summary__label"><?php esc_html_e( 'Tax-Free (Limited-Time)', 'astra' ); ?></span>
				<span class="muukal-cart-summary__value"><?php echo wp_kses_post( wc_price( $tax_total ) ); ?></span>
			</div>

			<div class="muukal-cart-summary__row muukal-cart-summary__row--discount">
				<span class="muukal-cart-summary__label"><?php esc_html_e( 'Discount', 'woocommerce' ); ?></span>
				<span class="muukal-cart-summary__value">-<?php echo wp_kses_post( wc_price( $discount_total ) ); ?></span>
			</div>

			<div class="muukal-cart-summary__row muukal-cart-summary__row--total order-total">
				<span class="muukal-cart-summary__label"><?php esc_html_e( 'Grand total', 'astra' ); ?></span>
				<span class="muukal-cart-summary__value"><span class="muukal-cart-summary__currency"><?php esc_html_e( 'US', 'astra' ); ?></span> <?php echo wp_kses_post( wc_price( $grand_total ) ); ?></span>
			</div>
		</div>

		<div class="muukal-cart-summary__coupon">
			<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>
			<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon Code', 'astra' ); ?>" />
			<button type="submit" class="button muukal-cart-summary__coupon-button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply', 'woocommerce' ); ?></button>
			<?php do_action( 'woocommerce_cart_coupon' ); ?>
		</div>

		<div class="muukal-cart-summary__promo-list" aria-label="<?php esc_attr_e( 'Coupon recommendations', 'astra' ); ?>">
			<div class="muukal-cart-summary__promo-card">
				<div class="muukal-cart-summary__promo-copy">[ENJOY3] Any 3 Pairs for $119</div>
				<a class="muukal-cart-summary__promo-action" href="#"><?php esc_html_e( 'USE', 'astra' ); ?></a>
			</div>
			<div class="muukal-cart-summary__promo-card">
				<div class="muukal-cart-summary__promo-copy">[MKBOGO] Second Pair Free</div>
				<a class="muukal-cart-summary__promo-action" href="#"><?php esc_html_e( 'USE', 'astra' ); ?></a>
			</div>
		</div>

		<div class="muukal-cart-summary__checkout">
			<div class="wc-proceed-to-checkout">
				<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
			</div>

			<div class="muukal-cart-summary__fast-checkout">
				<span><?php esc_html_e( 'Fast Checkout With', 'astra' ); ?></span>
			</div>

			<div class="muukal-cart-summary__express">
				<a class="muukal-cart-summary__express-button muukal-cart-summary__express-button--paypal" href="<?php echo esc_url( $paypal_url ); ?>">
					<span class="muukal-cart-summary__express-logo">PayPal</span>
				</a>
				<a class="muukal-cart-summary__express-button muukal-cart-summary__express-button--card" href="<?php echo esc_url( $card_url ); ?>">
					<span class="muukal-cart-summary__express-card-icons" aria-hidden="true">
						<span>VISA</span>
						<span>MC</span>
						<span>AMEX</span>
					</span>
					<span class="muukal-cart-summary__express-card-copy"><?php esc_html_e( 'Debit or Credit Card', 'astra' ); ?></span>
				</a>
			</div>

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
