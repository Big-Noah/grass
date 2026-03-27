<?php
/**
 * Muukal-style cart page layout.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.1.0
 */

defined( 'ABSPATH' ) || exit;

$toolbar_items = array(
	__( 'Prices are listed in USD', 'astra' ),
	__( 'Free standard shipping on orders over $65', 'astra' ),
	__( 'Lens options stay attached to each frame', 'astra' ),
);

do_action( 'woocommerce_before_cart' );
?>

<form class="woocommerce-cart-form muukal-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

	<div class="muukal-cart-layout">
		<section class="muukal-cart-main" aria-label="<?php esc_attr_e( 'Cart items', 'woocommerce' ); ?>">
			<?php if ( ! empty( $toolbar_items ) ) : ?>
				<div class="muukal-cart-toolbar" aria-label="<?php esc_attr_e( 'Shopping cart notes', 'astra' ); ?>">
					<?php foreach ( $toolbar_items as $toolbar_item ) : ?>
						<span class="muukal-cart-toolbar__item"><?php echo esc_html( $toolbar_item ); ?></span>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php do_action( 'woocommerce_before_cart_contents' ); ?>

			<div class="muukal-cart-items">
				<?php
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
					$product_name = apply_filters( 'woocommerce_cart_item_name', $_product ? $_product->get_name() : '', $cart_item, $cart_item_key );

					if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
						continue;
					}

					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					$product_image_id  = $_product->get_image_id();
					$thumbnail_markup  = $product_image_id
						? wp_get_attachment_image(
							$product_image_id,
							'medium_large',
							false,
							array(
								'class'    => 'attachment-medium_large size-medium_large',
								'loading'  => 'lazy',
								'decoding' => 'async',
							)
						)
						: $_product->get_image();
					$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $thumbnail_markup, $cart_item, $cart_item_key );
					$frame_price       = ! empty( $cart_item['muukal_lens_replica']['frame_price'] ) ? wc_price( (float) $cart_item['muukal_lens_replica']['frame_price'] ) : '';
					$item_data_rows    = apply_filters( 'woocommerce_get_item_data', array(), $cart_item );
					$detail_rows       = array();
					$frame_color       = '';
					$frame_size        = '';
					$lens_price        = '';
					$blue_light        = '';

					foreach ( $item_data_rows as $item_data_row ) {
						$label = isset( $item_data_row['name'] ) ? wc_clean( (string) $item_data_row['name'] ) : '';
						$value = isset( $item_data_row['display'] ) ? (string) $item_data_row['display'] : ( isset( $item_data_row['value'] ) ? (string) $item_data_row['value'] : '' );

						if ( '' === trim( wp_strip_all_tags( $value ) ) ) {
							continue;
						}

						if ( 'Frame Color' === $label ) {
							$frame_color = $value;
							continue;
						}

						if ( 'Frame Size' === $label ) {
							$frame_size = $value;
							continue;
						}

						if ( 'Lens Total' === $label ) {
							$lens_price = $value;
							continue;
						}

						if ( 'Blue Light' === $label ) {
							$blue_light = $value;
							continue;
						}

						$detail_rows[] = array(
							'label' => $label,
							'value' => $value,
						);
					}

					if ( $_product->is_sold_individually() ) {
						$min_quantity = 1;
						$max_quantity = 1;
					} else {
						$min_quantity = 0;
						$max_quantity = $_product->get_max_purchase_quantity();
					}

					$product_quantity = woocommerce_quantity_input(
						array(
							'input_name'   => "cart[{$cart_item_key}][qty]",
							'input_value'  => $cart_item['quantity'],
							'max_value'    => $max_quantity,
							'min_value'    => $min_quantity,
							'product_name' => $product_name,
						),
						$_product,
						false
					);

					$remove_link = apply_filters(
						'woocommerce_cart_item_remove_link',
						sprintf(
							'<a role="button" href="%s" class="muukal-cart-card__action muukal-cart-card__action--remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">%s</a>',
							esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
							esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
							esc_attr( $product_id ),
							esc_attr( $_product->get_sku() ),
							esc_html__( 'Remove', 'woocommerce' )
						),
						$cart_item_key
					);
					?>
					<article class="muukal-cart-card <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
						<div class="muukal-cart-card__header">
							<div class="muukal-cart-card__eyebrow">
								<div class="muukal-cart-card__title">
									<?php
									if ( ! $product_permalink ) {
										echo esc_html( $product_name );
									} else {
										echo wp_kses_post(
											apply_filters(
												'woocommerce_cart_item_name',
												sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), esc_html( $product_name ) ),
												$cart_item,
												$cart_item_key
											)
										);
									}
									?>
								</div>

								<?php if ( '' !== $frame_color ) : ?>
									<div class="muukal-cart-card__color">
										<span class="muukal-cart-card__eyebrow-label"><?php esc_html_e( 'Color:', 'astra' ); ?></span>
										<span><?php echo wp_kses_post( $frame_color ); ?></span>
									</div>
								<?php endif; ?>
							</div>

							<div class="muukal-cart-card__actions">
								<?php if ( $product_permalink ) : ?>
									<a class="muukal-cart-card__action" href="<?php echo esc_url( $product_permalink ); ?>"><?php esc_html_e( 'Edit', 'astra' ); ?></a>
								<?php endif; ?>
								<?php echo wp_kses_post( $remove_link ); ?>
							</div>
						</div>

						<div class="muukal-cart-card__body">
							<div class="muukal-cart-card__media">
								<?php
								if ( ! $product_permalink ) {
									echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								} else {
									printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
								?>
							</div>

							<div class="muukal-cart-card__content">
								<div class="muukal-cart-card__meta-grid">
									<div class="muukal-cart-card__details">
										<?php if ( '' !== $frame_size ) : ?>
											<div class="muukal-cart-card__row">
												<span class="muukal-cart-card__label"><?php esc_html_e( 'Frame Size', 'astra' ); ?></span>
												<span class="muukal-cart-card__value"><?php echo wp_kses_post( $frame_size ); ?></span>
											</div>
										<?php endif; ?>

										<?php foreach ( $detail_rows as $detail_row ) : ?>
											<div class="muukal-cart-card__row">
												<span class="muukal-cart-card__label"><?php echo esc_html( $detail_row['label'] ); ?></span>
												<span class="muukal-cart-card__value"><?php echo wp_kses_post( $detail_row['value'] ); ?></span>
											</div>
										<?php endforeach; ?>

										<?php if ( ! empty( $blue_light ) && 'no' !== strtolower( trim( wp_strip_all_tags( $blue_light ) ) ) ) : ?>
											<div class="muukal-cart-card__addon"><?php esc_html_e( 'Blue Light Blocking included', 'astra' ); ?></div>
										<?php endif; ?>

										<div class="muukal-cart-card__extra">
											<?php
											do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

											if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
												echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
											}
											?>
										</div>
									</div>

									<div class="muukal-cart-card__pricing">
										<?php if ( '' !== $frame_price ) : ?>
											<div class="muukal-cart-card__row muukal-cart-card__row--price">
												<span class="muukal-cart-card__label"><?php esc_html_e( 'Frame Price', 'astra' ); ?></span>
												<span class="muukal-cart-card__value"><?php echo wp_kses_post( $frame_price ); ?></span>
											</div>
										<?php endif; ?>

										<?php if ( '' !== $lens_price ) : ?>
											<div class="muukal-cart-card__row muukal-cart-card__row--price">
												<span class="muukal-cart-card__label"><?php esc_html_e( 'Lens Price', 'astra' ); ?></span>
												<span class="muukal-cart-card__value"><?php echo wp_kses_post( $lens_price ); ?></span>
											</div>
										<?php endif; ?>
									</div>
								</div>

								<div class="muukal-cart-card__footer">
									<div class="muukal-cart-card__quantity">
										<span class="muukal-cart-card__footer-label"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></span>
										<div class="muukal-cart-card__quantity-field">
											<?php echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</div>
									</div>

									<div class="muukal-cart-card__amount">
										<span class="muukal-cart-card__footer-label"><?php esc_html_e( 'Amount', 'astra' ); ?></span>
										<div class="muukal-cart-card__amount-value">
											<?php
											echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</article>
					<?php
				}
				?>
			</div>

			<?php do_action( 'woocommerce_cart_contents' ); ?>

			<div class="muukal-cart-form__actions">
				<div class="muukal-cart-form__hint"><?php esc_html_e( 'Update the cart after changing quantities.', 'astra' ); ?></div>

				<div class="muukal-cart-form__buttons">
					<button type="submit" class="button muukal-cart-update-button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>
					<?php do_action( 'woocommerce_cart_actions' ); ?>
				</div>

				<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
			</div>

			<?php do_action( 'woocommerce_after_cart_contents' ); ?>
		</section>

		<aside class="muukal-cart-sidebar" aria-label="<?php esc_attr_e( 'Order summary', 'astra' ); ?>">
			<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
			<?php woocommerce_cart_totals(); ?>
		</aside>
	</div>

	<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>

<?php do_action( 'woocommerce_after_cart' ); ?>
