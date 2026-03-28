<?php
/**
 * Muukal-style checkout payment method card.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$gateway_title = '';
$gateway_desc  = '';
$has_fields    = false;
$is_static     = ! empty( $gateway->is_static );

if ( is_object( $gateway ) && method_exists( $gateway, 'get_title' ) ) {
	$gateway_title = trim( wp_strip_all_tags( $gateway->get_title() ) );
} elseif ( isset( $gateway->title ) ) {
	$gateway_title = trim( wp_strip_all_tags( (string) $gateway->title ) );
}

if ( is_object( $gateway ) && method_exists( $gateway, 'get_description' ) ) {
	$gateway_desc = trim( wp_strip_all_tags( $gateway->get_description() ) );
} elseif ( isset( $gateway->description ) ) {
	$gateway_desc = trim( wp_strip_all_tags( (string) $gateway->description ) );
}

if ( is_object( $gateway ) && method_exists( $gateway, 'has_fields' ) ) {
	$has_fields = (bool) $gateway->has_fields();
}

$is_paypal     = false !== stripos( $gateway->id, 'paypal' ) || false !== stripos( $gateway_title, 'paypal' );
$is_card       = ! $is_paypal && (
	false !== stripos( $gateway->id, 'card' )
	|| false !== stripos( $gateway->id, 'oceanpay' )
	|| false !== stripos( $gateway_title, 'card' )
);
$display_title = $is_paypal ? __( 'Paypal', 'astra' ) : ( $is_card ? __( 'Credit Card', 'astra' ) : $gateway_title );
$display_desc  = $gateway_desc;

if ( '' === $display_desc ) {
	$display_desc = $is_paypal
		? __( 'Safe and popular digital wallet payment.', 'astra' )
		: __( 'Protection you can count on.', 'astra' );
}

$paypal_logo = get_theme_file_uri( 'assets/images/cart/paypal-wordmark.png' );
$visa_logo   = content_url( 'plugins/woocommerce/assets/images/payment-methods-cards/visa.svg' );
$master_logo = content_url( 'plugins/woocommerce/assets/images/payment-methods-cards/mastercard.svg' );
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?> <?php echo $gateway->chosen ? 'is-checked' : ''; ?>">
	<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>" class="muukal-checkout-payment-card <?php echo $is_paypal ? 'muukal-checkout-payment-card--paypal' : 'muukal-checkout-payment-card--card'; ?> <?php echo $is_static ? 'muukal-checkout-payment-card--static' : ''; ?>">
		<input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />

		<span class="muukal-checkout-payment-card__copy">
			<span class="muukal-checkout-payment-card__title"><?php echo esc_html( $display_title ); ?></span>
			<span class="muukal-checkout-payment-card__description"><?php echo esc_html( $display_desc ); ?></span>
		</span>

		<span class="muukal-checkout-payment-card__visual" aria-hidden="true">
			<?php if ( $is_paypal ) : ?>
				<span class="muukal-checkout-payment-card__brand muukal-checkout-payment-card__brand--paypal">
					<img src="<?php echo esc_url( $paypal_logo ); ?>" alt="" />
				</span>
			<?php elseif ( $is_card ) : ?>
				<span class="muukal-checkout-payment-card__brand muukal-checkout-payment-card__brand--card">
					<span class="muukal-checkout-payment-card__logos">
						<span class="muukal-checkout-payment-card__logo-box">
							<img src="<?php echo esc_url( $visa_logo ); ?>" alt="" />
						</span>
						<span class="muukal-checkout-payment-card__logo-box">
							<img src="<?php echo esc_url( $master_logo ); ?>" alt="" />
						</span>
					</span>
				</span>
			<?php else : ?>
				<span class="muukal-checkout-payment-card__fallback"><?php echo esc_html( $gateway_title ); ?></span>
			<?php endif; ?>
		</span>

		<span class="muukal-checkout-payment-card__check" aria-hidden="true">
			<svg viewBox="0 0 20 20"><path d="m3.5 10.5 4 4 9-9" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
		</span>
	</label>

	<?php if ( ! $is_static && ( $has_fields || '' !== $gateway_desc ) ) : ?>
		<div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" <?php if ( ! $gateway->chosen ) : ?>style="display:none;"<?php endif; ?>>
			<?php $gateway->payment_fields(); ?>
		</div>
	<?php endif; ?>
</li>
