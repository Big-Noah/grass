<?php
/**
 * Plugin Name: Muukal Lens Replica
 * Description: Standalone Muukal lens-selector replica for testing, with PHP field schema and simulated add-to-cart payload export.
 * Version: 0.1.0
 * Author: Codex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MUUKAL_LENS_REPLICA_VERSION', '0.1.0' );
define( 'MUUKAL_LENS_REPLICA_DIR', plugin_dir_path( __FILE__ ) );
define( 'MUUKAL_LENS_REPLICA_URL', plugin_dir_url( __FILE__ ) );

function muukal_lens_replica_get_config() {
	static $config = null;

	if ( null === $config ) {
		$config = require MUUKAL_LENS_REPLICA_DIR . 'includes/config.php';
	}

	return $config;
}

function muukal_lens_replica_register_assets() {
	wp_register_style( 'muukal-lens-replica', MUUKAL_LENS_REPLICA_URL . 'assets/muukal-lens-replica.css', array(), MUUKAL_LENS_REPLICA_VERSION );
	wp_register_script( 'muukal-lens-replica', MUUKAL_LENS_REPLICA_URL . 'assets/muukal-lens-replica.js', array(), MUUKAL_LENS_REPLICA_VERSION, true );
}
add_action( 'init', 'muukal_lens_replica_register_assets' );

function muukal_lens_replica_enqueue_assets() {
	wp_enqueue_style( 'muukal-lens-replica' );
	wp_enqueue_script( 'muukal-lens-replica' );
	wp_localize_script(
		'muukal-lens-replica',
		'muukalLensReplicaConfig',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'muukal_lens_replica_build_payload' ),
			'config'  => muukal_lens_replica_get_config(),
		)
	);
}

function muukal_lens_replica_shortcode() {
	muukal_lens_replica_enqueue_assets();
	$config  = muukal_lens_replica_get_config();
	$product = $config['product'];

	ob_start();
	?>
	<div class="mlr-app" data-product-id="<?php echo esc_attr( $product['id'] ); ?>">
		<button type="button" class="mlr-open"><?php echo esc_html( $config['ui']['open_button_label'] ); ?></button>
		<div class="mlr-overlay" hidden>
			<div class="mlr-backdrop" data-close="1"></div>
			<div class="mlr-drawer" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( $config['ui']['drawer_title'] ); ?>">
				<div class="mlr-drawer-head">
					<div>
						<p class="mlr-kicker"><?php echo esc_html( $config['ui']['drawer_kicker'] ); ?></p>
						<h2><?php echo esc_html( $config['ui']['drawer_title'] ); ?></h2>
					</div>
					<button type="button" class="mlr-close" data-close="1" aria-label="Close">&times;</button>
				</div>
				<div class="mlr-drawer-body">
					<div class="mlr-stage">
						<div class="mlr-product-card">
							<div class="mlr-product-meta">
								<p class="mlr-product-brand"><?php echo esc_html( $product['describe'] ); ?></p>
								<h3><?php echo esc_html( $product['name'] ); ?></h3>
								<p class="mlr-product-copy"><?php echo esc_html( $config['ui']['product_copy'] ); ?></p>
							</div>
							<ul class="mlr-product-facts">
								<li><span>Frame Code</span><strong><?php echo esc_html( $product['code'] ); ?></strong></li>
								<li><span>Color</span><strong><?php echo esc_html( $product['color_label'] ); ?></strong></li>
								<li><span>Size</span><strong><?php echo esc_html( $product['size'] . ' / ' . $product['measurements'] ); ?></strong></li>
							</ul>
						</div>
						<div class="mlr-steps"></div>
					</div>
					<aside class="mlr-summary">
						<div class="mlr-summary-card">
							<h3>Selection Summary</h3>
							<div class="mlr-summary-lines"></div>
							<div class="mlr-addon"></div>
							<div class="mlr-totals"></div>
							<div class="mlr-actions"><button type="button" class="mlr-submit">Simulate Add To Cart</button></div>
							<p class="mlr-status" aria-live="polite"></p>
						</div>
						<div class="mlr-payload-card">
							<div class="mlr-payload-head">
								<h3>Payload Preview</h3>
								<button type="button" class="mlr-copy-payload">Copy</button>
							</div>
							<pre class="mlr-payload-preview">Waiting for simulation...</pre>
						</div>
					</aside>
				</div>
				<div class="mlr-upgrade-modal" hidden>
					<div class="mlr-upgrade-backdrop"></div>
					<div class="mlr-upgrade-dialog">
						<h3>Upgrade to Premium Progressive?</h3>
						<p class="mlr-upgrade-copy"></p>
						<div class="mlr-upgrade-actions">
							<button type="button" class="mlr-upgrade-skip">Keep Standard Progressive</button>
							<button type="button" class="mlr-upgrade-accept">Upgrade</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php

	return ob_get_clean();
}
add_shortcode( 'muukal_lens_replica', 'muukal_lens_replica_shortcode' );

function muukal_lens_replica_menu() {
	add_menu_page( 'Muukal Lens Replica', 'Lens Replica', 'manage_options', 'muukal-lens-replica', 'muukal_lens_replica_render_preview_page', 'dashicons-visibility', 60 );
	add_submenu_page( 'muukal-lens-replica', 'Lens Replica Preview', 'Preview', 'manage_options', 'muukal-lens-replica', 'muukal_lens_replica_render_preview_page' );
	add_submenu_page( 'muukal-lens-replica', 'Lens Replica Schema', 'Schema', 'manage_options', 'muukal-lens-replica-schema', 'muukal_lens_replica_render_schema_page' );
}
add_action( 'admin_menu', 'muukal_lens_replica_menu' );

function muukal_lens_replica_admin_assets( $hook ) {
	if ( false === strpos( $hook, 'muukal-lens-replica' ) ) {
		return;
	}

	muukal_lens_replica_enqueue_assets();
}
add_action( 'admin_enqueue_scripts', 'muukal_lens_replica_admin_assets' );

function muukal_lens_replica_render_preview_page() {
	echo '<div class="wrap"><h1>Muukal Lens Replica</h1><p>Test the crawled lens flow without writing to WooCommerce cart or database. Use shortcode <code>[muukal_lens_replica]</code> on front-end pages if needed.</p>' . do_shortcode( '[muukal_lens_replica]' ) . '</div>';
}

function muukal_lens_replica_render_schema_page() {
	$config = muukal_lens_replica_get_config();

	echo '<div class="wrap">';
	echo '<h1>Muukal Lens Replica Schema</h1>';
	echo '<p>The plugin reads its field definitions from <code>plugins/muukal-lens-replica/includes/config.php</code>. Nothing here writes into WooCommerce tables.</p>';
	echo '<h2>Payload Fields</h2><pre style="white-space:pre-wrap;">' . esc_html( var_export( $config['payload_fields'], true ) ) . '</pre>';
	echo '<h2>Dependency Rules</h2><pre style="white-space:pre-wrap;">' . esc_html( var_export( $config['dependency_rules'], true ) ) . '</pre>';
	echo '<h2>Prescription Fields</h2><pre style="white-space:pre-wrap;">' . esc_html( var_export( $config['prescription_fields'], true ) ) . '</pre>';
	echo '</div>';
}

function muukal_lens_replica_build_payload() {
	check_ajax_referer( 'muukal_lens_replica_build_payload', 'nonce' );

	$raw_state = isset( $_POST['state'] ) ? wp_unslash( $_POST['state'] ) : '';
	$state     = json_decode( $raw_state, true );
	$config    = muukal_lens_replica_get_config();

	if ( ! is_array( $state ) ) {
		wp_send_json_error( array( 'message' => 'Invalid state payload.' ), 400 );
	}

	$product = $config['product'];
	$form    = isset( $state['form'] ) && is_array( $state['form'] ) ? $state['form'] : array();

	$payload = array(
		'goodsid'        => (int) $product['id'],
		'color'          => (string) $product['color_id'],
		'login_key'      => 'plugin-test',
		'usage'          => isset( $state['usage'] ) ? (int) $state['usage'] : 0,
		'lenstype'       => isset( $state['lenstype'] ) ? (int) $state['lenstype'] : 0,
		'lenstype_color' => isset( $state['lenstype_color'] ) ? (int) $state['lenstype_color'] : 0,
		'lensindex'      => isset( $state['lensindex'] ) ? (int) $state['lensindex'] : 0,
		'coating'        => isset( $state['coating'] ) ? (int) $state['coating'] : 0,
		'pdkey'          => isset( $state['pdkey'] ) ? (int) $state['pdkey'] : 1,
		'nearpd'         => isset( $state['nearpd'] ) ? (int) $state['nearpd'] : 0,
		'prism'          => isset( $state['prism'] ) ? (int) $state['prism'] : 0,
		'rxkey'          => 1,
		'rximg'          => '',
		'od_sph'         => isset( $form['od_sph'] ) ? (string) $form['od_sph'] : '0',
		'os_sph'         => isset( $form['os_sph'] ) ? (string) $form['os_sph'] : '0',
		'od_cyl'         => isset( $form['od_cyl'] ) ? (string) $form['od_cyl'] : '0',
		'os_cyl'         => isset( $form['os_cyl'] ) ? (string) $form['os_cyl'] : '0',
		'od_axis'        => isset( $form['od_axis'] ) ? (string) $form['od_axis'] : '',
		'os_axis'        => isset( $form['os_axis'] ) ? (string) $form['os_axis'] : '',
		'od_add'         => isset( $form['od_add'] ) ? (string) $form['od_add'] : '0',
		'os_add'         => isset( $form['os_add'] ) ? (string) $form['os_add'] : '0',
		'pd'             => isset( $form['pd'] ) ? (string) $form['pd'] : '',
		'od_pd'          => isset( $form['od_pd'] ) ? (string) $form['od_pd'] : '0',
		'os_pd'          => isset( $form['os_pd'] ) ? (string) $form['os_pd'] : '0',
		'npd'            => isset( $form['npd'] ) ? (string) $form['npd'] : '',
		'birth_year'     => isset( $form['birth_year'] ) ? (string) $form['birth_year'] : '0',
		'od_prismnum_v'  => isset( $form['od_prismnum_v'] ) ? (string) $form['od_prismnum_v'] : '0',
		'os_prismnum_v'  => isset( $form['os_prismnum_v'] ) ? (string) $form['os_prismnum_v'] : '0',
		'od_prismdir_v'  => isset( $form['od_prismdir_v'] ) ? (string) $form['od_prismdir_v'] : '0',
		'os_prismdir_v'  => isset( $form['os_prismdir_v'] ) ? (string) $form['os_prismdir_v'] : '0',
		'od_prismnum_h'  => isset( $form['od_prismnum_h'] ) ? (string) $form['od_prismnum_h'] : '0',
		'os_prismnum_h'  => isset( $form['os_prismnum_h'] ) ? (string) $form['os_prismnum_h'] : '0',
		'od_prismdir_h'  => isset( $form['od_prismdir_h'] ) ? (string) $form['od_prismdir_h'] : '0',
		'os_prismdir_h'  => isset( $form['os_prismdir_h'] ) ? (string) $form['os_prismdir_h'] : '0',
		'lens_comment'   => isset( $form['lens_comment'] ) ? sanitize_textarea_field( $form['lens_comment'] ) : '',
		'rx_name'        => isset( $form['rx_name'] ) ? sanitize_text_field( $form['rx_name'] ) : '',
		'bluelight'      => ! empty( $state['bluelight'] ) ? 1 : 0,
		'editlens'       => 0,
		'cartid'         => '',
		'lensid'         => '',
		'cartprice'      => isset( $state['total'] ) ? (float) $state['total'] : (float) $product['frame_price'],
		'readers'        => isset( $state['readers'] ) ? (int) $state['readers'] : 0,
		'power'          => isset( $state['power'] ) ? (string) $state['power'] : '0',
	);

	wp_send_json_success(
		array(
			'message'        => 'Simulated add-to-cart payload generated.',
			'payload'        => $payload,
			'payload_fields' => $config['payload_fields'],
			'response_mock'  => array(
				'code'    => 1,
				'info'    => 'Simulated success',
				'eventID' => wp_generate_uuid4(),
			),
		)
	);
}
add_action( 'wp_ajax_muukal_lens_replica_build_payload', 'muukal_lens_replica_build_payload' );
add_action( 'wp_ajax_nopriv_muukal_lens_replica_build_payload', 'muukal_lens_replica_build_payload' );
