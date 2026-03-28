<?php
/**
 * Plugin Name: Face++ Virtual Try On
 * Description: Independent virtual try-on plugin using Face++ eye landmarks for glasses alignment.
 * Version: 1.3.0
 * Author: Codex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FACEPP_TRYON_VERSION', '1.3.0' );
define( 'FACEPP_TRYON_FILE', __FILE__ );
define( 'FACEPP_TRYON_DIR', plugin_dir_path( __FILE__ ) );
define( 'FACEPP_TRYON_URL', plugin_dir_url( __FILE__ ) );
define( 'FACEPP_TRYON_PRODUCT_FRAMES_META', '_facepp_tryon_product_frames' );
define( 'FACEPP_TRYON_PRODUCT_MODELS_META', '_facepp_tryon_product_models' );

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
		'frame_1_lx'   => '0.29',
		'frame_1_ly'   => '0.52',
		'frame_1_rx'   => '0.71',
		'frame_1_ry'   => '0.52',
		'frame_2_name' => 'Frame 2',
		'frame_2_url'  => '',
		'frame_2_wf'   => '2.15',
		'frame_2_yo'   => '0.02',
		'frame_2_fs'   => '0.78',
		'frame_2_lx'   => '0.29',
		'frame_2_ly'   => '0.52',
		'frame_2_rx'   => '0.71',
		'frame_2_ry'   => '0.52',
		'frame_3_name' => 'Frame 3',
		'frame_3_url'  => '',
		'frame_3_wf'   => '2.15',
		'frame_3_yo'   => '0.02',
		'frame_3_fs'   => '0.78',
		'frame_3_lx'   => '0.29',
		'frame_3_ly'   => '0.52',
		'frame_3_rx'   => '0.71',
		'frame_3_ry'   => '0.52',
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

function facepp_tryon_product_frame_defaults() {
	return array(
		'name'        => '',
		'key'         => '',
		'source_url'  => '',
		'processed_url' => '',
		'thumbnail'   => '',
		'attachment_id' => 0,
		'widthFactor' => 2.15,
		'yOffset'     => 0.02,
		'fitScale'    => 0.78,
		'leftEyeX'    => 0.29,
		'leftEyeY'    => 0.52,
		'rightEyeX'   => 0.71,
		'rightEyeY'   => 0.52,
	);
}

function facepp_tryon_product_model_defaults() {
	return array(
		'name'          => '',
		'source_url'    => '',
		'processed_url' => '',
		'attachment_id' => 0,
		'detectedEyes'  => array(),
	);
}

function facepp_tryon_normalize_product_frame( $entry, $index = 0 ) {
	$defaults = facepp_tryon_product_frame_defaults();
	$entry    = is_array( $entry ) ? $entry : array();
	$name     = ! empty( $entry['name'] ) ? sanitize_text_field( (string) $entry['name'] ) : 'Custom Frame ' . ( $index + 1 );
	$key      = ! empty( $entry['key'] ) ? sanitize_title( (string) $entry['key'] ) : sanitize_title( $name );

	return array(
		'name'          => $name,
		'key'           => $key,
		'source_url'    => ! empty( $entry['source_url'] ) ? esc_url_raw( (string) $entry['source_url'] ) : '',
		'processed_url' => ! empty( $entry['processed_url'] ) ? esc_url_raw( (string) $entry['processed_url'] ) : '',
		'thumbnail'     => ! empty( $entry['thumbnail'] ) ? esc_url_raw( (string) $entry['thumbnail'] ) : '',
		'attachment_id' => ! empty( $entry['attachment_id'] ) ? absint( $entry['attachment_id'] ) : 0,
		'widthFactor'   => isset( $entry['widthFactor'] ) ? (float) $entry['widthFactor'] : $defaults['widthFactor'],
		'yOffset'       => isset( $entry['yOffset'] ) ? (float) $entry['yOffset'] : $defaults['yOffset'],
		'fitScale'      => isset( $entry['fitScale'] ) ? (float) $entry['fitScale'] : $defaults['fitScale'],
		'leftEyeX'      => isset( $entry['leftEyeX'] ) ? (float) $entry['leftEyeX'] : $defaults['leftEyeX'],
		'leftEyeY'      => isset( $entry['leftEyeY'] ) ? (float) $entry['leftEyeY'] : $defaults['leftEyeY'],
		'rightEyeX'     => isset( $entry['rightEyeX'] ) ? (float) $entry['rightEyeX'] : $defaults['rightEyeX'],
		'rightEyeY'     => isset( $entry['rightEyeY'] ) ? (float) $entry['rightEyeY'] : $defaults['rightEyeY'],
	);
}

function facepp_tryon_normalize_product_model( $entry, $index = 0 ) {
	$entry = is_array( $entry ) ? $entry : array();
	$name  = ! empty( $entry['name'] ) ? sanitize_text_field( (string) $entry['name'] ) : 'Custom Model ' . ( $index + 1 );
	$eyes  = array();

	if ( ! empty( $entry['detectedEyes'] ) && is_array( $entry['detectedEyes'] ) ) {
		foreach ( array( 'left_eye', 'right_eye' ) as $side ) {
			if ( empty( $entry['detectedEyes'][ $side ] ) || ! is_array( $entry['detectedEyes'][ $side ] ) ) {
				continue;
			}

			$eyes[ $side ] = array(
				'x' => isset( $entry['detectedEyes'][ $side ]['x'] ) ? (float) $entry['detectedEyes'][ $side ]['x'] : 0,
				'y' => isset( $entry['detectedEyes'][ $side ]['y'] ) ? (float) $entry['detectedEyes'][ $side ]['y'] : 0,
			);
		}
	}

	return array(
		'name'          => $name,
		'source_url'    => ! empty( $entry['source_url'] ) ? esc_url_raw( (string) $entry['source_url'] ) : '',
		'processed_url' => ! empty( $entry['processed_url'] ) ? esc_url_raw( (string) $entry['processed_url'] ) : '',
		'attachment_id' => ! empty( $entry['attachment_id'] ) ? absint( $entry['attachment_id'] ) : 0,
		'detectedEyes'  => $eyes,
	);
}

function facepp_tryon_get_product_custom_frames( $product_id ) {
	$stored = get_post_meta( $product_id, FACEPP_TRYON_PRODUCT_FRAMES_META, true );
	$stored = is_array( $stored ) ? $stored : array();
	$frames = array();

	foreach ( $stored as $index => $entry ) {
		$frame = facepp_tryon_normalize_product_frame( $entry, (int) $index );

		if ( '' === $frame['source_url'] && '' === $frame['processed_url'] ) {
			continue;
		}

		$frames[] = $frame;
	}

	return $frames;
}

function facepp_tryon_get_product_custom_models( $product_id ) {
	$stored = get_post_meta( $product_id, FACEPP_TRYON_PRODUCT_MODELS_META, true );
	$stored = is_array( $stored ) ? $stored : array();
	$models = array();

	foreach ( $stored as $index => $entry ) {
		$model = facepp_tryon_normalize_product_model( $entry, (int) $index );

		if ( '' === $model['source_url'] && '' === $model['processed_url'] ) {
			continue;
		}

		$models[] = $model;
	}

	return $models;
}

function facepp_tryon_fetch_attachment_payload( $attachment_id, $fallback_url = '' ) {
	$attachment_id = absint( $attachment_id );
	$fallback_url  = esc_url_raw( (string) $fallback_url );

	if ( $attachment_id ) {
		$file_path = get_attached_file( $attachment_id );

		if ( $file_path && file_exists( $file_path ) ) {
			$bytes = file_get_contents( $file_path );

			if ( false !== $bytes ) {
				return array(
					'bytes' => $bytes,
					'url'   => wp_get_attachment_url( $attachment_id ) ?: $fallback_url,
					'mime'  => get_post_mime_type( $attachment_id ) ?: '',
				);
			}
		}
	}

	if ( '' === $fallback_url ) {
		return null;
	}

	$response = wp_remote_get(
		$fallback_url,
		array(
			'timeout'     => 20,
			'redirection' => 4,
		)
	);

	if ( is_wp_error( $response ) ) {
		return null;
	}

	if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return null;
	}

	$body = wp_remote_retrieve_body( $response );

	if ( '' === $body ) {
		return null;
	}

	return array(
		'bytes' => $body,
		'url'   => $fallback_url,
		'mime'  => (string) wp_remote_retrieve_header( $response, 'content-type' ),
	);
}

function facepp_tryon_create_truecolor_canvas( $width, $height ) {
	$canvas = imagecreatetruecolor( $width, $height );

	if ( ! $canvas ) {
		return null;
	}

	imagealphablending( $canvas, false );
	imagesavealpha( $canvas, true );
	$transparent = imagecolorallocatealpha( $canvas, 0, 0, 0, 127 );
	imagefilledrectangle( $canvas, 0, 0, $width, $height, $transparent );

	return $canvas;
}

function facepp_tryon_save_generated_png( $image, $product_id, $filename ) {
	$uploads = wp_upload_dir();

	if ( ! empty( $uploads['error'] ) ) {
		return '';
	}

	$directory = trailingslashit( $uploads['basedir'] ) . 'facepp-tryon/product-' . absint( $product_id ) . '/';

	if ( ! wp_mkdir_p( $directory ) ) {
		return '';
	}

	$file_path = $directory . sanitize_file_name( $filename );

	if ( ! imagepng( $image, $file_path ) ) {
		return '';
	}

	return trailingslashit( $uploads['baseurl'] ) . 'facepp-tryon/product-' . absint( $product_id ) . '/' . basename( $file_path );
}

function facepp_tryon_prepare_frame_asset( $product_id, $slot, $entry ) {
	if ( ! function_exists( 'imagecreatefromstring' ) ) {
		return $entry;
	}

	$payload = facepp_tryon_fetch_attachment_payload( $entry['attachment_id'], $entry['source_url'] );

	if ( empty( $payload['bytes'] ) ) {
		return $entry;
	}

	$image = @imagecreatefromstring( $payload['bytes'] );

	if ( ! $image ) {
		return $entry;
	}

	$width   = imagesx( $image );
	$height  = imagesy( $image );
	$min_x   = $width;
	$min_y   = $height;
	$max_x   = -1;
	$max_y   = -1;

	for ( $x = 0; $x < $width; $x++ ) {
		for ( $y = 0; $y < $height; $y++ ) {
			$rgba  = imagecolorat( $image, $x, $y );
			$alpha = ( $rgba & 0x7F000000 ) >> 24;

			if ( $alpha >= 126 ) {
				continue;
			}

			$min_x = min( $min_x, $x );
			$min_y = min( $min_y, $y );
			$max_x = max( $max_x, $x );
			$max_y = max( $max_y, $y );
		}
	}

	if ( $max_x < 0 || $max_y < 0 ) {
		$min_x = 0;
		$min_y = 0;
		$max_x = $width - 1;
		$max_y = $height - 1;
	}

	$trim_w = max( 1, $max_x - $min_x + 1 );
	$trim_h = max( 1, $max_y - $min_y + 1 );
	$canvas = facepp_tryon_create_truecolor_canvas( $trim_w, $trim_h );

	if ( ! $canvas ) {
		imagedestroy( $image );
		return $entry;
	}

	imagecopy( $canvas, $image, 0, 0, $min_x, $min_y, $trim_w, $trim_h );
	imagedestroy( $image );

	$file_name = sprintf( 'frame-%d-%d.png', absint( $slot ) + 1, time() );
	$url       = facepp_tryon_save_generated_png( $canvas, $product_id, $file_name );
	imagedestroy( $canvas );

	if ( '' === $url ) {
		return $entry;
	}

	$entry['processed_url'] = $url;
	$entry['thumbnail']     = $url;

	return $entry;
}

function facepp_tryon_detect_face_result( $args ) {
	$settings   = facepp_tryon_get_settings();
	$api_key    = isset( $settings['api_key'] ) ? trim( (string) $settings['api_key'] ) : '';
	$api_secret = isset( $settings['api_secret'] ) ? trim( (string) $settings['api_secret'] ) : '';
	$endpoint   = isset( $settings['api_endpoint'] ) ? trim( (string) $settings['api_endpoint'] ) : '';
	$image_data = ! empty( $args['image_base64'] ) ? (string) $args['image_base64'] : '';
	$image_url  = ! empty( $args['image_url'] ) ? esc_url_raw( (string) $args['image_url'] ) : '';

	if ( '' === $endpoint ) {
		$endpoint = 'https://api-us.faceplusplus.com/facepp/v3/detect';
	}

	if ( '' === $api_key || '' === $api_secret ) {
		return new WP_Error( 'missing_credentials', 'Missing Face++ credentials.' );
	}

	if ( '' === $image_data && '' === $image_url ) {
		return new WP_Error( 'invalid_image', 'Invalid image payload.' );
	}

	$payload = array(
		'api_key'         => $api_key,
		'api_secret'      => $api_secret,
		'return_landmark' => '1',
	);

	if ( '' !== $image_data ) {
		$payload['image_base64'] = $image_data;
	} else {
		$payload['image_url'] = $image_url;
	}

	$response = wp_remote_post(
		$endpoint,
		array(
			'timeout' => 20,
			'body'    => $payload,
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$status = wp_remote_retrieve_response_code( $response );
	$body   = wp_remote_retrieve_body( $response );
	$data   = json_decode( $body, true );

	if ( 200 !== $status || ! is_array( $data ) ) {
		return new WP_Error( 'invalid_response', 'Invalid Face++ response.' );
	}

	if ( ! empty( $data['error_message'] ) ) {
		return new WP_Error( 'facepp_error', (string) $data['error_message'] );
	}

	if ( empty( $data['faces'] ) ) {
		return new WP_Error( 'no_face', 'No face detected.' );
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
		return new WP_Error( 'no_eye_landmark', 'Eye pupil landmarks are missing.' );
	}

	return array(
		'left_eye'  => array(
			'x' => (float) $best_face['landmark']['left_eye_pupil']['x'],
			'y' => (float) $best_face['landmark']['left_eye_pupil']['y'],
		),
		'right_eye' => array(
			'x' => (float) $best_face['landmark']['right_eye_pupil']['x'],
			'y' => (float) $best_face['landmark']['right_eye_pupil']['y'],
		),
		'face_rectangle' => array(
			'top'    => (float) $best_face['face_rectangle']['top'],
			'left'   => (float) $best_face['face_rectangle']['left'],
			'width'  => (float) $best_face['face_rectangle']['width'],
			'height' => (float) $best_face['face_rectangle']['height'],
		),
	);
}

function facepp_tryon_prepare_model_asset( $product_id, $slot, $entry ) {
	if ( ! function_exists( 'imagecreatefromstring' ) ) {
		return $entry;
	}

	$payload = facepp_tryon_fetch_attachment_payload( $entry['attachment_id'], $entry['source_url'] );

	if ( empty( $payload['bytes'] ) ) {
		return $entry;
	}

	$image = @imagecreatefromstring( $payload['bytes'] );

	if ( ! $image ) {
		return $entry;
	}

	$detected = facepp_tryon_detect_face_result(
		array(
			'image_base64' => base64_encode( $payload['bytes'] ),
		)
	);

	if ( is_wp_error( $detected ) ) {
		imagedestroy( $image );
		return $entry;
	}

	$source_w     = imagesx( $image );
	$source_h     = imagesy( $image );
	$target_w     = 350;
	$target_h     = 410;
	$target_ratio = $target_w / $target_h;
	$left_eye     = $detected['left_eye'];
	$right_eye    = $detected['right_eye'];
	$face_rect    = $detected['face_rectangle'];
	$eye_distance = sqrt( pow( $right_eye['x'] - $left_eye['x'], 2 ) + pow( $right_eye['y'] - $left_eye['y'], 2 ) );
	$center_x     = ( $left_eye['x'] + $right_eye['x'] ) / 2;
	$eye_center_y = ( $left_eye['y'] + $right_eye['y'] ) / 2;
	$crop_w       = max( $face_rect['width'] * 2, $eye_distance * 3.4, $source_w * 0.4 );
	$crop_w       = min( $crop_w, $source_w );
	$crop_h       = $crop_w / $target_ratio;

	if ( $crop_h > $source_h ) {
		$crop_h = $source_h;
		$crop_w = $crop_h * $target_ratio;
	}

	$crop_x = $center_x - ( $crop_w / 2 );
	$crop_y = $eye_center_y - ( $crop_h * 0.38 );
	$crop_x = max( 0, min( $source_w - $crop_w, $crop_x ) );
	$crop_y = max( 0, min( $source_h - $crop_h, $crop_y ) );
	$canvas = facepp_tryon_create_truecolor_canvas( $target_w, $target_h );

	if ( ! $canvas ) {
		imagedestroy( $image );
		return $entry;
	}

	imagecopyresampled( $canvas, $image, 0, 0, (int) round( $crop_x ), (int) round( $crop_y ), $target_w, $target_h, (int) round( $crop_w ), (int) round( $crop_h ) );
	imagedestroy( $image );

	$file_name = sprintf( 'model-%d-%d.png', absint( $slot ) + 1, time() );
	$url       = facepp_tryon_save_generated_png( $canvas, $product_id, $file_name );
	imagedestroy( $canvas );

	if ( '' === $url ) {
		return $entry;
	}

	$scale_x = $target_w / max( 1, $crop_w );
	$scale_y = $target_h / max( 1, $crop_h );

	$entry['processed_url'] = $url;
	$entry['detectedEyes']  = array(
		'left_eye'  => array(
			'x' => round( ( $left_eye['x'] - $crop_x ) * $scale_x, 2 ),
			'y' => round( ( $left_eye['y'] - $crop_y ) * $scale_y, 2 ),
		),
		'right_eye' => array(
			'x' => round( ( $right_eye['x'] - $crop_x ) * $scale_x, 2 ),
			'y' => round( ( $right_eye['y'] - $crop_y ) * $scale_y, 2 ),
		),
	);

	return $entry;
}

function facepp_tryon_build_product_frames( $product_id ) {
	$frames = array();
	$seen   = array();

	if ( $product_id ) {
		foreach ( facepp_tryon_get_product_custom_frames( $product_id ) as $custom_frame ) {
			$url = '' !== $custom_frame['processed_url'] ? $custom_frame['processed_url'] : $custom_frame['source_url'];

			if ( '' === $url ) {
				continue;
			}

			$key = sanitize_title( $custom_frame['key'] );

			$frames[] = array(
				'key'         => $key,
				'name'        => $custom_frame['name'],
				'url'         => $url,
				'thumbnail'   => '' !== $custom_frame['thumbnail'] ? $custom_frame['thumbnail'] : $url,
				'widthFactor' => (float) $custom_frame['widthFactor'],
				'yOffset'     => (float) $custom_frame['yOffset'],
				'fitScale'    => (float) $custom_frame['fitScale'],
				'leftEyeX'    => (float) $custom_frame['leftEyeX'],
				'leftEyeY'    => (float) $custom_frame['leftEyeY'],
				'rightEyeX'   => (float) $custom_frame['rightEyeX'],
				'rightEyeY'   => (float) $custom_frame['rightEyeY'],
			);

			$seen[ $key ] = true;
		}
	}

	if ( ! $product_id || ! function_exists( 'muukal_loop_swatch_get_rows' ) ) {
		return $frames;
	}

	$rows = muukal_loop_swatch_get_rows( $product_id );

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

		if ( isset( $seen[ $key ] ) ) {
			continue;
		}

		$frames[] = array(
			'key'         => $key,
			'name'        => $color_name,
			'url'         => $image_url,
			'thumbnail'   => ! empty( $row['main_image'] ) ? esc_url_raw( (string) $row['main_image'] ) : $image_url,
			'widthFactor' => 2.15,
			'yOffset'     => 0.02,
			'fitScale'    => 0.78,
			'leftEyeX'    => 0.29,
			'leftEyeY'    => 0.52,
			'rightEyeX'   => 0.71,
			'rightEyeY'   => 0.52,
		);

		$seen[ $key ] = true;
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
			'leftEyeX'    => isset( $settings[ 'frame_' . $i . '_lx' ] ) ? (float) $settings[ 'frame_' . $i . '_lx' ] : 0.29,
			'leftEyeY'    => isset( $settings[ 'frame_' . $i . '_ly' ] ) ? (float) $settings[ 'frame_' . $i . '_ly' ] : 0.52,
			'rightEyeX'   => isset( $settings[ 'frame_' . $i . '_rx' ] ) ? (float) $settings[ 'frame_' . $i . '_rx' ] : 0.71,
			'rightEyeY'   => isset( $settings[ 'frame_' . $i . '_ry' ] ) ? (float) $settings[ 'frame_' . $i . '_ry' ] : 0.52,
		);
	}

	return $frames;
}

function facepp_tryon_get_models( $product_id = 0 ) {
	$product_id = absint( $product_id );
	$models     = array();

	if ( $product_id ) {
		foreach ( facepp_tryon_get_product_custom_models( $product_id ) as $custom_model ) {
			$url = '' !== $custom_model['processed_url'] ? $custom_model['processed_url'] : $custom_model['source_url'];

			if ( '' === $url ) {
				continue;
			}

			$model = array(
				'name' => $custom_model['name'],
				'url'  => $url,
			);

			if ( ! empty( $custom_model['detectedEyes']['left_eye'] ) && ! empty( $custom_model['detectedEyes']['right_eye'] ) ) {
				$model['detectedEyes'] = $custom_model['detectedEyes'];
			}

			$models[] = $model;
		}
	}

	$settings = facepp_tryon_get_settings();
	$extra    = array();

	for ( $i = 1; $i <= 4; $i++ ) {
		$url = isset( $settings[ 'model_' . $i . '_url' ] ) ? trim( (string) $settings[ 'model_' . $i . '_url' ] ) : '';
		if ( '' === $url ) {
			continue;
		}

		$extra[] = array(
			'name' => isset( $settings[ 'model_' . $i . '_name' ] ) ? sanitize_text_field( (string) $settings[ 'model_' . $i . '_name' ] ) : 'Model ' . $i,
			'url'  => esc_url_raw( $url ),
		);
	}

	if ( empty( $extra ) ) {
		$extra = facepp_tryon_default_models();
	}

	return array_merge( $models, $extra );
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
	$is_plugin_screen  = false !== strpos( $hook, 'facepp-tryon' );
	$is_product_screen = in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && 'product' === get_post_type();

	if ( ! $is_plugin_screen && ! $is_product_screen ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script( 'facepp-tryon-admin', FACEPP_TRYON_URL . 'assets/admin.js', array( 'jquery' ), FACEPP_TRYON_VERSION, true );

	if ( false !== strpos( $hook, 'facepp-tryon-preview' ) ) {
		facepp_tryon_enqueue_assets();
	}
}
add_action( 'admin_enqueue_scripts', 'facepp_tryon_admin_assets' );

function facepp_tryon_add_product_metabox() {
	add_meta_box(
		'facepp-tryon-product-assets',
		'Face++ Try On Assets',
		'facepp_tryon_render_product_metabox',
		'product',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes_product', 'facepp_tryon_add_product_metabox' );

function facepp_tryon_render_product_metabox( $post ) {
	$product_id = $post instanceof WP_Post ? $post->ID : 0;
	$frames     = facepp_tryon_get_product_custom_frames( $product_id );
	$models     = facepp_tryon_get_product_custom_models( $product_id );

	wp_nonce_field( 'facepp_tryon_product_assets', 'facepp_tryon_product_assets_nonce' );
	?>
	<p>Upload custom try-on frame PNGs and model portraits for this product. Existing built-in templates stay available as fallback. If you want a frame to auto-switch with a product color, keep the <strong>Key</strong> aligned with that color's try-on key or slug.</p>
	<table class="widefat striped" style="margin-bottom:20px;">
		<thead>
			<tr>
				<th colspan="11">Custom Frame PNGs</th>
			</tr>
			<tr>
				<th>Name</th>
				<th>Key</th>
				<th>PNG URL</th>
				<th>Width</th>
				<th>Y Offset</th>
				<th>Fit Scale</th>
				<th>Left X</th>
				<th>Left Y</th>
				<th>Right X</th>
				<th>Right Y</th>
				<th>Processed</th>
			</tr>
		</thead>
		<tbody>
			<?php for ( $i = 0; $i < 4; $i++ ) : ?>
				<?php $frame = isset( $frames[ $i ] ) ? $frames[ $i ] : facepp_tryon_product_frame_defaults(); ?>
				<tr>
					<td><input type="text" class="widefat" name="facepp_tryon_product[frames][<?php echo esc_attr( $i ); ?>][name]" value="<?php echo esc_attr( $frame['name'] ); ?>"></td>
					<td><input type="text" class="widefat" name="facepp_tryon_product[frames][<?php echo esc_attr( $i ); ?>][key]" value="<?php echo esc_attr( $frame['key'] ); ?>"></td>
					<td>
						<input type="url" class="widefat" id="facepp-frame-url-<?php echo esc_attr( $i ); ?>" name="facepp_tryon_product[frames][<?php echo esc_attr( $i ); ?>][source_url]" value="<?php echo esc_attr( $frame['source_url'] ); ?>">
						<input type="hidden" id="facepp-frame-id-<?php echo esc_attr( $i ); ?>" name="facepp_tryon_product[frames][<?php echo esc_attr( $i ); ?>][attachment_id]" value="<?php echo esc_attr( $frame['attachment_id'] ); ?>">
						<button type="button" class="button facepp-tryon-pick" data-target="#facepp-frame-url-<?php echo esc_attr( $i ); ?>" data-target-id="#facepp-frame-id-<?php echo esc_attr( $i ); ?>" data-facepp-png="1" style="margin-top:6px;">Choose PNG</button>
						<div class="facepp-admin-preview facepp-admin-preview--frame" style="margin-top:8px;">
							<?php if ( ! empty( $frame['processed_url'] ) ) : ?>
								<img class="facepp-admin-preview__image" src="<?php echo esc_url( $frame['processed_url'] ); ?>" alt="" style="max-width:140px;max-height:60px;display:block;">
							<?php elseif ( ! empty( $frame['source_url'] ) ) : ?>
								<img class="facepp-admin-preview__image" src="<?php echo esc_url( $frame['source_url'] ); ?>" alt="" style="max-width:140px;max-height:60px;display:block;">
							<?php endif; ?>
						</div>
					</td>
					<td><input type="number" step="any" class="small-text" name="facepp_tryon_product[frames][<?php echo esc_attr( $i ); ?>][widthFactor]" value="<?php echo esc_attr( $frame['widthFactor'] ); ?>"></td>
					<td><input type="number" step="any" class="small-text" name="facepp_tryon_product[frames][<?php echo esc_attr( $i ); ?>][yOffset]" value="<?php echo esc_attr( $frame['yOffset'] ); ?>"></td>
					<td><input type="number" step="any" class="small-text" name="facepp_tryon_product[frames][<?php echo esc_attr( $i ); ?>][fitScale]" value="<?php echo esc_attr( $frame['fitScale'] ); ?>"></td>
					<td><input type="number" step="any" class="small-text" name="facepp_tryon_product[frames][<?php echo esc_attr( $i ); ?>][leftEyeX]" value="<?php echo esc_attr( $frame['leftEyeX'] ); ?>"></td>
					<td><input type="number" step="any" class="small-text" name="facepp_tryon_product[frames][<?php echo esc_attr( $i ); ?>][leftEyeY]" value="<?php echo esc_attr( $frame['leftEyeY'] ); ?>"></td>
					<td><input type="number" step="any" class="small-text" name="facepp_tryon_product[frames][<?php echo esc_attr( $i ); ?>][rightEyeX]" value="<?php echo esc_attr( $frame['rightEyeX'] ); ?>"></td>
					<td><input type="number" step="any" class="small-text" name="facepp_tryon_product[frames][<?php echo esc_attr( $i ); ?>][rightEyeY]" value="<?php echo esc_attr( $frame['rightEyeY'] ); ?>"></td>
					<td>
						<?php if ( ! empty( $frame['processed_url'] ) ) : ?>
							<a href="<?php echo esc_url( $frame['processed_url'] ); ?>" target="_blank" rel="noreferrer">View trimmed PNG</a>
						<?php else : ?>
							<span style="color:#666;">Will auto-trim transparent edge on save</span>
						<?php endif; ?>
						<div style="margin-top:6px;color:#666;font-size:12px;">If the frame looks too large on product page, reduce <strong>Fit Scale</strong> first, then fine-tune Left/Right Eye X.</div>
					</td>
				</tr>
			<?php endfor; ?>
		</tbody>
	</table>

	<table class="widefat striped">
		<thead>
			<tr>
				<th colspan="4">Custom Model Images</th>
			</tr>
			<tr>
				<th>Name</th>
				<th>Image URL</th>
				<th>Processed</th>
				<th>Detected Eyes</th>
			</tr>
		</thead>
		<tbody>
			<?php for ( $i = 0; $i < 4; $i++ ) : ?>
				<?php $model = isset( $models[ $i ] ) ? $models[ $i ] : facepp_tryon_product_model_defaults(); ?>
				<tr>
					<td><input type="text" class="widefat" name="facepp_tryon_product[models][<?php echo esc_attr( $i ); ?>][name]" value="<?php echo esc_attr( $model['name'] ); ?>"></td>
					<td>
						<input type="url" class="widefat" id="facepp-model-url-<?php echo esc_attr( $i ); ?>" name="facepp_tryon_product[models][<?php echo esc_attr( $i ); ?>][source_url]" value="<?php echo esc_attr( $model['source_url'] ); ?>">
						<input type="hidden" id="facepp-model-id-<?php echo esc_attr( $i ); ?>" name="facepp_tryon_product[models][<?php echo esc_attr( $i ); ?>][attachment_id]" value="<?php echo esc_attr( $model['attachment_id'] ); ?>">
						<button type="button" class="button facepp-tryon-pick" data-target="#facepp-model-url-<?php echo esc_attr( $i ); ?>" data-target-id="#facepp-model-id-<?php echo esc_attr( $i ); ?>" style="margin-top:6px;">Choose image</button>
						<div
							class="facepp-admin-preview facepp-admin-preview--model"
							data-facepp-eyes="<?php echo esc_attr( wp_json_encode( $model['detectedEyes'] ) ); ?>"
							style="position:relative;margin-top:8px;width:140px;min-height:120px;border:1px solid #ddd;border-radius:6px;overflow:hidden;background:#fafafa;"
						>
							<?php if ( ! empty( $model['processed_url'] ) ) : ?>
								<img class="facepp-admin-preview__image" src="<?php echo esc_url( $model['processed_url'] ); ?>" alt="" style="width:100%;height:auto;display:block;">
							<?php elseif ( ! empty( $model['source_url'] ) ) : ?>
								<img class="facepp-admin-preview__image" src="<?php echo esc_url( $model['source_url'] ); ?>" alt="" style="width:100%;height:auto;display:block;">
							<?php endif; ?>
						</div>
					</td>
					<td>
						<?php if ( ! empty( $model['processed_url'] ) ) : ?>
							<a href="<?php echo esc_url( $model['processed_url'] ); ?>" target="_blank" rel="noreferrer">View cropped model</a>
						<?php else : ?>
							<span style="color:#666;">Will auto-crop portrait on save</span>
						<?php endif; ?>
					</td>
					<td>
						<?php if ( ! empty( $model['detectedEyes']['left_eye'] ) && ! empty( $model['detectedEyes']['right_eye'] ) ) : ?>
							<code>L(<?php echo esc_html( $model['detectedEyes']['left_eye']['x'] . ',' . $model['detectedEyes']['left_eye']['y'] ); ?>)</code><br>
							<code>R(<?php echo esc_html( $model['detectedEyes']['right_eye']['x'] . ',' . $model['detectedEyes']['right_eye']['y'] ); ?>)</code>
						<?php else : ?>
							<span style="color:#666;">Will detect on save if Face++ is available</span>
						<?php endif; ?>
					</td>
				</tr>
			<?php endfor; ?>
		</tbody>
	</table>
	<p style="margin-top:12px;color:#666;">Frame PNGs are trimmed on save to remove extra transparent area. Model images are cropped into a try-on portrait and saved with stored eye landmarks when Face++ detection succeeds.</p>
	<?php
}

function facepp_tryon_save_product_assets( $post_id, $post ) {
	if ( ! $post instanceof WP_Post || 'product' !== $post->post_type ) {
		return;
	}

	if ( ! isset( $_POST['facepp_tryon_product_assets_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['facepp_tryon_product_assets_nonce'] ) ), 'facepp_tryon_product_assets' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$payload = isset( $_POST['facepp_tryon_product'] ) && is_array( $_POST['facepp_tryon_product'] ) ? wp_unslash( $_POST['facepp_tryon_product'] ) : array();
	$frames  = array();
	$models  = array();
	$stored_frames = facepp_tryon_get_product_custom_frames( $post_id );
	$stored_models = facepp_tryon_get_product_custom_models( $post_id );

	if ( ! empty( $payload['frames'] ) && is_array( $payload['frames'] ) ) {
		foreach ( $payload['frames'] as $index => $entry ) {
			$frame = facepp_tryon_normalize_product_frame( $entry, (int) $index );

			if ( '' === $frame['source_url'] ) {
				continue;
			}

			if ( isset( $stored_frames[ $index ] ) && $stored_frames[ $index ]['source_url'] === $frame['source_url'] && '' !== $stored_frames[ $index ]['processed_url'] ) {
				$frame['processed_url'] = $stored_frames[ $index ]['processed_url'];
				$frame['thumbnail']     = $stored_frames[ $index ]['thumbnail'];
				$frames[]               = $frame;
				continue;
			}

			$frames[] = facepp_tryon_prepare_frame_asset( $post_id, (int) $index, $frame );
		}
	}

	if ( ! empty( $payload['models'] ) && is_array( $payload['models'] ) ) {
		foreach ( $payload['models'] as $index => $entry ) {
			$model = facepp_tryon_normalize_product_model( $entry, (int) $index );

			if ( '' === $model['source_url'] ) {
				continue;
			}

			if ( isset( $stored_models[ $index ] ) && $stored_models[ $index ]['source_url'] === $model['source_url'] && '' !== $stored_models[ $index ]['processed_url'] ) {
				$model['processed_url'] = $stored_models[ $index ]['processed_url'];
				$model['detectedEyes']  = $stored_models[ $index ]['detectedEyes'];
				$models[]               = $model;
				continue;
			}

			$models[] = facepp_tryon_prepare_model_asset( $post_id, (int) $index, $model );
		}
	}

	if ( empty( $frames ) ) {
		delete_post_meta( $post_id, FACEPP_TRYON_PRODUCT_FRAMES_META );
	} else {
		update_post_meta( $post_id, FACEPP_TRYON_PRODUCT_FRAMES_META, $frames );
	}

	if ( empty( $models ) ) {
		delete_post_meta( $post_id, FACEPP_TRYON_PRODUCT_MODELS_META );
	} else {
		update_post_meta( $post_id, FACEPP_TRYON_PRODUCT_MODELS_META, $models );
	}
}
add_action( 'save_post_product', 'facepp_tryon_save_product_assets', 10, 2 );

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
	$image_data = isset( $_POST['image'] ) ? (string) wp_unslash( $_POST['image'] ) : '';
	$image_url  = isset( $_POST['image_url'] ) ? esc_url_raw( (string) wp_unslash( $_POST['image_url'] ) ) : '';

	if ( ! preg_match( '/^data:image\/(?:png|jpe?g|webp);base64,/', $image_data ) && '' === $image_url ) {
		wp_send_json_error(
			array(
				'code'    => 'invalid_image',
				'message' => 'Invalid image payload.',
			),
			400
		);
	}

	$result = facepp_tryon_detect_face_result(
		array(
			'image_base64' => preg_match( '/^data:image\/(?:png|jpe?g|webp);base64,/', $image_data ) ? preg_replace( '/^data:image\/(?:png|jpe?g|webp);base64,/', '', $image_data ) : '',
			'image_url'    => $image_url,
		)
	);

	if ( is_wp_error( $result ) ) {
		wp_send_json_error(
			array(
				'code'    => $result->get_error_code(),
				'message' => $result->get_error_message(),
			),
			in_array( $result->get_error_code(), array( 'missing_credentials', 'invalid_image', 'facepp_error' ), true ) ? 400 : 502
		);
	}

	wp_send_json_success(
		array(
			'left_eye'  => array(
				'x' => (float) $result['left_eye']['x'],
				'y' => (float) $result['left_eye']['y'],
			),
			'right_eye' => array(
				'x' => (float) $result['right_eye']['x'],
				'y' => (float) $result['right_eye']['y'],
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
	$models        = facepp_tryon_get_models( $product_id );
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
