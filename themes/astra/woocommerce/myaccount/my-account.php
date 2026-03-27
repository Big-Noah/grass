<?php
/**
 * Custom My Account page shell.
 *
 * @package Astra
 */

defined( 'ABSPATH' ) || exit;

$content_title = function_exists( 'muukal_astra_get_account_content_title' ) ? muukal_astra_get_account_content_title() : __( 'My Account', 'astra' );
?>

<div class="muukal-account-dashboard">
	<?php do_action( 'woocommerce_account_navigation' ); ?>

	<div class="woocommerce-MyAccount-content muukal-account-dashboard-content">
		<h2 class="muukal-account-content-title"><?php echo esc_html( $content_title ); ?></h2>
		<?php do_action( 'woocommerce_account_content' ); ?>
	</div>
</div>
