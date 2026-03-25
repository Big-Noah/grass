<?php
/**
 * Plugin Name: Face++ Virtual Try On
 * Description: Independent virtual try-on plugin using Face++ eye landmarks for glasses alignment.
 * Version: 1.0.0
 * Author: Codex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FACEPP_TRYON_VERSION', '1.0.0' );
define( 'FACEPP_TRYON_FILE', __FILE__ );
define( 'FACEPP_TRYON_DIR', plugin_dir_path( __FILE__ ) );
define( 'FACEPP_TRYON_URL', plugin_dir_url( __FILE__ ) );

function facepp_tryon_defaults() {
	return array(
		'api_key'      => 'xWz1g1fxB0aEYffwQphC5UOF1C3QqIee',
		'api_secret'   => '77LQsA8iG0QCaQy6PTHT-JUbqq2U-3Uq',
		'api_endpoint' => 'https://api-us.faceplusplus.com/facepp/v3/detect',
		'button_label' => 'Try On',
		'modal_title'  => 'Virtual Try On',
		'helper_text'  => 'Upload a front-face portrait, run auto align, then fine-tune if needed.',
		'frame_1_name' => 'Frame 1',
		'frame_1_url'  => '',
		'frame_1_wf'   => '2.15',
		'frame_1_yo'   => '0.02',
		'frame_2_name' => 'Frame 2',
		'frame_2_url'  => '',
		'frame_2_wf'   => '2.15',
		'frame_2_yo'   => '0.02',
		'frame_3_name' => 'Frame 3',
		'frame_3_url'  => '',
		'frame_3_wf'   => '2.15',
		'frame_3_yo'   => '0.02',
		'model_1_name' => 'Model 1',
		'model_1_url'  => '',
		'model_2_name' => 'Model 2',
		'model_2_url'  => '',
		'model_3_name' => 'Model 3',
		'model_3_url'  => '',
		'model_4_name' => 'Model 4',
		'model_4_url'  => '',
	);
}

function facepp_tryon_get_settings() {
	return wp_parse_args( get_option( 'facepp_tryon_settings', array() ), facepp_tryon_defaults() );
}

function facepp_tryon_get_frames() {
	$settings = facepp_tryon_get_settings();
	$frames   = array();

	for ( $i = 1; $i <= 3; $i++ ) {
		$url = isset( $settings[ 'frame_' . $i . '_url' ] ) ? trim( (string) $settings[ 'frame_' . $i . '_url' ] ) : '';
		if ( '' === $url ) {
			continue;
		}

		$frames[] = array(
			'name'        => isset( $settings[ 'frame_' . $i . '_name' ] ) ? sanitize_text_field( (string) $settings[ 'frame_' . $i . '_name' ] ) : 'Frame ' . $i,
			'url'         => esc_url_raw( $url ),
			'widthFactor' => isset( $settings[ 'frame_' . $i . '_wf' ] ) ? (float) $settings[ 'frame_' . $i . '_wf' ] : 2.15,
			'yOffset'     => isset( $settings[ 'frame_' . $i . '_yo' ] ) ? (float) $settings[ 'frame_' . $i . '_yo' ] : 0.02,
		);
	}

	return $frames;
}

function facepp_tryon_get_models() {
	$settings = facepp_tryon_get_settings();
	$models   = array();

	for ( $i = 1; $i <= 4; $i++ ) {
		$url = isset( $settings[ 'model_' . $i . '_url' ] ) ? trim( (string) $settings[ 'model_' . $i . '_url' ] ) : '';
		if ( '' === $url ) {
			continue;
		}

		$models[] = array(
			'name' => isset( $settings[ 'model_' . $i . '_name' ] ) ? sanitize_text_field( (string) $settings[ 'model_' . $i . '_name' ] ) : 'Model ' . $i,
			'url'  => esc_url_raw( $url ),
		);
	}

	return $models;
}

function facepp_tryon_sanitize_settings( $input ) {
	$defaults = facepp_tryon_defaults();
	$output   = array();

	foreach ( $defaults as $key => $default ) {
		$value = isset( $input[ $key ] ) ? $input[ $key ] : $default;
		if ( false !== strpos( $key, '_url' ) ) {
			$output[ $key ] = esc_url_raw( trim( (string) $value ) );
			continue;
		}
		if ( in_array( $key, array( 'api_key', 'api_secret' ), true ) ) {
			$output[ $key ] = sanitize_text_field( (string) $value );
			continue;
		}
		if ( 'api_endpoint' === $key ) {
			$output[ $key ] = esc_url_raw( trim( (string) $value ) );
			continue;
		}
		if ( false !== strpos( $key, '_wf' ) || false !== strpos( $key, '_yo' ) ) {
			$output[ $key ] = (string) (float) $value;
			continue;
		}
		$output[ $key ] = sanitize_text_field( (string) $value );
	}

	return $output;
}

function facepp_tryon_register_settings() {
	register_setting(
		'facepp_tryon_settings_group',
		'facepp_tryon_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'facepp_tryon_sanitize_settings',
			'default'           => facepp_tryon_defaults(),
		)
	);
}
add_action( 'admin_init', 'facepp_tryon_register_settings' );

function facepp_tryon_admin_menu() {
	add_menu_page(
		'Face++ Try On',
		'Face++ Try On',
		'manage_options',
		'facepp-tryon',
		'facepp_tryon_render_settings_page',
		'dashicons-visibility',
		59
	);

	add_submenu_page(
		'facepp-tryon',
		'Face++ Try On Preview',
		'Preview',
		'manage_options',
		'facepp-tryon-preview',
		'facepp_tryon_render_preview_page'
	);
}
add_action( 'admin_menu', 'facepp_tryon_admin_menu' );

function facepp_tryon_admin_assets( $hook ) {
	if ( false === strpos( $hook, 'facepp-tryon' ) ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'facepp-tryon-admin', FACEPP_TRYON_URL . 'assets/admin.js', array( 'jquery' ), FACEPP_TRYON_VERSION, true );

	if ( false !== strpos( $hook, 'facepp-tryon-preview' ) ) {
		facepp_tryon_enqueue_assets();
	}
}
add_action( 'admin_enqueue_scripts', 'facepp_tryon_admin_assets' );

function facepp_tryon_register_assets() {
	wp_register_style( 'facepp-tryon-style', FACEPP_TRYON_URL . 'assets/app.css', array(), FACEPP_TRYON_VERSION );
	wp_register_script( 'facepp-tryon-script', FACEPP_TRYON_URL . 'assets/app.js', array(), FACEPP_TRYON_VERSION, true );
}
add_action( 'init', 'facepp_tryon_register_assets' );

function facepp_tryon_enqueue_assets() {
	$settings = facepp_tryon_get_settings();
	$config   = array(
		'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
		'ajaxNonce'  => wp_create_nonce( 'facepp_tryon_detect' ),
		'frames'     => facepp_tryon_get_frames(),
		'testModels' => facepp_tryon_get_models(),
		'modalTitle' => $settings['modal_title'],
		'i18n'       => array(
			'noPhoto'        => 'Upload a photo first.',
			'noFrame'        => 'Select a frame first.',
			'detecting'      => 'Detecting eyes and aligning frame...',
			'aligned'        => 'Auto align complete.',
			'detectFailed'   => 'Face detection failed. You can drag and adjust manually.',
			'noFace'         => 'No face detected. Please use a clearer front-facing portrait.',
			'missingCreds'   => 'Face++ key/secret is missing in plugin settings.',
			'photoReady'     => 'Photo loaded. Choose frame and click Auto Align.',
			'modelReady'     => 'Model loaded. Choose frame and click Auto Align.',
			'encodeFailed'   => 'This image cannot be sent to Face++ (cross-origin blocked). Use local upload or WordPress Media image.',
			'frameSelected'  => 'Frame selected.',
			'manualReset'    => 'Manual adjustments reset.',
		),
	);

	wp_enqueue_style( 'facepp-tryon-style' );
	wp_enqueue_script( 'facepp-tryon-script' );
	wp_localize_script( 'facepp-tryon-script', 'faceppTryonConfig', $config );
}

function facepp_tryon_ajax_detect() {
	check_ajax_referer( 'facepp_tryon_detect', 'nonce' );

	$settings   = facepp_tryon_get_settings();
	$api_key    = isset( $settings['api_key'] ) ? trim( (string) $settings['api_key'] ) : '';
	$api_secret = isset( $settings['api_secret'] ) ? trim( (string) $settings['api_secret'] ) : '';
	$endpoint   = isset( $settings['api_endpoint'] ) ? trim( (string) $settings['api_endpoint'] ) : '';
	$image_data = isset( $_POST['image'] ) ? (string) wp_unslash( $_POST['image'] ) : '';

	if ( '' === $endpoint ) {
		$endpoint = 'https://api-us.faceplusplus.com/facepp/v3/detect';
	}

	if ( '' === $api_key || '' === $api_secret ) {
		wp_send_json_error(
			array(
				'code'    => 'missing_credentials',
				'message' => 'Missing Face++ credentials.',
			),
			400
		);
	}

	if ( ! preg_match( '/^data:image\/(?:png|jpe?g|webp);base64,/', $image_data ) ) {
		wp_send_json_error(
			array(
				'code'    => 'invalid_image',
				'message' => 'Invalid image payload.',
			),
			400
		);
	}

	$payload = array(
		'api_key'         => $api_key,
		'api_secret'      => $api_secret,
		'image_base64'    => preg_replace( '/^data:image\/(?:png|jpe?g|webp);base64,/', '', $image_data ),
		'return_landmark' => '1',
	);

	$response = wp_remote_post(
		$endpoint,
		array(
			'timeout' => 20,
			'body'    => $payload,
		)
	);

	if ( is_wp_error( $response ) ) {
		wp_send_json_error(
			array(
				'code'    => 'http_error',
				'message' => $response->get_error_message(),
			),
			502
		);
	}

	$status = wp_remote_retrieve_response_code( $response );
	$body   = wp_remote_retrieve_body( $response );
	$data   = json_decode( $body, true );

	if ( 200 !== $status || ! is_array( $data ) ) {
		wp_send_json_error(
			array(
				'code'    => 'invalid_response',
				'message' => 'Invalid Face++ response.',
			),
			502
		);
	}

	if ( ! empty( $data['error_message'] ) ) {
		wp_send_json_error(
			array(
				'code'    => 'facepp_error',
				'message' => (string) $data['error_message'],
			),
			400
		);
	}

	if ( empty( $data['faces'] ) ) {
		wp_send_json_error(
			array(
				'code'    => 'no_face',
				'message' => 'No face detected.',
			),
			404
		);
	}

	$best_face = null;
	$max_area  = -1;

	foreach ( $data['faces'] as $face ) {
		if ( empty( $face['face_rectangle'] ) ) {
			continue;
		}
		$area = (float) $face['face_rectangle']['width'] * (float) $face['face_rectangle']['height'];
		if ( $area > $max_area ) {
			$max_area  = $area;
			$best_face = $face;
		}
	}

	if ( empty( $best_face['landmark']['left_eye_pupil'] ) || empty( $best_face['landmark']['right_eye_pupil'] ) ) {
		wp_send_json_error(
			array(
				'code'    => 'no_eye_landmark',
				'message' => 'Eye pupil landmarks are missing.',
			),
			404
		);
	}

	wp_send_json_success(
		array(
			'left_eye'  => array(
				'x' => (float) $best_face['landmark']['left_eye_pupil']['x'],
				'y' => (float) $best_face['landmark']['left_eye_pupil']['y'],
			),
			'right_eye' => array(
				'x' => (float) $best_face['landmark']['right_eye_pupil']['x'],
				'y' => (float) $best_face['landmark']['right_eye_pupil']['y'],
			),
		)
	);
}
add_action( 'wp_ajax_facepp_tryon_detect', 'facepp_tryon_ajax_detect' );
add_action( 'wp_ajax_nopriv_facepp_tryon_detect', 'facepp_tryon_ajax_detect' );

function facepp_tryon_shortcode() {
	$settings = facepp_tryon_get_settings();
	facepp_tryon_enqueue_assets();
	ob_start();
	?>
	<div class="facepp-tryon-app">
		<button type="button" class="facepp-tryon-open button button-primary"><?php echo esc_html( $settings['button_label'] ); ?></button>
		<div class="facepp-tryon-modal" hidden>
			<div class="facepp-tryon-mask" data-close="1"></div>
			<div class="facepp-tryon-dialog" role="dialog" aria-modal="true">
				<button class="facepp-tryon-close" type="button" data-close="1">&times;</button>
				<div class="facepp-tryon-main">
					<div class="facepp-tryon-stage-wrap">
						<div class="facepp-tryon-stage">
							<img class="facepp-tryon-photo" alt="" />
							<img class="facepp-tryon-frame" alt="" />
							<div class="facepp-tryon-empty">Upload photo and pick a frame.</div>
						</div>
					</div>
					<div class="facepp-tryon-panel">
						<h3><?php echo esc_html( $settings['modal_title'] ); ?></h3>
						<p class="facepp-tryon-helper"><?php echo esc_html( $settings['helper_text'] ); ?></p>
						<p class="facepp-tryon-status" aria-live="polite"></p>
						<label class="facepp-tryon-upload">
							<span>Upload Photo</span>
							<input type="file" class="facepp-tryon-file" accept="image/*" />
						</label>
						<div class="facepp-tryon-actions">
							<button type="button" class="button facepp-tryon-auto">Auto Align</button>
							<button type="button" class="button facepp-tryon-reset">Reset Manual</button>
						</div>
						<div class="facepp-tryon-grid">
							<label><span>Move X</span><input data-control="x" type="range" min="-220" max="220" step="1" value="0"></label>
							<label><span>Move Y</span><input data-control="y" type="range" min="-220" max="220" step="1" value="0"></label>
							<label><span>Scale</span><input data-control="scale" type="range" min="0.5" max="2.4" step="0.01" value="1"></label>
							<label><span>Rotate</span><input data-control="rotate" type="range" min="-35" max="35" step="0.1" value="0"></label>
						</div>
						<div class="facepp-tryon-model-wrap">
							<h4>Test Models</h4>
							<div class="facepp-tryon-models"></div>
						</div>
						<h4>Frames</h4>
						<div class="facepp-tryon-frames"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'facepp_virtual_tryon', 'facepp_tryon_shortcode' );

function facepp_tryon_field( $key, $label, $type = 'text', $media = false ) {
	$s = facepp_tryon_get_settings();
	$v = isset( $s[ $key ] ) ? $s[ $key ] : '';
	?>
	<tr>
		<th scope="row"><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
		<td>
			<input id="<?php echo esc_attr( $key ); ?>" name="facepp_tryon_settings[<?php echo esc_attr( $key ); ?>]" type="<?php echo esc_attr( $type ); ?>" class="regular-text" value="<?php echo esc_attr( $v ); ?>" />
			<?php if ( $media ) : ?>
				<button type="button" class="button facepp-tryon-pick" data-target="#<?php echo esc_attr( $key ); ?>">Choose image</button>
			<?php endif; ?>
		</td>
	</tr>
	<?php
}

function facepp_tryon_render_settings_page() {
	?>
	<div class="wrap">
		<h1>Face++ Virtual Try On</h1>
		<p>Use shortcode <code>[facepp_virtual_tryon]</code> on any page or product description.</p>
		<p><a class="button button-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=facepp-tryon-preview' ) ); ?>">Open Admin Preview</a></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'facepp_tryon_settings_group' ); ?>
			<table class="form-table">
				<tbody>
					<?php facepp_tryon_field( 'api_key', 'Face++ API Key' ); ?>
					<?php facepp_tryon_field( 'api_secret', 'Face++ API Secret', 'password' ); ?>
					<?php facepp_tryon_field( 'api_endpoint', 'Face++ Detect Endpoint' ); ?>
					<?php facepp_tryon_field( 'button_label', 'Open Button Label' ); ?>
					<?php facepp_tryon_field( 'modal_title', 'Modal Title' ); ?>
					<?php facepp_tryon_field( 'helper_text', 'Helper Text' ); ?>
					<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
						<?php facepp_tryon_field( 'frame_' . $i . '_name', 'Frame ' . $i . ' Name' ); ?>
						<?php facepp_tryon_field( 'frame_' . $i . '_url', 'Frame ' . $i . ' PNG URL', 'url', true ); ?>
						<?php facepp_tryon_field( 'frame_' . $i . '_wf', 'Frame ' . $i . ' Width Factor', 'number' ); ?>
						<?php facepp_tryon_field( 'frame_' . $i . '_yo', 'Frame ' . $i . ' Y Offset', 'number' ); ?>
					<?php endfor; ?>
					<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
						<?php facepp_tryon_field( 'model_' . $i . '_name', 'Test Model ' . $i . ' Name' ); ?>
						<?php facepp_tryon_field( 'model_' . $i . '_url', 'Test Model ' . $i . ' Image URL', 'url', true ); ?>
					<?php endfor; ?>
				</tbody>
			</table>
			<?php submit_button(); ?>
		</form>
		<p><strong>Endpoint tips:</strong> US 通常用 <code>https://api-us.faceplusplus.com/facepp/v3/detect</code>；中国区通常用 <code>https://api-cn.faceplusplus.com/facepp/v3/detect</code>。Key/Secret 必须和区域匹配。</p>
	</div>
	<?php
}

function facepp_tryon_render_preview_page() {
	?>
	<div class="wrap">
		<h1>Face++ Try On Preview</h1>
		<p>Use this page to upload or choose model photos and test alignment before adding the shortcode to front-end pages.</p>
		<?php echo do_shortcode( '[facepp_virtual_tryon]' ); ?>
	</div>
	<?php
}
