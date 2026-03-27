<?php
/**
 * Custom My Account login and registration layout.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$privacy_policy_url = function_exists( 'get_privacy_policy_url' ) ? get_privacy_policy_url() : '';

do_action( 'woocommerce_before_customer_login_form' );
?>

<div class="muukal-account-shell" id="customer_login">
	<div class="muukal-account-panel muukal-account-panel--login">
		<h2 class="muukal-account-title"><?php esc_html_e( 'Sign In', 'astra' ); ?></h2>

		<form class="woocommerce-form woocommerce-form-login login muukal-account-form" method="post" novalidate>
			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="username"><?php esc_html_e( 'Email Address', 'astra' ); ?> <span class="required" aria-hidden="true">*</span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" placeholder="<?php esc_attr_e( 'Email', 'astra' ); ?>" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" />
			</p>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="password"><?php esc_html_e( 'Password', 'astra' ); ?> <span class="required" aria-hidden="true">*</span></label>
				<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" placeholder="<?php esc_attr_e( 'Password', 'astra' ); ?>" required aria-required="true" />
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="form-row woocommerce-form-row">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" />
					<span><?php esc_html_e( 'Remember Me', 'astra' ); ?></span>
				</label>
			</p>

			<p class="form-row muukal-account-actions">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" class="woocommerce-button button woocommerce-form-login__submit<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="login" value="<?php esc_attr_e( 'Login', 'astra' ); ?>"><?php esc_html_e( 'Login', 'astra' ); ?></button>
			</p>

			<p class="woocommerce-LostPassword lost_password">
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Forgot your password?', 'astra' ); ?></a>
			</p>

			<?php do_action( 'woocommerce_login_form_end' ); ?>
		</form>
	</div>

	<div class="muukal-account-panel muukal-account-panel--register">
		<h2 class="muukal-account-title"><?php esc_html_e( 'Create Account', 'astra' ); ?></h2>

		<form method="post" class="woocommerce-form woocommerce-form-register register muukal-account-form" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?> <span class="required" aria-hidden="true">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" placeholder="<?php esc_attr_e( 'Username', 'woocommerce' ); ?>" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" />
				</p>
			<?php endif; ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_email"><?php esc_html_e( 'Email Address', 'astra' ); ?> <span class="required" aria-hidden="true">*</span></label>
				<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" placeholder="<?php esc_attr_e( 'Email', 'astra' ); ?>" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required aria-required="true" />
			</p>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_password"><?php esc_html_e( 'Password', 'astra' ); ?> <span class="required" aria-hidden="true">*</span></label>
				<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" placeholder="<?php esc_attr_e( 'Password', 'astra' ); ?>" required aria-required="true" />
			</p>

			<?php do_action( 'woocommerce_register_form' ); ?>

			<p class="woocommerce-form-row form-row muukal-account-actions">
				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<button type="submit" class="woocommerce-Button woocommerce-button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?> woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Create Account', 'astra' ); ?>"><?php esc_html_e( 'Create Account', 'astra' ); ?></button>
			</p>

			<p class="muukal-account-register-note">
				<?php esc_html_e( 'By signing up, you agree to receive marketing emails and to our privacy policy.', 'astra' ); ?>
			</p>
			<p class="muukal-account-register-subnote">
				<?php if ( $privacy_policy_url ) : ?>
					<?php printf( wp_kses_post( __( 'You may unsubscribe at any time. Visit our <a href="%s">Privacy Policy here</a>.', 'astra' ) ), esc_url( $privacy_policy_url ) ); ?>
				<?php else : ?>
					<?php esc_html_e( 'You may unsubscribe at any time.', 'astra' ); ?>
				<?php endif; ?>
			</p>

			<?php do_action( 'woocommerce_register_form_end' ); ?>
		</form>
	</div>
</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
