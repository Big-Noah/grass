<?php
/**
 * Custom My Account navigation.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$menu_items = wc_get_account_menu_items();
$groups     = function_exists( 'muukal_astra_account_nav_groups' ) ? muukal_astra_account_nav_groups() : array();

do_action( 'woocommerce_before_account_navigation' );
?>

<nav class="woocommerce-MyAccount-navigation muukal-account-dashboard-nav" aria-label="<?php esc_html_e( 'Account pages', 'woocommerce' ); ?>">
	<ul>
		<?php
		$rendered_groups = array();

		foreach ( $menu_items as $endpoint => $label ) :
			$group_label = isset( $groups[ $endpoint ] ) ? $groups[ $endpoint ] : '';

			if ( $group_label && ! in_array( $group_label, $rendered_groups, true ) ) :
				$rendered_groups[] = $group_label;
				?>
				<li class="muukal-account-nav-group"><?php echo esc_html( $group_label ); ?></li>
			<?php endif; ?>

			<li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" <?php echo wc_is_current_account_menu_item( $endpoint ) ? 'aria-current="page"' : ''; ?>>
					<?php echo esc_html( $label ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
