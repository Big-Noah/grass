<?php
/**
 * Muukal Astra overrides.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Disable Astra's automatic underline styling for content links site-wide.
 *
 * This prevents Astra from outputting the inline rule that underlines links in
 * single post content, comment content, and WooCommerce short descriptions.
 *
 * @return bool
 */
function muukal_disable_astra_content_link_underline() {
	return false;
}
add_filter( 'astra_get_option_underline-content-links', 'muukal_disable_astra_content_link_underline' );
