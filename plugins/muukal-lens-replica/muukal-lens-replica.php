<?php
/**
 * Plugin Name: Muukal Lens Replica
 * Description: Standalone Muukal lens-selector replica for testing, with PHP field schema and simulated add-to-cart payload export.
 * Version: 0.2.2
 * Author: Codex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MUUKAL_LENS_REPLICA_VERSION', '0.2.2' );
define( 'MUUKAL_LENS_REPLICA_DIR', plugin_dir_path( __FILE__ ) );
define( 'MUUKAL_LENS_REPLICA_URL', plugin_dir_url( __FILE__ ) );

function muukal_lens_replica_get_config() {
	static $config = null;

	if ( null === $config ) {
		$config = require MUUKAL_LENS_REPLICA_DIR . 'includes/config.php';
	}

	return $config;
}

/**
 * Merge runtime product context into the lens config.
 *
 * @return array
 */
function muukal_lens_replica_get_runtime_config( $atts = array() ) {
	$config  = muukal_lens_replica_get_config();
	$product = $config['product'];
	$atts    = wp_parse_args(
		is_array( $atts ) ? $atts : array(),
		array(
			'product_id'    => '',
			'color_id'      => '',
			'color_label'   => '',
			'frame_size'    => '',
			'measurements'  => '',
			'frame_price'   => '',
			'image_url'     => '',
			'button_label'  => '',
			'button_subtext'=> '',
			'button_class'  => '',
			'debug'         => '',
		)
	);

	if ( function_exists( 'wc_get_product' ) ) {
		$current_product = null;

		if ( ! empty( $atts['product_id'] ) ) {
			$current_product = wc_get_product( absint( $atts['product_id'] ) );
		} elseif ( isset( $GLOBALS['product'] ) && $GLOBALS['product'] instanceof WC_Product ) {
			$current_product = $GLOBALS['product'];
		} elseif ( get_the_ID() ) {
			$current_product = wc_get_product( get_the_ID() );
		}

		if ( $current_product instanceof WC_Product ) {
			$product['id']          = $current_product->get_id();
			$product['code']        = $current_product->get_sku() ? $current_product->get_sku() : $product['code'];
			$product['describe']    = $current_product->get_name();
			$product['name']        = $current_product->get_name();
			$product['frame_price'] = (float) ( $current_product->get_price() ? $current_product->get_price() : $product['frame_price'] );
			$product['image_url']   = $current_product->get_image_id() ? wp_get_attachment_image_url( $current_product->get_image_id(), 'woocommerce_single' ) : '';

			$size_terms = wc_get_product_terms(
				$current_product->get_id(),
				'pa_size',
				array(
					'fields' => 'names',
				)
			);

			if ( ! empty( $size_terms ) && ! is_wp_error( $size_terms ) ) {
				$product['size'] = (string) $size_terms[0];
			}
		}
	}

	if ( '' !== (string) $atts['color_id'] ) {
		$product['color_id'] = (string) $atts['color_id'];
	}

	if ( '' !== (string) $atts['color_label'] ) {
		$product['color_label'] = sanitize_text_field( (string) $atts['color_label'] );
	}

	if ( '' !== (string) $atts['frame_size'] ) {
		$product['size'] = sanitize_text_field( (string) $atts['frame_size'] );
	}

	if ( '' !== (string) $atts['measurements'] ) {
		$product['measurements'] = sanitize_text_field( (string) $atts['measurements'] );
	}

	if ( '' !== (string) $atts['frame_price'] ) {
		$product['frame_price'] = (float) $atts['frame_price'];
	}

	if ( '' !== (string) $atts['image_url'] ) {
		$product['image_url'] = esc_url_raw( (string) $atts['image_url'] );
	}

	$config['ui']['open_button_label']   = '' !== (string) $atts['button_label'] ? sanitize_text_field( (string) $atts['button_label'] ) : 'SELECT LENSES';
	$config['ui']['open_button_subtext'] = '' !== (string) $atts['button_subtext'] ? sanitize_text_field( (string) $atts['button_subtext'] ) : 'or Non-prescription';
	$config['ui']['button_class']        = '' !== (string) $atts['button_class'] ? sanitize_text_field( (string) $atts['button_class'] ) : 'btn theme-btn-b f-right go-select-lenses';
	$config['ui']['debug']               = ! empty( $atts['debug'] );

	$config['product'] = $product;

	return $config;
}

function muukal_lens_replica_register_assets() {
	wp_register_style( 'muukal-lens-replica', MUUKAL_LENS_REPLICA_URL . 'assets/muukal-lens-replica.css', array(), MUUKAL_LENS_REPLICA_VERSION );
	wp_register_script( 'muukal-lens-replica', MUUKAL_LENS_REPLICA_URL . 'assets/muukal-lens-replica.js', array(), MUUKAL_LENS_REPLICA_VERSION, true );
}
add_action( 'init', 'muukal_lens_replica_register_assets' );

function muukal_lens_replica_enqueue_assets( $runtime_config = null ) {
	$runtime_config = is_array( $runtime_config ) ? $runtime_config : muukal_lens_replica_get_runtime_config();

	wp_enqueue_style( 'muukal-lens-replica' );
	wp_enqueue_script( 'muukal-lens-replica' );
	wp_localize_script(
		'muukal-lens-replica',
		'muukalLensReplicaConfig',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'muukal_lens_replica_build_payload' ),
			'config'  => $runtime_config,
		)
	);
}

function muukal_lens_replica_shortcode( $atts = array() ) {
	$atts    = shortcode_atts(
		array(
			'product_id'     => '',
			'color_id'       => '',
			'color_label'    => '',
			'frame_size'     => '',
			'measurements'   => '',
			'frame_price'    => '',
			'image_url'      => '',
			'button_label'   => '',
			'button_subtext' => '',
			'button_class'   => '',
			'debug'          => '',
		),
		$atts,
		'muukal_lens_replica'
	);
	$config  = muukal_lens_replica_get_runtime_config( $atts );
	muukal_lens_replica_enqueue_assets( $config );
	$product = $config['product'];
	$image   = ! empty( $product['image_url'] ) ? esc_url( $product['image_url'] ) : '';

	ob_start();
	?>
	<div class="mlr-app" data-product-id="<?php echo esc_attr( $product['id'] ); ?>" data-color-id="<?php echo esc_attr( $product['color_id'] ); ?>" data-color-label="<?php echo esc_attr( $product['color_label'] ); ?>">
		<button type="button" id="hadStock" class="<?php echo esc_attr( $config['ui']['button_class'] ); ?>">
			<?php echo esc_html( $config['ui']['open_button_label'] ); ?>
			<div class="fs14 mt5"><?php echo esc_html( $config['ui']['open_button_subtext'] ); ?></div>
		</button>
		<section id="lens_container" class="container-fluid" hidden>
			<div id="lens_mask" data-close="1"></div>
			<div id="lens_box" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( $config['ui']['drawer_title'] ); ?>">
				<div id="lens_left_close" data-close="1" aria-label="Close"><i class="icon dripicons-cross"></i></div>
				<div class="container pb-60 ncpd1200_max_w">
					<div class="row">
						<div id="lensbox_left" class="col-12 col-xl-9">
							<div id="lens_step_box" class="mt-20">
								<div class="accordion" id="accordionExample">
									<div id="step_1_card" class="card">
										<div class="card-header">
											<div class="mlr-step-toggle" data-step="1">
												Step 1. &nbsp;WHAT DO YOU USE YOUR GLASSES FOR?
												<span id="step_1_cn" class="f-right mr-50 step_cn"></span>
											</div>
										</div>
										<div id="step1_div_box" class="collapse show">
											<div class="card-body">
												<ul class="row mlr-step-options" data-step-list="1"></ul>
											</div>
										</div>
									</div>
									<div id="step_2_card" class="card mt-10">
										<div class="card-header">
											<div class="mlr-step-toggle" data-step="2">
												Step 2. &nbsp;ENTER YOUR PRESCRIPTION
												<span id="step_2_cn" class="f-right mr-50 step_cn"></span>
											</div>
										</div>
										<div id="step2_div_box" class="collapse">
											<div class="card-body" id="power_box" style="display:none;"></div>
											<div class="card-body" id="prescription_box"></div>
										</div>
									</div>
									<div id="step_3_card" class="card mt-10">
										<div class="card-header">
											<div class="mlr-step-toggle" data-step="3">
												Step 3. &nbsp;LENS TYPE - CHOOSE ONE OPTION
												<span id="step_3_cn" class="f-right mr-50 step_cn"></span>
											</div>
										</div>
										<div id="step3_div_box" class="collapse">
											<div class="card-body">
												<ul class="row mlr-step-options" data-step-list="3"></ul>
											</div>
										</div>
									</div>
									<div id="step_4_card" class="card mt-10">
										<div class="card-header">
											<div class="mlr-step-toggle" data-step="4">
												Step 4. &nbsp;SELECT LENSES THICKNESS
												<span id="step_4_cn" class="f-right mr-50 step_cn"></span>
											</div>
										</div>
										<div id="step4_div_box" class="collapse">
											<div class="card-body">
												<ul class="row mlr-step-options" data-step-list="4"></ul>
											</div>
										</div>
									</div>
									<div id="step_5_card" class="card mt-10">
										<div class="card-header">
											<div class="mlr-step-toggle" data-step="5">
												Step 5. &nbsp;SELECT LENSES COATING
												<span id="step_5_cn" class="f-right mr-50 step_cn"></span>
											</div>
										</div>
										<div id="step5_div_box" class="collapse">
											<div class="card-body">
												<ul class="row mlr-step-options" data-step-list="5"></ul>
											</div>
										</div>
									</div>
								</div>
								<p class="mlr-status" aria-live="polite"></p>
							</div>
						</div>
						<div id="lensbox_right" class="col-12 col-xl-3 animated">
							<div id="lens_goods_box" class="mt-20 borderd7">
								<div class="lens_goods_info">
									<div id="lens_img_v_box">
										<?php if ( $image ) : ?>
											<img id="lens_img_v" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $product['describe'] ); ?>">
										<?php else : ?>
											<div id="lens_img_v" aria-hidden="true"><?php echo esc_html( substr( $product['describe'], 0, 1 ) ); ?></div>
										<?php endif; ?>
									</div>
									<div class="mlr-divider"></div>
									<div class="mt-10 pb-10 ml-10 mr-10">
										<div class="bluelight-btn bluelight-btn-full">
											<div class="inline-left ml10">
												<div class="inline-left mt5 mlr-badge-icon">B</div>
												<div class="inline-left ml15 fs18">
													<div>
														Add Blue Light Blocking
														<span class="mlr-help">?</span>
													</div>
													<div class="lens_k_price fs14"><del>$23.95</del>&nbsp;&nbsp;<span class="fs18">+$9.75</span></div>
													<div class="bluelight_tips">Shield your eyes from harmful blue light.</div>
													<div class="lock_tips" style="display:none;">This option is not available</div>
												</div>
												<div class="inline-left fs18"><div class="bluelight-add">ADD</div></div>
											</div>
											<div class="clear"></div>
										</div>
										<div id="lens-add-cart" class="btn theme-btn-b">ADD TO CART</div>
										<div class="mt-10 text-center mk-blue fs16" id="edit-again" style="display:none;">Back to Edit Lenses</div>
										<?php if ( ! empty( $config['ui']['debug'] ) ) : ?>
											<div class="mlr-payload-card">
												<div class="mlr-payload-head">
													<strong>Payload Preview</strong>
													<button type="button" class="mlr-copy-payload">Copy</button>
												</div>
												<pre class="mlr-payload-preview">Waiting for simulation...</pre>
											</div>
										<?php endif; ?>
									</div>
									<div class="mt-10 ml-20 lens-line borderb-d7">
										<div class="lens_goods_title" id="lens_goods_title"><?php echo esc_html( $product['describe'] ); ?></div>
									</div>
									<div class="ml-20 mt-10 lens-line"><span class="lens_lab">Frame Size:</span> <span id="lens_size_v"><?php echo esc_html( $product['size'] ); ?></span>&nbsp;&nbsp;<span id="lens_measurements_v"><?php echo esc_html( $product['measurements'] ); ?></span></div>
									<div class="ml-20 lens-line"><span class="lens_lab">Frame Color:</span> <span id="lens_color_v"><?php echo esc_html( $product['color_label'] ); ?></span></div>
									<div class="ml-20 lens-line mr-20"><span class="lens_lab">Frame Price:</span> <span class="mk-price f-right">$<span id="frame_price"><?php echo esc_html( number_format( (float) $product['frame_price'], 2, '.', '' ) ); ?></span></span></div>
									<div class="ml-20 pb-10 borderb-d7"></div>
								</div>
								<div class="mt-20 ml-20 mr-20">
									<div id="lens_deta_info" class="mt-20">
										<div class="lens-line" id="data_attr_1"><span class="lens_lab">Usage:</span><span id="deta_usage"></span><span class="f-right" id="deta_usage_p"></span></div>
										<div class="lens-line" id="data_attr_2" style="display:none;"><span class="lens_lab">Lens Prism:</span><span id="deta_lenst_prism">NONE</span><span class="f-right" id="deta_lenst_prism_p"></span></div>
										<div class="lens-line" id="data_attr_3" style="display:none;"><span class="lens_lab">Lens Type:</span><span id="deta_lenst_type"></span><span class="f-right" id="deta_lenst_type_p"></span></div>
										<div class="lens-line" id="data_attr_4" style="display:none;"><span class="lens_lab">Lens Index:</span><span id="deta_lenst_index"></span><span class="f-right" id="deta_lenst_index_p"></span></div>
										<div class="lens-line" id="data_attr_5" style="display:none;"><span class="lens_lab">Lens Coating:</span><span id="deta_lenst_coatinc"></span><span class="f-right" id="deta_lenst_coatinc_p"></span></div>
										<div class="lens-line" id="data_attr_6"><span class="lens_lab" style="width:135px;">Blue-light Blocking:</span><span id="deta_lenst_bluelight">NO</span><span class="f-right" id="deta_lenst_bluelight_p"></span></div>
									</div>
									<div class="lens-line"><span class="lens_lab">Lens Price:</span><span class="mk-price f-right">$<span id="lens_price">0.00</span></span></div>
									<div class="fs16 lens-line"><span class="lens_lab">TOTAL:</span><span class="mk-price fs22 f-right">$<span id="total"><?php echo esc_html( number_format( (float) $product['frame_price'], 2, '.', '' ) ); ?></span></span></div>
								</div>
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
			</div>
		</section>
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

	if ( ! empty( $state['product'] ) && is_array( $state['product'] ) ) {
		if ( isset( $state['product']['id'] ) ) {
			$product['id'] = (int) $state['product']['id'];
		}

		if ( isset( $state['product']['color_id'] ) ) {
			$product['color_id'] = (string) $state['product']['color_id'];
		}

		if ( isset( $state['product']['frame_price'] ) ) {
			$product['frame_price'] = (float) $state['product']['frame_price'];
		}
	}

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
			'message'        => 'Lens payload generated.',
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
