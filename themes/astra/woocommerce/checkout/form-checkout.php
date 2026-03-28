<?php
/**
 * Muukal-style checkout form layout.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

$shipping_cards = muukal_astra_render_checkout_shipping_options();
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout muukal-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">
	<div class="muukal-checkout-layout">
		<main class="muukal-checkout-main">
			<?php if ( $checkout->get_checkout_fields() ) : ?>
				<section class="muukal-checkout-section muukal-checkout-section--address">
					<header class="muukal-checkout-section__header">
						<h2>1. <?php esc_html_e( 'Shipping Address', 'astra' ); ?></h2>
					</header>

					<div class="muukal-checkout-address-intro">
						<div class="muukal-checkout-address-actions">
							<button type="button" class="muukal-checkout-address-action" data-muukal-address-open="modal"><?php esc_html_e( 'Add New Address', 'astra' ); ?></button>
						</div>

						<div class="muukal-checkout-address-summary">
							<div class="muukal-checkout-address-summary__label"><?php esc_html_e( 'Current shipping address', 'astra' ); ?></div>
							<div class="muukal-checkout-address-summary__content" id="muukal-checkout-address-summary" data-empty-text="<?php esc_attr_e( 'No address added yet. Use Add New Address to fill in the checkout address.', 'astra' ); ?>">
								<?php esc_html_e( 'No address added yet. Use Add New Address to fill in the checkout address.', 'astra' ); ?>
							</div>
						</div>
					</div>

					<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

					<div class="muukal-checkout-address-modal" id="muukal-checkout-address-modal" aria-hidden="true">
						<div class="muukal-checkout-address-modal__backdrop" data-muukal-address-close="modal"></div>
						<div class="muukal-checkout-address-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="muukal-checkout-address-title">
							<div class="muukal-checkout-address-modal__header">
								<h3 id="muukal-checkout-address-title"><?php esc_html_e( 'Add New Address', 'astra' ); ?></h3>
								<button type="button" class="muukal-checkout-address-modal__close" data-muukal-address-close="modal" aria-label="<?php esc_attr_e( 'Close address dialog', 'astra' ); ?>">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>

							<div class="muukal-checkout-address-modal__body">
								<div class="muukal-checkout-customer" id="customer_details">
									<div class="muukal-checkout-customer__col muukal-checkout-customer__col--billing">
										<?php do_action( 'woocommerce_checkout_billing' ); ?>
									</div>

									<div class="muukal-checkout-customer__col muukal-checkout-customer__col--shipping">
										<?php do_action( 'woocommerce_checkout_shipping' ); ?>
									</div>
								</div>
							</div>

							<div class="muukal-checkout-address-modal__footer">
								<button type="button" class="muukal-checkout-address-modal__confirm" data-muukal-address-confirm="modal"><?php esc_html_e( 'Confirm', 'astra' ); ?></button>
							</div>
						</div>
					</div>

					<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
				</section>
			<?php endif; ?>

			<section class="muukal-checkout-section muukal-checkout-section--shipping">
				<header class="muukal-checkout-section__header">
					<h2>2. <?php esc_html_e( 'Shipping Method', 'astra' ); ?></h2>
					<p><?php esc_html_e( 'Free standard shipping on orders over $65 (US/CA/SG/AU/GB) and the shipping time excludes production time.', 'astra' ); ?></p>
				</header>

				<div class="muukal-checkout-section__body">
					<?php if ( '' !== $shipping_cards ) : ?>
						<?php echo $shipping_cards; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php else : ?>
						<p class="muukal-checkout-empty"><?php esc_html_e( 'Shipping options will appear here once a product shipping card is configured.', 'astra' ); ?></p>
					<?php endif; ?>
				</div>
			</section>

			<?php if ( wc_coupons_enabled() ) : ?>
				<section class="muukal-checkout-section muukal-checkout-section--discount">
					<header class="muukal-checkout-section__header">
						<h2>3. <?php esc_html_e( 'Discount', 'astra' ); ?></h2>
						<p><?php esc_html_e( 'One coupon code per order. Coupon cannot be combined with other discounts.', 'astra' ); ?></p>
					</header>

					<div class="muukal-checkout-coupon">
						<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon code', 'woocommerce' ); ?></label>
						<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon Code', 'astra' ); ?>" />
						<button type="submit" class="button muukal-checkout-coupon__button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply', 'woocommerce' ); ?></button>
					</div>
				</section>
			<?php endif; ?>

			<section class="muukal-checkout-section muukal-checkout-section--payment">
				<header class="muukal-checkout-section__header">
					<h2>4. <?php esc_html_e( 'Payment Details', 'astra' ); ?></h2>
					<p><?php esc_html_e( 'We accept all major debit and credit cards, FSA/HSA included.', 'astra' ); ?></p>
				</header>

				<?php woocommerce_checkout_payment(); ?>
			</section>
		</main>

		<aside class="muukal-checkout-sidebar" aria-label="<?php esc_attr_e( 'Order summary', 'astra' ); ?>">
			<div id="order_review" class="woocommerce-checkout-review-order">
				<?php woocommerce_order_review(); ?>
			</div>
		</aside>
	</div>
</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
