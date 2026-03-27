<?php
/**
 * Astra functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Define Constants
 */
define( 'ASTRA_THEME_VERSION', '4.12.5' );
define( 'ASTRA_THEME_SETTINGS', 'astra-settings' );
define( 'ASTRA_THEME_DIR', trailingslashit( get_template_directory() ) );
define( 'ASTRA_THEME_URI', trailingslashit( esc_url( get_template_directory_uri() ) ) );
define( 'ASTRA_THEME_ORG_VERSION', file_exists( ASTRA_THEME_DIR . 'inc/w-org-version.php' ) );

/**
 * Minimum Version requirement of the Astra Pro addon.
 * This constant will be used to display the notice asking user to update the Astra addon to the version defined below.
 */
define( 'ASTRA_EXT_MIN_VER', '4.12.0' );

/**
 * Load in-house compatibility.
 */
if ( ASTRA_THEME_ORG_VERSION ) {
	require_once ASTRA_THEME_DIR . 'inc/w-org-version.php';
}

/**
 * Setup helper functions of Astra.
 */
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-theme-options.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-theme-strings.php';
require_once ASTRA_THEME_DIR . 'inc/core/common-functions.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-icons.php';

define( 'ASTRA_WEBSITE_BASE_URL', 'https://wpastra.com' );

/**
 * Update theme
 */
require_once ASTRA_THEME_DIR . 'inc/theme-update/astra-update-functions.php';
require_once ASTRA_THEME_DIR . 'inc/theme-update/class-astra-theme-background-updater.php';

/**
 * Fonts Files
 */
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-font-families.php';
if ( is_admin() ) {
	require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-fonts-data.php';
}

require_once ASTRA_THEME_DIR . 'inc/lib/webfont/class-astra-webfont-loader.php';
require_once ASTRA_THEME_DIR . 'inc/lib/docs/class-astra-docs-loader.php';
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-fonts.php';

require_once ASTRA_THEME_DIR . 'inc/dynamic-css/custom-menu-old-header.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/container-layouts.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/astra-icons.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-walker-page.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-enqueue-scripts.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-gutenberg-editor-css.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-wp-editor-css.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-command-palette.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/block-editor-compatibility.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/inline-on-mobile.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/content-background.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/dark-mode.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-dynamic-css.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-global-palette.php';

// Enable NPS Survey only if the starter templates version is < 4.3.7 or > 4.4.4 to prevent fatal error.
if ( ! defined( 'ASTRA_SITES_VER' ) || version_compare( ASTRA_SITES_VER, '4.3.7', '<' ) || version_compare( ASTRA_SITES_VER, '4.4.4', '>' ) ) {
	// NPS Survey Integration
	require_once ASTRA_THEME_DIR . 'inc/lib/class-astra-nps-notice.php';
	require_once ASTRA_THEME_DIR . 'inc/lib/class-astra-nps-survey.php';
}

/**
 * Custom template tags for this theme.
 */
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-attr.php';
require_once ASTRA_THEME_DIR . 'inc/template-tags.php';

require_once ASTRA_THEME_DIR . 'inc/widgets.php';
require_once ASTRA_THEME_DIR . 'inc/core/theme-hooks.php';
require_once ASTRA_THEME_DIR . 'inc/admin-functions.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-memory-limit-notice.php';
require_once ASTRA_THEME_DIR . 'inc/core/sidebar-manager.php';

/**
 * Markup Functions
 */
require_once ASTRA_THEME_DIR . 'inc/markup-extras.php';
require_once ASTRA_THEME_DIR . 'inc/extras.php';
require_once ASTRA_THEME_DIR . 'inc/blog/blog-config.php';
require_once ASTRA_THEME_DIR . 'inc/blog/blog.php';
require_once ASTRA_THEME_DIR . 'inc/blog/single-blog.php';

/**
 * Markup Files
 */
require_once ASTRA_THEME_DIR . 'inc/template-parts.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-loop.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-mobile-header.php';

/**
 * Functions and definitions.
 */
require_once ASTRA_THEME_DIR . 'inc/class-astra-after-setup-theme.php';

// Required files.
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-admin-helper.php';

require_once ASTRA_THEME_DIR . 'inc/schema/class-astra-schema.php';

/* Setup API */
require_once ASTRA_THEME_DIR . 'admin/includes/class-astra-learn.php';
require_once ASTRA_THEME_DIR . 'admin/includes/class-astra-api-init.php';

if ( is_admin() ) {
	/**
	 * Admin Menu Settings
	 */
	require_once ASTRA_THEME_DIR . 'inc/core/class-astra-admin-settings.php';
	require_once ASTRA_THEME_DIR . 'admin/class-astra-admin-loader.php';
	require_once ASTRA_THEME_DIR . 'inc/lib/astra-notices/class-astra-notices.php';
}

/**
 * Metabox additions.
 */
require_once ASTRA_THEME_DIR . 'inc/metabox/class-astra-meta-boxes.php';
require_once ASTRA_THEME_DIR . 'inc/metabox/class-astra-meta-box-operations.php';
require_once ASTRA_THEME_DIR . 'inc/metabox/class-astra-elementor-editor-settings.php';

/**
 * Customizer additions.
 */
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-customizer.php';

/**
 * Astra Modules.
 */
require_once ASTRA_THEME_DIR . 'inc/modules/posts-structures/class-astra-post-structures.php';
require_once ASTRA_THEME_DIR . 'inc/modules/related-posts/class-astra-related-posts.php';

/**
 * Compatibility
 */
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-gutenberg.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-jetpack.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/woocommerce/class-astra-woocommerce.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/edd/class-astra-edd.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/lifterlms/class-astra-lifterlms.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/learndash/class-astra-learndash.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-beaver-builder.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-bb-ultimate-addon.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-contact-form-7.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-visual-composer.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-site-origin.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-gravity-forms.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-bne-flyout.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-ubermeu.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-divi-builder.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-amp.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-yoast-seo.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/surecart/class-astra-surecart.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-starter-content.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-buddypress.php';
require_once ASTRA_THEME_DIR . 'inc/addons/transparent-header/class-astra-ext-transparent-header.php';
require_once ASTRA_THEME_DIR . 'inc/addons/breadcrumbs/class-astra-breadcrumbs.php';
require_once ASTRA_THEME_DIR . 'inc/addons/scroll-to-top/class-astra-scroll-to-top.php';
require_once ASTRA_THEME_DIR . 'inc/addons/heading-colors/class-astra-heading-colors.php';
require_once ASTRA_THEME_DIR . 'inc/builder/class-astra-builder-loader.php';

// Elementor Compatibility requires PHP 5.4 for namespaces.
if ( version_compare( PHP_VERSION, '5.4', '>=' ) ) {
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-elementor.php';
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-elementor-pro.php';
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-web-stories.php';
}

// Beaver Themer compatibility requires PHP 5.3 for anonymous functions.
if ( version_compare( PHP_VERSION, '5.3', '>=' ) ) {
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-beaver-themer.php';
}

require_once ASTRA_THEME_DIR . 'inc/core/markup/class-astra-markup.php';

/**
 * Load deprecated functions
 */
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-filters.php';
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-hooks.php';
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-functions.php';

require_once ASTRA_THEME_DIR . 'inc/shortcodes/header-shortcode.php';
require_once ASTRA_THEME_DIR . 'inc/shortcodes/footer-shortcode.php';
require_once ASTRA_THEME_DIR . 'inc/shortcodes/featured-collection-nav-shortcode.php';
require_once ASTRA_THEME_DIR . 'inc/shortcodes/index-nav-box-shortcode.php';
require_once ASTRA_THEME_DIR . 'inc/shortcodes/why-choose-shortcode.php';
require_once ASTRA_THEME_DIR . 'inc/shortcodes/product-loop-item-shortcode.php';
require_once ASTRA_THEME_DIR . 'inc/shortcodes/product-loop-item-price-shortcode.php';
require_once ASTRA_THEME_DIR . 'inc/shortcodes/product-filter-archive-shortcode.php';
require_once ASTRA_THEME_DIR . 'inc/shortcodes/product-detail-template-shortcode.php';

/**
 * Enqueue custom account page styling for the WooCommerce My Account page.
 */
function muukal_astra_enqueue_account_assets() {
	if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
		return;
	}

	$style_relative_path  = 'assets/css/muukal-account.css';
	$style_absolute_path  = ASTRA_THEME_DIR . $style_relative_path;
	$style_version        = file_exists( $style_absolute_path ) ? (string) filemtime( $style_absolute_path ) : ASTRA_THEME_VERSION;
	$script_relative_path = 'assets/js/muukal-account.js';
	$script_absolute_path = ASTRA_THEME_DIR . $script_relative_path;
	$script_version       = file_exists( $script_absolute_path ) ? (string) filemtime( $script_absolute_path ) : ASTRA_THEME_VERSION;

	wp_enqueue_style(
		'astra-muukal-account',
		ASTRA_THEME_URI . $style_relative_path,
		array(),
		$style_version
	);

	wp_enqueue_script(
		'astra-muukal-account',
		ASTRA_THEME_URI . $script_relative_path,
		array(),
		$script_version,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'muukal_astra_enqueue_account_assets', 40 );

/**
 * Force WooCommerce to use the custom account form template from the theme.
 *
 * @param string $template Resolved template path.
 * @param string $template_name Requested template name.
 * @return string
 */
function muukal_astra_locate_account_form_template( $template, $template_name ) {
	$custom_templates = array(
		'myaccount/form-login.php' => ASTRA_THEME_DIR . 'woocommerce/myaccount/form-login.php',
		'myaccount/my-account.php' => ASTRA_THEME_DIR . 'woocommerce/myaccount/my-account.php',
		'myaccount/navigation.php' => ASTRA_THEME_DIR . 'woocommerce/myaccount/navigation.php',
	);

	if ( ! isset( $custom_templates[ $template_name ] ) ) {
		return $template;
	}

	$custom_template = $custom_templates[ $template_name ];

	return file_exists( $custom_template ) ? $custom_template : $template;
}
add_filter( 'woocommerce_locate_template', 'muukal_astra_locate_account_form_template', 20, 2 );

/**
 * Always expose registration on the My Account page.
 *
 * @return string
 */
function muukal_astra_enable_myaccount_registration() {
	return 'yes';
}
add_filter( 'pre_option_woocommerce_enable_myaccount_registration', 'muukal_astra_enable_myaccount_registration' );

/**
 * Require customers to create their own password during registration.
 *
 * @return string
 */
function muukal_astra_disable_generated_account_password() {
	return 'no';
}
add_filter( 'pre_option_woocommerce_registration_generate_password', 'muukal_astra_disable_generated_account_password' );

/**
 * Remove WooCommerce's default privacy text so the custom note can take its place.
 */
function muukal_astra_remove_default_register_privacy_text() {
	remove_action( 'woocommerce_register_form', 'wc_registration_privacy_policy_text', 20 );
}
add_action( 'init', 'muukal_astra_remove_default_register_privacy_text' );

/**
 * Tune account page copy to match the desired login/register layout.
 *
 * @param string $translated_text Translated string.
 * @param string $text Original string.
 * @param string $domain Text domain.
 * @return string
 */
function muukal_astra_customize_account_copy( $translated_text, $text, $domain ) {
	if ( is_admin() || ! function_exists( 'is_account_page' ) || ! is_account_page() || is_user_logged_in() ) {
		return $translated_text;
	}

	$replacements = array(
		'Login'                     => 'Sign In',
		'Register'                  => 'Create Account',
		'Log in'                    => 'Login',
		'Lost your password?'       => 'Forgot your password?',
		'Username or email address' => 'Email Address',
		'Remember me'               => 'Remember Me',
		'Email address'             => 'Email Address',
	);

	return isset( $replacements[ $text ] ) ? $replacements[ $text ] : $translated_text;
}
add_filter( 'gettext', 'muukal_astra_customize_account_copy', 20, 3 );

/**
 * Add custom registration helper copy under the registration button.
 */
function muukal_astra_render_account_register_note() {
	if ( is_user_logged_in() ) {
		return;
	}

	$privacy_policy_url = function_exists( 'get_privacy_policy_url' ) ? get_privacy_policy_url() : '';
	?>
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
	<?php
}
add_action( 'woocommerce_register_form_end', 'muukal_astra_render_account_register_note', 30 );

/**
 * Return grouped labels for the account navigation sidebar.
 *
 * @return array<string,string>
 */
function muukal_astra_account_nav_groups() {
	return array(
		'orders'           => __( 'My Order', 'astra' ),
		'downloads'        => __( 'My Order', 'astra' ),
		'dashboard'        => __( 'My Account', 'astra' ),
		'edit-account'     => __( 'My Account', 'astra' ),
		'edit-address'     => __( 'My Account', 'astra' ),
		'payment-methods'  => __( 'My Account', 'astra' ),
		'customer-logout'  => __( 'Sign Out', 'astra' ),
	);
}

/**
 * Get the current account page title.
 *
 * @return string
 */
function muukal_astra_get_account_content_title() {
	if ( ! function_exists( 'wc_get_account_menu_items' ) ) {
		return '';
	}

	foreach ( wc_get_account_menu_items() as $endpoint => $label ) {
		if ( wc_is_current_account_menu_item( $endpoint ) ) {
			return $label;
		}
	}

	return __( 'My Account', 'astra' );
}

/**
 * Output first name and last name fields on the account registration form.
 */
function muukal_astra_render_account_register_name_fields() {
	$first_name = isset( $_POST['billing_first_name'] ) ? wc_clean( wp_unslash( $_POST['billing_first_name'] ) ) : '';
	$last_name  = isset( $_POST['billing_last_name'] ) ? wc_clean( wp_unslash( $_POST['billing_last_name'] ) ) : '';
	?>
	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="reg_billing_first_name"><?php esc_html_e( 'First Name', 'astra' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_first_name" id="reg_billing_first_name" autocomplete="given-name" value="<?php echo esc_attr( $first_name ); ?>" required />
	</p>
	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="reg_billing_last_name"><?php esc_html_e( 'Last Name', 'astra' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_last_name" id="reg_billing_last_name" autocomplete="family-name" value="<?php echo esc_attr( $last_name ); ?>" required />
	</p>
	<?php
}
add_action( 'woocommerce_register_form_start', 'muukal_astra_render_account_register_name_fields' );

/**
 * Validate the custom registration fields.
 *
 * @param string   $username Username being registered.
 * @param string   $email Email being registered.
 * @param WP_Error $errors Validation errors.
 */
function muukal_astra_validate_account_register_name_fields( $username, $email, $errors ) {
	if ( empty( $_POST['billing_first_name'] ) ) {
		$errors->add( 'billing_first_name_error', __( 'First name is required.', 'astra' ) );
	}

	if ( empty( $_POST['billing_last_name'] ) ) {
		$errors->add( 'billing_last_name_error', __( 'Last name is required.', 'astra' ) );
	}
}
add_action( 'woocommerce_register_post', 'muukal_astra_validate_account_register_name_fields', 10, 3 );

/**
 * Persist custom account registration fields to the customer profile.
 *
 * @param int $customer_id Newly created customer ID.
 */
function muukal_astra_save_account_register_name_fields( $customer_id ) {
	if ( ! empty( $_POST['billing_first_name'] ) ) {
		$first_name = wc_clean( wp_unslash( $_POST['billing_first_name'] ) );
		update_user_meta( $customer_id, 'first_name', $first_name );
		update_user_meta( $customer_id, 'billing_first_name', $first_name );
	}

	if ( ! empty( $_POST['billing_last_name'] ) ) {
		$last_name = wc_clean( wp_unslash( $_POST['billing_last_name'] ) );
		update_user_meta( $customer_id, 'last_name', $last_name );
		update_user_meta( $customer_id, 'billing_last_name', $last_name );
	}
}
add_action( 'woocommerce_created_customer', 'muukal_astra_save_account_register_name_fields' );

/**
 * Enqueue the Muukal-inspired cart styling on the classic WooCommerce cart page.
 */
function muukal_astra_enqueue_cart_assets() {
	if ( ! function_exists( 'is_cart' ) || ! is_cart() ) {
		return;
	}

	$relative_path = 'assets/css/muukal-cart.css';
	$absolute_path = ASTRA_THEME_DIR . $relative_path;
	$version       = file_exists( $absolute_path ) ? (string) filemtime( $absolute_path ) : ASTRA_THEME_VERSION;

	wp_enqueue_style(
		'astra-muukal-cart',
		ASTRA_THEME_URI . $relative_path,
		array(),
		$version
	);
}
add_action( 'wp_enqueue_scripts', 'muukal_astra_enqueue_cart_assets', 40 );

/**
 * Render PayPal express buttons in a dedicated hook so the custom cart summary
 * can place them below the main checkout CTA.
 *
 * @param string $hook Default PayPal hook location.
 * @return string
 */
function muukal_astra_paypal_cart_button_hook( $hook ) {
	return 'muukal_astra_cart_paypal_buttons';
}
add_filter( 'woocommerce_paypal_payments_proceed_to_checkout_button_renderer_hook', 'muukal_astra_paypal_cart_button_hook' );
