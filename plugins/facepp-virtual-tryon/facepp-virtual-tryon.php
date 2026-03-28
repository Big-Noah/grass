<?php
/**
 * Plugin Name: Face++ Virtual Try On
 * Description: Independent virtual try-on plugin using Face++ eye landmarks for glasses alignment.
 * Version: 1.2.6
 * Author: Codex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FACEPP_TRYON_VERSION', '1.2.6' );
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
		'frame_1_fs'   => '0.78',
		'frame_1_lx'   => '0.32',
		'frame_1_ly'   => '0.43',
		'frame_1_rx'   => '0.68',
		'frame_1_ry'   => '0.43',
		'frame_2_name' => 'Frame 2',
		'frame_2_url'  => '',
		'frame_2_wf'   => '2.15',
		'frame_2_yo'   => '0.02',
		'frame_2_fs'   => '0.78',
		'frame_2_lx'   => '0.32',
		'frame_2_ly'   => '0.43',
		'frame_2_rx'   => '0.68',
		'frame_2_ry'   => '0.43',
		'frame_3_name' => 'Frame 3',
		'frame_3_url'  => '',
		'frame_3_wf'   => '2.15',
		'frame_3_yo'   => '0.02',
		'frame_3_fs'   => '0.78',
		'frame_3_lx'   => '0.32',
		'frame_3_ly'   => '0.43',
		'frame_3_rx'   => '0.68',
		'frame_3_ry'   => '0.43',
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

function facepp_tryon_default_models() {
	return array(
		array(
			'name'   => 'Model 1',
			'url'    => 'https://img.muukal.com/img/model/m3.jpg!mk',
			'preset' => array(
				'frameWidth'  => 172,
				'frameTop'    => 121,
				'frameLeft'   => 85,
				'frameRotate' => 0,
			),
		),
		array(
			'name'   => 'Model 2',
			'url'    => 'https://img.muukal.com/img/model/m1.jpg!mk',
			'preset' => array(
				'frameWidth'  => 188,
				'frameTop'    => 120,
				'frameLeft'   => 87,
				'frameRotate' => 0,
			),
		),
		array(
			'name'   => 'Model 3',
			'url'    => 'https://img.muukal.com/img/model/m5.jpg!mk',
			'preset' => array(
				'frameWidth'  => 120,
				'frameTop'    => 131,
				'frameLeft'   => 106,
				'frameRotate' => -7,
			),
		),
		array(
			'name'   => 'Model 4',
			'url'    => 'https://img.muukal.com/img/model/m6.jpg!mk',
			'preset' => array(
				'frameWidth'  => 141,
				'frameTop'    => 98,
				'frameLeft'   => 113,
				'frameRotate' => 0,
			),
		),
		array(
			'name'   => 'Model 5',
			'url'    => 'https://img.muukal.com/img/model/m2.jpg!mk',
			'preset' => array(
				'frameWidth'  => 143,
				'frameTop'    => 96,
				'frameLeft'   => 101,
				'frameRotate' => 0,
			),
		),
		array(
			'name'   => 'Model 6',
			'url'    => 'https://img.muukal.com/img/model/m4.jpg!mk',
			'preset' => array(
				'frameWidth'  => 169,
				'frameTop'    => 87,
				'frameLeft'   => 89,
				'frameRotate' => 1,
			),
		),
	);
}

function facepp_tryon_get_settings() {
	return wp_parse_args( get_option( 'facepp_tryon_settings', array() ), facepp_tryon_defaults() );
}

function facepp_tryon_build_product_frames( $product_id ) {
	if ( ! $product_id || ! function_exists( 'muukal_loop_swatch_get_rows' ) ) {
		return array();
	}

	$rows   = muukal_loop_swatch_get_rows( $product_id );
	$frames = array();

	foreach ( $rows as $index => $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$image_url = '';
		$keys      = array(
			'tryon_image',
			'try_on_image',
			'tryon_url',
			'tryon_png',
			'png_url',
			'frame_png',
			'overlay_png',
			'overlay_image',
			'transparent_png',
			'transparent_image',
			'main_image',
		);

		foreach ( $keys as $key ) {
			if ( empty( $row[ $key ] ) ) {
				continue;
			}

			$candidate = (string) $row[ $key ];

			if ( 'main_image' === $key && ! preg_match( '/\.png(?:$|\?)/i', $candidate ) ) {
				continue;
			}

			$image_url = esc_url_raw( $candidate );
			break;
		}

		if ( '' === $image_url ) {
			continue;
		}

		$color_name = ! empty( $row['color_name'] ) ? sanitize_text_field( (string) $row['color_name'] ) : 'Frame ' . ( $index + 1 );
		$key        = sanitize_title(
			! empty( $row['color_slug'] )
				? (string) $row['color_slug']
				: ( ! empty( $row['variant_slug'] ) ? (string) $row['variant_slug'] : $color_name )
		);

		$frames[] = array(
			'key'         => $key,
			'name'        => $color_name,
			'url'         => $image_url,
			'thumbnail'   => ! empty( $row['main_image'] ) ? esc_url_raw( (string) $row['main_image'] ) : $image_url,
			'widthFactor' => 2.15,
			'yOffset'     => 0.02,
			'fitScale'    => 0.78,
			'leftEyeX'    => 0.32,
			'leftEyeY'    => 0.43,
			'rightEyeX'   => 0.68,
			'rightEyeY'   => 0.43,
		);
	}

	return $frames;
}

function facepp_tryon_get_instance_frames( $atts ) {
	$product_id = isset( $atts['product_id'] ) ? absint( $atts['product_id'] ) : 0;

	if ( $product_id ) {
		$product_frames = facepp_tryon_build_product_frames( $product_id );

		if ( ! empty( $product_frames ) ) {
			return $product_frames;
		}
	}

	return facepp_tryon_get_frames();
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
			'fitScale'    => isset( $settings[ 'frame_' . $i . '_fs' ] ) ? (float) $settings[ 'frame_' . $i . '_fs' ] : 0.78,
			'leftEyeX'    => isset( $settings[ 'frame_' . $i . '_lx' ] ) ? (float) $settings[ 'frame_' . $i . '_lx' ] : 0.32,
			'leftEyeY'    => isset( $settings[ 'frame_' . $i . '_ly' ] ) ? (float) $settings[ 'frame_' . $i . '_ly' ] : 0.43,
			'rightEyeX'   => isset( $settings[ 'frame_' . $i . '_rx' ] ) ? (float) $settings[ 'frame_' . $i . '_rx' ] : 0.68,
			'rightEyeY'   => isset( $settings[ 'frame_' . $i . '_ry' ] ) ? (float) $settings[ 'frame_' . $i . '_ry' ] : 0.43,
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

	if ( ! empty( $models ) ) {
		return $models;
	}

	return facepp_tryon_default_models();
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
		if (
			false !== strpos( $key, '_wf' ) ||
			false !== strpos( $key, '_yo' ) ||
			false !== strpos( $key, '_fs' ) ||
			false !== strpos( $key, '_lx' ) ||
			false !== strpos( $key, '_ly' ) ||
			false !== strpos( $key, '_rx' ) ||
			false !== strpos( $key, '_ry' )
		) {
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
		'i18n'       => array(
			'noPhoto'        => 'Upload a photo first.',
			'noFrame'        => 'Select a frame first.',
			'detecting'      => 'Detecting eyes and aligning frame...',
			'aligned'        => 'Auto align complete.',
			'detectFailed'   => 'Face detection failed. You can drag and adjust manually.',
			'noFace'         => 'No face detected. Please use a clearer front-facing portrait.',
			'missingCreds'   => 'Face++ key/secret is missing in plugin settings.',
			'photoReady'     => 'Photo loaded.',
			'modelReady'     => 'Model loaded.',
			'encodeFailed'   => 'This image cannot be sent to Face++ (cross-origin blocked). Use local upload or WordPress Media image.',
			'frameSelected'  => 'Frame selected.',
			'manualReset'    => 'Manual adjustments reset.',
			'badFrameFormat' => 'Frame image should be a transparent PNG. JPG or white-background product photos will not align correctly.',
			'uploading'      => 'Loading your photo...',
			'loadingModel'   => 'Loading model and aligning frame...',
			'fallbackAligned'=> 'Face detection was not available for this photo. A fallback position was applied.',
		),
	);

	wp_enqueue_style( 'facepp-tryon-style' );
	wp_enqueue_script( 'facepp-tryon-script' );
	wp_localize_script( 'facepp-tryon-script', 'faceppTryonGlobals', $config );
}

function facepp_tryon_ajax_detect() {
	check_ajax_referer( 'facepp_tryon_detect', 'nonce' );

	$settings   = facepp_tryon_get_settings();
	$api_key    = isset( $settings['api_key'] ) ? trim( (string) $settings['api_key'] ) : '';
	$api_secret = isset( $settings['api_secret'] ) ? trim( (string) $settings['api_secret'] ) : '';
	$endpoint   = isset( $settings['api_endpoint'] ) ? trim( (string) $settings['api_endpoint'] ) : '';
	$image_data = isset( $_POST['image'] ) ? (string) wp_unslash( $_POST['image'] ) : '';
	$image_url  = isset( $_POST['image_url'] ) ? esc_url_raw( (string) wp_unslash( $_POST['image_url'] ) ) : '';

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

	if ( ! preg_match( '/^data:image\/(?:png|jpe?g|webp);base64,/', $image_data ) && '' === $image_url ) {
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
		'return_landmark' => '1',
	);

	if ( preg_match( '/^data:image\/(?:png|jpe?g|webp);base64,/', $image_data ) ) {
		$payload['image_base64'] = preg_replace( '/^data:image\/(?:png|jpe?g|webp);base64,/', '', $image_data );
	} else {
		$image_response = wp_remote_get(
			$image_url,
			array(
				'timeout'     => 20,
				'redirection' => 4,
			)
		);

		if ( is_wp_error( $image_response ) ) {
			wp_send_json_error(
				array(
					'code'    => 'image_fetch_failed',
					'message' => $image_response->get_error_message(),
				),
				502
			);
		}

		$image_status = wp_remote_retrieve_response_code( $image_response );
		$image_body   = wp_remote_retrieve_body( $image_response );

		if ( 200 !== $image_status || '' === $image_body ) {
			wp_send_json_error(
				array(
					'code'    => 'image_fetch_failed',
					'message' => 'Could not fetch the source image.',
				),
				502
			);
		}

		$payload['image_base64'] = base64_encode( $image_body );
	}

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

function facepp_tryon_shortcode( $atts = array() ) {
	$settings = facepp_tryon_get_settings();
	$atts     = shortcode_atts(
		array(
			'product_id'    => '',
			'button_label'  => '',
			'button_class'  => 'tyr-btn col-xl-2',
			'modal_title'   => '',
			'helper_text'   => '',
			'default_frame' => '',
		),
		$atts,
		'facepp_virtual_tryon'
	);

	$button_label  = '' !== trim( (string) $atts['button_label'] ) ? sanitize_text_field( (string) $atts['button_label'] ) : $settings['button_label'];
	$button_class  = trim( (string) $atts['button_class'] );
	$modal_title   = '' !== trim( (string) $atts['modal_title'] ) ? sanitize_text_field( (string) $atts['modal_title'] ) : $settings['modal_title'];
	$helper_text   = '' !== trim( (string) $atts['helper_text'] ) ? sanitize_text_field( (string) $atts['helper_text'] ) : $settings['helper_text'];
	$product_id    = absint( $atts['product_id'] );
	$default_frame = sanitize_title( (string) $atts['default_frame'] );
	$instance_id   = wp_unique_id( 'facepp-tryon-' );
	$models        = facepp_tryon_get_models();
	$frames        = facepp_tryon_get_instance_frames(
		array(
			'product_id' => $product_id,
		)
	);

	if ( '' === $default_frame && ! empty( $frames[0]['key'] ) ) {
		$default_frame = sanitize_title( (string) $frames[0]['key'] );
	}

	$pd_options = range( 50, 80 );
	$config     = array(
		'productId'    => $product_id,
		'modalTitle'   => $modal_title,
		'helperText'   => $helper_text,
		'defaultFrame' => $default_frame,
		'stageWidth'   => 350,
		'stageHeight'  => 410,
		'frames'       => $frames,
		'testModels'   => $models,
	);

	facepp_tryon_enqueue_assets();
	ob_start();
	?>
	<div class="facepp-tryon-app" data-instance-id="<?php echo esc_attr( $instance_id ); ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>">
		<button type="button" class="facepp-tryon-open <?php echo esc_attr( $button_class ); ?>"><?php echo esc_html( $button_label ); ?></button>
		<div class="facepp-tryon-modal" hidden>
			<div class="facepp-tryon-mask" data-close="1"></div>
			<div class="facepp-tryon-dialog" role="dialog" aria-modal="true">
				<div class="facepp-tryon-header">
					<button class="facepp-tryon-close" type="button" data-close="1" aria-label="Close">
						<span class="facepp-tryon-close-icon" aria-hidden="true">
							<svg viewBox="0 0 20 20" focusable="false">
								<path d="M5 5l10 10"></path>
								<path d="M15 5L5 15"></path>
							</svg>
						</span>
					</button>
					<h4 class="facepp-tryon-title"><?php echo esc_html( $modal_title ); ?></h4>
				</div>
				<div class="facepp-tryon-main">
					<div class="facepp-tryon-stage-shell">
						<div class="facepp-tryon-stage-wrap">
							<div class="facepp-tryon-stage">
								<img class="facepp-tryon-photo" alt="" />
								<img class="facepp-tryon-frame" alt="" />
								<span class="eye_icon facepp-tryon-eye facepp-tryon-eye-left" aria-hidden="true"></span>
								<span class="eye_icon facepp-tryon-eye facepp-tryon-eye-right" aria-hidden="true"></span>
								<div class="facepp-tryon-empty">Upload photo and pick a frame.</div>
								<div class="facepp-tryon-stage-overlay" aria-hidden="true">
									<span class="facepp-tryon-stage-spinner"></span>
									<span class="facepp-tryon-stage-loading-text">Aligning frame...</span>
								</div>
							</div>
							<div class="facepp-tryon-upload">
								<input
									id="<?php echo esc_attr( $instance_id ); ?>-upload"
									name="<?php echo esc_attr( $instance_id ); ?>-upload"
									type="file"
									class="facepp-tryon-file"
									accept="image/*"
									aria-label="Upload Image"
								/>
								<label class="facepp-tryon-upload-trigger" for="<?php echo esc_attr( $instance_id ); ?>-upload">Upload Image</label>
							</div>
							<div class="facepp-tryon-controls">
								<button type="button" class="tdo_btn" data-tryon-action="size_b">+</button>
								<button type="button" class="tdo_btn" data-tryon-action="size_s">-</button>
								<button type="button" class="tdo_btn" data-tryon-action="rotate_l">&#8634;</button>
								<button type="button" class="tdo_btn" data-tryon-action="rotate_r">&#8635;</button>
								<button type="button" class="tdo_btn" data-tryon-action="move_t">&#8593;</button>
								<button type="button" class="tdo_btn" data-tryon-action="move_r">&#8594;</button>
								<button type="button" class="tdo_btn" data-tryon-action="move_b">&#8595;</button>
								<button type="button" class="tdo_btn" data-tryon-action="move_l">&#8592;</button>
							</div>
						</div>
					</div>
					<div class="facepp-tryon-panel">
						<h4 class="facepp-tryon-panel-title">Upload your picture or use any of our models</h4>
						<div class="facepp-tryon-models"></div>
						<div class="facepp-tryon-color-list">
							<h4 class="facepp-tryon-panel-title">Click and change the color</h4>
							<div class="facepp-tryon-frames"></div>
						</div>
						<div class="facepp-tryon-helper">
							<p><?php echo esc_html( $helper_text ); ?></p>
							<p>1, Adjust the photo with the bottom controls.</p>
							<p>2, Drag the glasses to change the position.</p>
							<p>3, Set your PD, if you know it.</p>
						</div>
						<div class="facepp-tryon-pd-row">
							<span>Set your PD</span>
							<select class="facepp-tryon-pd">
								<?php foreach ( $pd_options as $pd_option ) : ?>
									<option value="<?php echo esc_attr( $pd_option ); ?>"<?php selected( 63, $pd_option ); ?>><?php echo esc_html( $pd_option ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<p class="facepp-tryon-status" aria-live="polite"></p>
						<div class="facepp-tryon-footnote">Try on is only for style reference. Not Size.</div>
					</div>
				</div>
			</div>
		</div>
		<script type="application/json" class="facepp-tryon-instance-config"><?php echo wp_json_encode( $config ); ?></script>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'facepp_virtual_tryon', 'facepp_tryon_shortcode' );

function facepp_tryon_field( $key, $label, $type = 'text', $media = false ) {
	$s = facepp_tryon_get_settings();
	$v = isset( $s[ $key ] ) ? $s[ $key ] : '';
	$step = 'number' === $type ? ' step="any"' : '';
	?>
	<tr>
		<th scope="row"><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
		<td>
			<input id="<?php echo esc_attr( $key ); ?>" name="facepp_tryon_settings[<?php echo esc_attr( $key ); ?>]" type="<?php echo esc_attr( $type ); ?>" class="regular-text" value="<?php echo esc_attr( $v ); ?>"<?php echo $step; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> />
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
		<p>Use shortcode <code>[facepp_virtual_tryon]</code> on any page or inside the product detail shortcode.</p>
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
						<?php facepp_tryon_field( 'frame_' . $i . '_fs', 'Frame ' . $i . ' Fit Scale', 'number' ); ?>
						<?php facepp_tryon_field( 'frame_' . $i . '_lx', 'Frame ' . $i . ' Left Eye X (0-1)', 'number' ); ?>
						<?php facepp_tryon_field( 'frame_' . $i . '_ly', 'Frame ' . $i . ' Left Eye Y (0-1)', 'number' ); ?>
						<?php facepp_tryon_field( 'frame_' . $i . '_rx', 'Frame ' . $i . ' Right Eye X (0-1)', 'number' ); ?>
						<?php facepp_tryon_field( 'frame_' . $i . '_ry', 'Frame ' . $i . ' Right Eye Y (0-1)', 'number' ); ?>
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
		<p><strong>Frame tips:</strong> 请优先使用透明 PNG 正面镜框图。Left/Right Eye X/Y 表示镜框素材里左右镜片中心点的相对位置，范围通常在 <code>0</code> 到 <code>1</code> 之间。</p>
	</div>
	<?php
}

function facepp_tryon_render_preview_page() {
	?>
	<div class="wrap">
		<h1>Face++ Try On Preview</h1>
		<p>Use this page to preview the Muukal-style try-on modal before adding the shortcode to front-end pages.</p>
		<?php echo do_shortcode( '[facepp_virtual_tryon]' ); ?>
	</div>
	<?php
}
