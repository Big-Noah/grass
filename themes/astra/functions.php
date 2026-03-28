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
 * Build a product category term context for the subcategory pills shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return WP_Term|null
 */
function muukal_astra_get_subcategory_pills_parent_term( $atts ) {
	$taxonomy = 'product_cat';

	if ( ! empty( $atts['parent_id'] ) ) {
		$term = get_term( (int) $atts['parent_id'], $taxonomy );
		return ( $term instanceof WP_Term && ! is_wp_error( $term ) ) ? $term : null;
	}

	if ( ! empty( $atts['parent_slug'] ) ) {
		$term = get_term_by( 'slug', sanitize_title( $atts['parent_slug'] ), $taxonomy );
		return ( $term instanceof WP_Term && ! is_wp_error( $term ) ) ? $term : null;
	}

	$queried_object = get_queried_object();

	if ( $queried_object instanceof WP_Term && $taxonomy === $queried_object->taxonomy ) {
		if ( ! empty( $queried_object->parent ) ) {
			$parent_term = get_term( (int) $queried_object->parent, $taxonomy );
			return ( $parent_term instanceof WP_Term && ! is_wp_error( $parent_term ) ) ? $parent_term : $queried_object;
		}

		return $queried_object;
	}

	return null;
}

/**
 * Return the shared inline style for the product subcategory pills.
 *
 * @return string
 */
function muukal_astra_get_subcategory_pills_styles() {
	return '
	.muukal-subcategory-pills {
		display: flex;
		flex-wrap: wrap;
		gap: 14px;
		align-items: center;
	}

	.muukal-subcategory-pills__item {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		min-height: 46px;
		padding: 10px 22px;
		border: 1px solid #ff6a73;
		border-radius: 999px;
		background: #ffffff;
		color: #202020;
		font-size: 16px;
		font-weight: 500;
		line-height: 1.2;
		text-decoration: none;
		transition: transform .18s ease, border-color .18s ease, color .18s ease, box-shadow .18s ease, background-color .18s ease;
	}

	.muukal-subcategory-pills__item:hover,
	.muukal-subcategory-pills__item:focus {
		color: #ff565c;
		border-color: #ff565c;
		box-shadow: 0 10px 22px rgba(255, 86, 92, 0.12);
		transform: translateY(-1px);
		outline: none;
	}

	.muukal-subcategory-pills__item--active {
		border-color: #ff565c;
		color: #ff565c;
		box-shadow: 0 8px 18px rgba(255, 86, 92, 0.1);
	}

	.muukal-subcategory-pills__item--accent {
		border-color: #a855f7;
		color: #7c3aed;
	}

	.muukal-subcategory-pills__item--accent:hover,
	.muukal-subcategory-pills__item--accent:focus {
		border-color: #9333ea;
		color: #6d28d9;
		box-shadow: 0 10px 22px rgba(147, 51, 234, 0.14);
	}

	@media (max-width: 767px) {
		.muukal-subcategory-pills {
			gap: 10px;
		}

		.muukal-subcategory-pills__item {
			min-height: 40px;
			padding: 8px 16px;
			font-size: 14px;
		}
	}';
}

/**
 * Render child product categories as pill links.
 *
 * Shortcode examples:
 * [muukal_subcategory_pills]
 * [muukal_subcategory_pills include_view_all="yes" promotions_label="Current Promotions" promotions_url="/sale/"]
 * [muukal_subcategory_pills parent_slug="women-eyeglasses"]
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function muukal_astra_render_subcategory_pills_shortcode( $atts ) {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return '';
	}

	$atts = shortcode_atts(
		array(
			'parent_id'         => '',
			'parent_slug'       => '',
			'hide_empty'        => 'false',
			'include_view_all'  => 'false',
			'view_all_label'    => 'View All',
			'promotions_label'  => '',
			'promotions_url'    => '',
			'class'             => '',
		),
		(array) $atts,
		'muukal_subcategory_pills'
	);

	$parent_term = muukal_astra_get_subcategory_pills_parent_term( $atts );

	if ( ! $parent_term ) {
		return '';
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => (int) $parent_term->term_id,
			'hide_empty' => filter_var( $atts['hide_empty'], FILTER_VALIDATE_BOOLEAN ),
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return '';
	}

	$queried_object    = get_queried_object();
	$current_term_id   = ( $queried_object instanceof WP_Term && 'product_cat' === $queried_object->taxonomy ) ? (int) $queried_object->term_id : 0;
	$wrapper_classes   = trim( 'muukal-subcategory-pills ' . sanitize_html_class( $atts['class'] ) );
	$include_view_all  = filter_var( $atts['include_view_all'], FILTER_VALIDATE_BOOLEAN );
	$promotions_label  = trim( (string) $atts['promotions_label'] );
	$promotions_url    = trim( (string) $atts['promotions_url'] );
	$styles            = muukal_astra_get_subcategory_pills_styles();

	ob_start();
	?>
	<style><?php echo $styles; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></style>
	<div class="<?php echo esc_attr( $wrapper_classes ); ?>">
		<?php foreach ( $terms as $term ) : ?>
			<?php
			$item_classes = 'muukal-subcategory-pills__item';
			if ( $current_term_id === (int) $term->term_id ) {
				$item_classes .= ' muukal-subcategory-pills__item--active';
			}
			?>
			<a class="<?php echo esc_attr( $item_classes ); ?>" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
				<?php echo esc_html( $term->name ); ?>
			</a>
		<?php endforeach; ?>

		<?php if ( $include_view_all ) : ?>
			<a class="muukal-subcategory-pills__item" href="<?php echo esc_url( get_term_link( $parent_term ) ); ?>">
				<?php echo esc_html( $atts['view_all_label'] ); ?>
			</a>
		<?php endif; ?>

		<?php if ( $promotions_label && $promotions_url ) : ?>
			<a class="muukal-subcategory-pills__item muukal-subcategory-pills__item--accent" href="<?php echo esc_url( $promotions_url ); ?>">
				<?php echo esc_html( $promotions_label ); ?>
			</a>
		<?php endif; ?>
	</div>
	<?php

	return (string) ob_get_clean();
}
add_shortcode( 'muukal_subcategory_pills', 'muukal_astra_render_subcategory_pills_shortcode' );

/**
 * Force WooCommerce to use the custom checkout templates from the theme.
 *
 * @param string $template Resolved template path.
 * @param string $template_name Requested template name.
 * @return string
 */
function muukal_astra_locate_checkout_templates( $template, $template_name ) {
	$custom_templates = array(
		'checkout/form-checkout.php'  => ASTRA_THEME_DIR . 'woocommerce/checkout/form-checkout.php',
		'checkout/payment.php'        => ASTRA_THEME_DIR . 'woocommerce/checkout/payment.php',
		'checkout/payment-method.php' => ASTRA_THEME_DIR . 'woocommerce/checkout/payment-method.php',
		'checkout/review-order.php'   => ASTRA_THEME_DIR . 'woocommerce/checkout/review-order.php',
	);

	if ( ! isset( $custom_templates[ $template_name ] ) ) {
		return $template;
	}

	$custom_template = $custom_templates[ $template_name ];

	return file_exists( $custom_template ) ? $custom_template : $template;
}
add_filter( 'woocommerce_locate_template', 'muukal_astra_locate_checkout_templates', 25, 2 );

/**
 * Force checkout templates even when WooCommerce resolves them from cache.
 *
 * @param string $template Resolved template path.
 * @param string $template_name Requested template name.
 * @return string
 */
function muukal_astra_force_checkout_templates( $template, $template_name ) {
	$custom_templates = array(
		'checkout/form-checkout.php'  => ASTRA_THEME_DIR . 'woocommerce/checkout/form-checkout.php',
		'checkout/payment.php'        => ASTRA_THEME_DIR . 'woocommerce/checkout/payment.php',
		'checkout/payment-method.php' => ASTRA_THEME_DIR . 'woocommerce/checkout/payment-method.php',
		'checkout/review-order.php'   => ASTRA_THEME_DIR . 'woocommerce/checkout/review-order.php',
	);

	if ( ! isset( $custom_templates[ $template_name ] ) ) {
		return $template;
	}

	$custom_template = $custom_templates[ $template_name ];

	return file_exists( $custom_template ) ? $custom_template : $template;
}
add_filter( 'wc_get_template', 'muukal_astra_force_checkout_templates', 25, 2 );

/**
 * Force WooCommerce to include the custom account templates at render time.
 *
 * @param string $template Resolved template path.
 * @param string $template_name Requested template name.
 * @return string
 */
function muukal_astra_force_account_templates( $template, $template_name ) {
	$custom_templates = array(
		'myaccount/my-account.php' => ASTRA_THEME_DIR . 'woocommerce/myaccount/my-account.php',
		'myaccount/navigation.php' => ASTRA_THEME_DIR . 'woocommerce/myaccount/navigation.php',
		'myaccount/form-login.php' => ASTRA_THEME_DIR . 'woocommerce/myaccount/form-login.php',
	);

	if ( ! isset( $custom_templates[ $template_name ] ) ) {
		return $template;
	}

	$custom_template = $custom_templates[ $template_name ];

	return file_exists( $custom_template ) ? $custom_template : $template;
}
add_filter( 'wc_get_template', 'muukal_astra_force_account_templates', 20, 2 );

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

/**
 * Get a cache-busting asset version for theme files.
 *
 * @param string $relative_path Relative path inside the theme.
 * @return string
 */
function muukal_astra_asset_version( $relative_path ) {
	$absolute_path = ASTRA_THEME_DIR . ltrim( $relative_path, '/\\' );

	if ( file_exists( $absolute_path ) ) {
		$modified = filemtime( $absolute_path );

		if ( false !== $modified ) {
			return (string) $modified;
		}
	}

	return ASTRA_THEME_VERSION;
}

/**
 * Enqueue the custom Muukal checkout assets.
 */
function muukal_astra_enqueue_checkout_assets() {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) ) {
		return;
	}

	$style_path = 'assets/css/muukal-checkout.css';
	$script_path = 'assets/js/unminified/muukal-checkout.js';

	wp_enqueue_style(
		'astra-muukal-checkout',
		ASTRA_THEME_URI . $style_path,
		array(),
		muukal_astra_asset_version( $style_path )
	);

	wp_enqueue_script(
		'astra-muukal-checkout',
		ASTRA_THEME_URI . $script_path,
		array( 'jquery', 'wc-checkout' ),
		muukal_astra_asset_version( $script_path ),
		true
	);

	wp_localize_script(
		'astra-muukal-checkout',
		'muukalCheckoutConfig',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'muukal_checkout_shipping_option' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'muukal_astra_enqueue_checkout_assets', 40 );

/**
 * Remove the default checkout coupon toggle because the custom layout renders
 * its own inline coupon field.
 */
function muukal_astra_prepare_checkout_hooks() {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) ) {
		return;
	}

	remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
}
add_action( 'wp', 'muukal_astra_prepare_checkout_hooks' );

/**
 * Simplify the custom checkout address modal fields so they fit the desired
 * modal layout instead of WooCommerce's default long-form checkout styling.
 *
 * @param array<string, array<string, array<string, mixed>>> $fields Checkout fields.
 * @return array<string, array<string, array<string, mixed>>>
 */
function muukal_astra_customize_checkout_fields( $fields ) {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) ) {
		return $fields;
	}

	unset( $fields['billing']['billing_company'] );
	unset( $fields['order']['order_comments'] );

	$field_updates = array(
		'billing_first_name' => array(
			'placeholder' => __( 'First Name', 'astra' ),
			'class'       => array( 'form-row-first' ),
			'priority'    => 10,
		),
		'billing_last_name'  => array(
			'placeholder' => __( 'Last Name', 'astra' ),
			'class'       => array( 'form-row-last' ),
			'priority'    => 20,
		),
		'billing_country'    => array(
			'label'       => __( 'Country', 'astra' ),
			'placeholder' => __( 'Please Choose Country', 'astra' ),
			'class'       => array( 'form-row-wide' ),
			'priority'    => 30,
		),
		'billing_address_1'  => array(
			'label'       => __( 'Address Line 1', 'astra' ),
			'placeholder' => __( 'Address Line 1', 'astra' ),
			'class'       => array( 'form-row-wide' ),
			'priority'    => 40,
		),
		'billing_address_2'  => array(
			'label'       => __( 'Address Line 2', 'astra' ),
			'placeholder' => __( 'Address Line 2', 'astra' ),
			'class'       => array( 'form-row-wide' ),
			'priority'    => 50,
		),
		'billing_city'       => array(
			'label'       => __( 'City', 'astra' ),
			'placeholder' => __( 'City', 'astra' ),
			'class'       => array( 'form-row-first' ),
			'priority'    => 60,
		),
		'billing_postcode'   => array(
			'label'       => __( 'Post/Zip Code', 'astra' ),
			'placeholder' => __( 'Post/Zip Code', 'astra' ),
			'class'       => array( 'form-row-last' ),
			'priority'    => 70,
		),
		'billing_state'      => array(
			'label'       => __( 'State/Province', 'astra' ),
			'placeholder' => __( 'State/Province', 'astra' ),
			'class'       => array( 'form-row-wide' ),
			'priority'    => 80,
		),
		'billing_phone'      => array(
			'label'       => __( 'Phone Number', 'astra' ),
			'placeholder' => __( 'Phone Number', 'astra' ),
			'class'       => array( 'form-row-wide' ),
			'priority'    => 90,
		),
		'billing_email'      => array(
			'label'       => __( 'Email Address', 'astra' ),
			'placeholder' => __( 'Email Address', 'astra' ),
			'class'       => array( 'form-row-wide' ),
			'priority'    => 100,
		),
	);

	foreach ( $field_updates as $field_key => $updates ) {
		if ( ! isset( $fields['billing'][ $field_key ] ) || ! is_array( $fields['billing'][ $field_key ] ) ) {
			continue;
		}

		$fields['billing'][ $field_key ] = array_merge( $fields['billing'][ $field_key ], $updates );
	}

	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'muukal_astra_customize_checkout_fields', 20 );

/**
 * Start checkout country selects empty so the address modal can use its own
 * explicit country placeholder instead of the store base country.
 *
 * @param string $country Default checkout country.
 * @return string
 */
function muukal_astra_clear_default_checkout_country( $country ) {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) ) {
		return $country;
	}

	return '';
}
add_filter( 'default_checkout_billing_country', 'muukal_astra_clear_default_checkout_country' );
add_filter( 'default_checkout_shipping_country', 'muukal_astra_clear_default_checkout_country' );

/**
 * Get the first cart product ID to drive the custom shipping selector.
 *
 * @return int
 */
function muukal_astra_get_checkout_shipping_product_id() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return 0;
	}

	foreach ( WC()->cart->get_cart() as $cart_item ) {
		if ( empty( $cart_item['product_id'] ) ) {
			continue;
		}

		return (int) $cart_item['product_id'];
	}

	return 0;
}

/**
 * Get the default custom shipping option for the first cart product.
 *
 * @return array<string, string>
 */
function muukal_astra_get_checkout_default_shipping_option() {
	if ( ! function_exists( 'muukal_product_shipping_options_get_product_options' ) ) {
		return array();
	}

	$product_id = muukal_astra_get_checkout_shipping_product_id();
	if ( ! $product_id ) {
		return array();
	}

	$options = muukal_product_shipping_options_get_product_options( $product_id );
	if ( empty( $options ) || ! is_array( $options ) ) {
		return array();
	}

	foreach ( $options as $option ) {
		if ( ! empty( $option['is_default'] ) ) {
			return $option;
		}
	}

	return isset( $options[0] ) && is_array( $options[0] ) ? $options[0] : array();
}

/**
 * Whether the current cart should use the custom checkout shipping cards.
 *
 * @return bool
 */
function muukal_astra_checkout_has_custom_shipping_options() {
	return ! empty( muukal_astra_get_checkout_default_shipping_option() );
}

/**
 * Get the selected checkout shipping option, defaulting from the product config.
 *
 * @return array<string, string>
 */
function muukal_astra_get_checkout_shipping_selection() {
	$selection = array();

	if ( function_exists( 'WC' ) && WC()->session ) {
		$session_value = WC()->session->get( 'muukal_checkout_shipping_option', array() );
		$selection     = is_array( $session_value ) ? $session_value : array();
	}

	if ( ! empty( $selection['label'] ) ) {
		return array(
			'label' => sanitize_text_field( (string) $selection['label'] ),
			'price' => '' !== (string) ( $selection['price'] ?? '' ) ? wc_format_decimal( $selection['price'] ) : '0',
		);
	}

	$default_option = muukal_astra_get_checkout_default_shipping_option();
	if ( empty( $default_option ) ) {
		return array();
	}

	$selection = array(
		'label' => sanitize_text_field( (string) $default_option['label'] ),
		'price' => '' !== (string) ( $default_option['price'] ?? '' ) ? wc_format_decimal( $default_option['price'] ) : '0',
	);

	if ( function_exists( 'WC' ) && WC()->session ) {
		WC()->session->set( 'muukal_checkout_shipping_option', $selection );
	}

	return $selection;
}

/**
 * Render the custom shipping option cards on checkout.
 *
 * @return string
 */
function muukal_astra_render_checkout_shipping_options() {
	if ( ! muukal_astra_checkout_has_custom_shipping_options() ) {
		return '';
	}

	$product_id = muukal_astra_get_checkout_shipping_product_id();
	$selection  = muukal_astra_get_checkout_shipping_selection();

	if ( ! $product_id ) {
		return '';
	}

	return do_shortcode(
		sprintf(
			'[muukal_product_shipping_options product_id="%d" title="" selected_label="%s" class="muukal-checkout-shipping-selector"]',
			$product_id,
			esc_attr( isset( $selection['label'] ) ? $selection['label'] : '' )
		)
	);
}

/**
 * Persist the chosen custom shipping option in the WooCommerce session.
 */
function muukal_astra_set_checkout_shipping_option() {
	check_ajax_referer( 'muukal_checkout_shipping_option', 'nonce' );

	if ( ! function_exists( 'WC' ) || ! WC()->session || ! function_exists( 'muukal_product_shipping_options_get_product_options' ) ) {
		wp_send_json_error( array( 'message' => 'Shipping options are unavailable.' ), 400 );
	}

	$product_id = muukal_astra_get_checkout_shipping_product_id();
	$options    = $product_id ? muukal_product_shipping_options_get_product_options( $product_id ) : array();
	$label      = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : '';
	$match      = array();

	foreach ( $options as $option ) {
		if ( ! is_array( $option ) || empty( $option['label'] ) ) {
			continue;
		}

		if ( $label === sanitize_text_field( (string) $option['label'] ) ) {
			$match = $option;
			break;
		}
	}

	if ( empty( $match ) ) {
		wp_send_json_error( array( 'message' => 'Invalid shipping option.' ), 400 );
	}

	$selection = array(
		'label' => sanitize_text_field( (string) $match['label'] ),
		'price' => '' !== (string) ( $match['price'] ?? '' ) ? wc_format_decimal( $match['price'] ) : '0',
	);

	WC()->session->set( 'muukal_checkout_shipping_option', $selection );

	wp_send_json_success( array( 'selection' => $selection ) );
}
add_action( 'wp_ajax_muukal_astra_set_checkout_shipping_option', 'muukal_astra_set_checkout_shipping_option' );
add_action( 'wp_ajax_nopriv_muukal_astra_set_checkout_shipping_option', 'muukal_astra_set_checkout_shipping_option' );

/**
 * Replace native package rates with the custom checkout shipping card selection.
 *
 * @param array<int|string, WC_Shipping_Rate> $rates Shipping rates.
 * @return array<int|string, WC_Shipping_Rate>
 */
function muukal_astra_filter_checkout_package_rates( $rates ) {
	if ( ! muukal_astra_checkout_has_custom_shipping_options() ) {
		return $rates;
	}

	return array();
}
add_filter( 'woocommerce_package_rates', 'muukal_astra_filter_checkout_package_rates', 1000 );

/**
 * Add the selected custom shipping option as a cart fee so totals update normally.
 *
 * @param WC_Cart $cart Cart object.
 */
function muukal_astra_apply_checkout_shipping_fee( $cart ) {
	if ( ! $cart instanceof WC_Cart ) {
		return;
	}

	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}

	$selection = muukal_astra_get_checkout_shipping_selection();
	if ( empty( $selection['label'] ) ) {
		return;
	}

	$cart->add_fee( __( 'Shipping Fee', 'astra' ), (float) $selection['price'], false );
}
add_action( 'woocommerce_cart_calculate_fees', 'muukal_astra_apply_checkout_shipping_fee' );

/**
 * Store the chosen custom shipping option on the order for later reference.
 *
 * @param WC_Order $order Order object.
 */
function muukal_astra_store_checkout_shipping_on_order( $order ) {
	if ( ! $order instanceof WC_Order ) {
		return;
	}

	$selection = muukal_astra_get_checkout_shipping_selection();
	if ( empty( $selection['label'] ) ) {
		return;
	}

	$order->update_meta_data( '_muukal_shipping_option_label', $selection['label'] );
	$order->update_meta_data( '_muukal_shipping_option_price', $selection['price'] );
}
add_action( 'woocommerce_checkout_create_order', 'muukal_astra_store_checkout_shipping_on_order' );

/**
 * Build a compact summary payload for one checkout line item.
 *
 * @param array<string, mixed> $cart_item Cart item data.
 * @param string               $cart_item_key Cart item key.
 * @return array<string, mixed>
 */
function muukal_astra_get_checkout_item_summary( $cart_item, $cart_item_key ) {
	$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

	if ( ! $_product || ! $_product->exists() ) {
		return array();
	}

	$product_name  = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
	$product_image = $_product->get_image_id()
		? wp_get_attachment_image(
			$_product->get_image_id(),
			'full',
			false,
			array(
				'class'    => 'muukal-checkout-review__product-image',
				'alt'      => wp_strip_all_tags( (string) $product_name ),
				'loading'  => 'lazy',
				'decoding' => 'async',
			)
		)
		: wc_placeholder_img(
			'large',
			array(
				'class'    => 'muukal-checkout-review__product-image',
				'alt'      => wp_strip_all_tags( (string) $product_name ),
				'loading'  => 'lazy',
				'decoding' => 'async',
			)
		);
	$item_data_rows = apply_filters( 'woocommerce_get_item_data', array(), $cart_item );
	$frame_color    = '';
	$frame_size     = '';
	$lens_type      = '';
	$usage          = '';

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

		if ( 'Lens Type' === $label ) {
			$lens_type = $value;
			continue;
		}

		if ( 'Usage' === $label ) {
			$usage = $value;
		}
	}

	if ( '' === $lens_type ) {
		$lens_type = $usage;
	}

	return array(
		'name'         => wp_strip_all_tags( (string) $product_name ),
		'image_html'   => $product_image,
		'frame_color'  => $frame_color,
		'frame_size'   => $frame_size,
		'lens_type'    => $lens_type,
		'quantity'     => isset( $cart_item['quantity'] ) ? (int) $cart_item['quantity'] : 0,
		'subtotal_html'=> WC()->cart ? WC()->cart->get_product_subtotal( $_product, isset( $cart_item['quantity'] ) ? (int) $cart_item['quantity'] : 0 ) : wc_price( 0 ),
	);
}
