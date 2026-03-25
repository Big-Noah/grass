<?php
/**
 * Plugin Name: Muukal Try On Prototype
 * Description: Standalone virtual try-on prototype with modal UI, upload-crop-align flow, preset models, manual eye points, and browser-side face landmark auto-alignment.
 * Version: 0.1.0
 * Author: Codex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MUUKAL_TRY_ON_VERSION', '0.1.0' );
define( 'MUUKAL_TRY_ON_FILE', __FILE__ );
define( 'MUUKAL_TRY_ON_DIR', plugin_dir_path( __FILE__ ) );
define( 'MUUKAL_TRY_ON_URL', plugin_dir_url( __FILE__ ) );

function muukal_try_on_default_settings() {
	return array(
		'overlay_image'       => '',
		'open_label'          => 'Open Try On Demo',
		'modal_title'         => 'Try On',
		'helper_text'         => 'Upload a portrait, crop it into a face-first frame, then auto align the glasses. If automatic eye detection misses, drag the two eye markers directly onto the pupils.',
		'auto_width_factor'   => '2.15',
		'auto_y_offset'       => '0.02',
		'model_1_label'       => 'Model 1',
		'model_1_image'       => '',
		'model_2_label'       => 'Model 2',
		'model_2_image'       => '',
		'model_3_label'       => 'Model 3',
		'model_3_image'       => '',
		'model_4_label'       => 'Model 4',
		'model_4_image'       => '',
	);
}

function muukal_try_on_get_settings() {
	return wp_parse_args( get_option( 'muukal_try_on_settings', array() ), muukal_try_on_default_settings() );
}

function muukal_try_on_get_models() {
	$settings = muukal_try_on_get_settings();
	$models   = array();

	for ( $i = 1; $i <= 4; $i++ ) {
		$image = isset( $settings[ 'model_' . $i . '_image' ] ) ? trim( (string) $settings[ 'model_' . $i . '_image' ] ) : '';
		$label = isset( $settings[ 'model_' . $i . '_label' ] ) ? trim( (string) $settings[ 'model_' . $i . '_label' ] ) : '';

		if ( '' === $image ) {
			continue;
		}

		$models[] = array(
			'label' => '' !== $label ? $label : 'Model ' . $i,
			'image' => esc_url_raw( $image ),
		);
	}

	return $models;
}

function muukal_try_on_sanitize_settings( $input ) {
	$defaults = muukal_try_on_default_settings();
	$output   = array();

	foreach ( $defaults as $key => $value ) {
		$raw = isset( $input[ $key ] ) ? $input[ $key ] : $value;

		if ( false !== strpos( $key, '_image' ) || 'overlay_image' === $key ) {
			$output[ $key ] = esc_url_raw( trim( (string) $raw ) );
			continue;
		}

		if ( in_array( $key, array( 'auto_width_factor', 'auto_y_offset' ), true ) ) {
			$output[ $key ] = (string) (float) $raw;
			continue;
		}

		$output[ $key ] = sanitize_text_field( (string) $raw );
	}

	return $output;
}

function muukal_try_on_register_settings() {
	register_setting(
		'muukal_try_on_settings_group',
		'muukal_try_on_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'muukal_try_on_sanitize_settings',
			'default'           => muukal_try_on_default_settings(),
		)
	);
}
add_action( 'admin_init', 'muukal_try_on_register_settings' );

function muukal_try_on_admin_menu() {
	add_menu_page(
		'Try On Demo',
		'Try On Demo',
		'manage_options',
		'muukal-try-on-demo',
		'muukal_try_on_render_preview_page',
		'dashicons-format-image',
		58
	);

	add_submenu_page(
		'muukal-try-on-demo',
		'Try On Demo',
		'Preview',
		'manage_options',
		'muukal-try-on-demo',
		'muukal_try_on_render_preview_page'
	);

	add_submenu_page(
		'muukal-try-on-demo',
		'Try On Settings',
		'Settings',
		'manage_options',
		'muukal-try-on-settings',
		'muukal_try_on_render_settings_page'
	);
}
add_action( 'admin_menu', 'muukal_try_on_admin_menu' );

function muukal_try_on_admin_assets( $hook ) {
	if ( false === strpos( $hook, 'muukal-try-on' ) ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style( 'muukal-try-on-style', MUUKAL_TRY_ON_URL . 'assets/try-on.css', array(), MUUKAL_TRY_ON_VERSION );
	wp_enqueue_script( 'muukal-try-on-media', MUUKAL_TRY_ON_URL . 'assets/admin-media.js', array( 'jquery' ), MUUKAL_TRY_ON_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'muukal_try_on_admin_assets' );

function muukal_try_on_register_assets() {
	wp_register_style( 'muukal-try-on-style', MUUKAL_TRY_ON_URL . 'assets/try-on.css', array(), MUUKAL_TRY_ON_VERSION );
	wp_register_script( 'muukal-try-on-vision', 'https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision/vision_bundle.js', array(), null, true );
	wp_register_script( 'muukal-try-on-script', MUUKAL_TRY_ON_URL . 'assets/try-on.js', array( 'muukal-try-on-vision' ), MUUKAL_TRY_ON_VERSION, true );
}
add_action( 'init', 'muukal_try_on_register_assets' );

function muukal_try_on_enqueue_frontend_assets() {
	$settings = muukal_try_on_get_settings();
	$config   = array(
		'models'           => muukal_try_on_get_models(),
		'overlayImage'     => esc_url_raw( $settings['overlay_image'] ),
		'modalTitle'       => $settings['modal_title'],
		'helperText'       => $settings['helper_text'],
		'autoWidthFactor'  => (float) $settings['auto_width_factor'],
		'autoYOffset'      => (float) $settings['auto_y_offset'],
		'wasmBase'         => 'https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision/wasm',
		'modelAssetPath'   => 'https://storage.googleapis.com/mediapipe-models/face_landmarker/face_landmarker/float16/1/face_landmarker.task',
		'i18n'             => array(
			'loading'         => 'Loading face landmarks...',
			'ready'           => 'Ready for auto alignment.',
			'detecting'       => 'Detecting face and aligning glasses...',
			'noFace'          => 'No face detected. Try a clearer front-facing photo or adjust manually.',
			'alignFailed'     => 'Auto alignment is unavailable right now. Manual controls still work.',
			'overlayMissing'  => 'Set a transparent glasses image in Try On Settings first.',
			'imageReady'      => 'Image loaded. Run auto align or fine-tune manually.',
			'modelSelected'   => 'Preset model selected.',
			'uploadSelected'  => 'Uploaded photo loaded.',
			'manualEyes'      => 'Drag the two eye markers onto the center of each eye.',
			'manualApplied'   => 'Manual eye points applied. Fine-tune with drag or sliders if needed.',
			'cropPhoto'       => 'Crop the portrait so the face fills the frame before aligning.',
			'cropApplied'     => 'Photo cropped. Run auto align or place the eye markers manually.',
			'cropNeeded'      => 'Upload and crop a photo first.',
		),
	);

	wp_enqueue_style( 'muukal-try-on-style' );
	wp_enqueue_script( 'muukal-try-on-vision' );
	wp_enqueue_script( 'muukal-try-on-script' );
	wp_localize_script( 'muukal-try-on-script', 'muukalTryOnConfig', $config );
}

function muukal_try_on_shortcode( $atts = array() ) {
	muukal_try_on_enqueue_frontend_assets();
	$settings = muukal_try_on_get_settings();

	ob_start();
	?>
	<div class="muukal-try-on-app" data-open-label="<?php echo esc_attr( $settings['open_label'] ); ?>">
		<button type="button" class="muukal-try-on-open button button-primary">
			<?php echo esc_html( $settings['open_label'] ); ?>
		</button>

		<div class="muukal-try-on-modal" hidden>
			<div class="muukal-try-on-backdrop" data-close-modal></div>
			<div class="muukal-try-on-dialog" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( $settings['modal_title'] ); ?>">
				<button type="button" class="muukal-try-on-close" data-close-modal aria-label="Close">&times;</button>
				<div class="muukal-try-on-shell">
					<div class="muukal-try-on-stage-panel">
						<div class="muukal-try-on-stage">
							<div class="muukal-try-on-cropper" hidden>
								<div class="muukal-try-on-crop-frame">
									<img class="muukal-try-on-crop-image" alt="" />
									<div class="muukal-try-on-crop-mask"></div>
								</div>
							</div>
							<img class="muukal-try-on-base" alt="" />
							<img class="muukal-try-on-overlay" alt="" />
							<div class="muukal-try-on-eye-layer" hidden>
								<button type="button" class="muukal-try-on-eye-marker" data-eye="left" aria-label="Left eye marker">
									<span></span>
								</button>
								<button type="button" class="muukal-try-on-eye-marker" data-eye="right" aria-label="Right eye marker">
									<span></span>
								</button>
							</div>
							<div class="muukal-try-on-empty">
								<p>Upload a portrait or choose a preset model.</p>
							</div>
						</div>
					</div>
					<div class="muukal-try-on-controls">
						<div class="muukal-try-on-block">
							<h3><?php echo esc_html( $settings['modal_title'] ); ?></h3>
							<p class="muukal-try-on-helper"><?php echo esc_html( $settings['helper_text'] ); ?></p>
							<p class="muukal-try-on-status" aria-live="polite"></p>
						</div>

						<div class="muukal-try-on-block">
							<label class="muukal-try-on-upload">
								<span>Upload your picture</span>
								<input type="file" accept="image/*" class="muukal-try-on-file" />
							</label>
							<button type="button" class="button muukal-try-on-apply-crop">Apply Photo Crop</button>
							<button type="button" class="button muukal-try-on-auto-align">Auto Align</button>
							<button type="button" class="button muukal-try-on-manual-eyes">Set Eyes Manually</button>
							<button type="button" class="button muukal-try-on-reset">Reset Adjustments</button>
						</div>

						<div class="muukal-try-on-block">
							<h4>Photo crop</h4>
							<div class="muukal-try-on-grid">
								<label>
									<span>Zoom</span>
									<input type="range" min="1" max="3" value="1.35" step="0.01" data-crop-control="zoom" />
								</label>
								<label>
									<span>Pan X</span>
									<input type="range" min="-220" max="220" value="0" step="1" data-crop-control="x" />
								</label>
								<label>
									<span>Pan Y</span>
									<input type="range" min="-260" max="260" value="0" step="1" data-crop-control="y" />
								</label>
							</div>
							<p class="muukal-try-on-note">This mirrors Muukal&apos;s flow: crop the portrait first, then run eye alignment on the cleaned face area.</p>
						</div>

						<?php if ( muukal_try_on_get_models() ) : ?>
							<div class="muukal-try-on-block">
								<h4>Preset models</h4>
								<div class="muukal-try-on-models">
									<?php foreach ( muukal_try_on_get_models() as $model ) : ?>
										<button
											type="button"
											class="muukal-try-on-model"
											data-model-image="<?php echo esc_url( $model['image'] ); ?>"
											data-model-label="<?php echo esc_attr( $model['label'] ); ?>"
										>
											<img src="<?php echo esc_url( $model['image'] ); ?>" alt="<?php echo esc_attr( $model['label'] ); ?>" />
											<span><?php echo esc_html( $model['label'] ); ?></span>
										</button>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>

						<div class="muukal-try-on-block">
							<h4>Manual adjustments</h4>
							<div class="muukal-try-on-grid">
								<label>
									<span>Move X</span>
									<input type="range" min="-180" max="180" value="0" step="1" data-control="manualX" />
								</label>
								<label>
									<span>Move Y</span>
									<input type="range" min="-180" max="180" value="0" step="1" data-control="manualY" />
								</label>
								<label>
									<span>Scale</span>
									<input type="range" min="0.4" max="2.4" value="1" step="0.01" data-control="manualScale" />
								</label>
								<label>
									<span>Rotate</span>
									<input type="range" min="-30" max="30" value="0" step="0.1" data-control="manualRotate" />
								</label>
							</div>
							<p class="muukal-try-on-note">Tip: drag the glasses directly on the photo for quick X/Y adjustment.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php

	return ob_get_clean();
}
add_shortcode( 'muukal_try_on_demo', 'muukal_try_on_shortcode' );

function muukal_try_on_render_preview_page() {
	echo '<div class="wrap">';
	echo '<h1>Try On Demo</h1>';
	echo '<p>Use this preview page to test the modal before wiring it into the product page. The same UI is also available via shortcode <code>[muukal_try_on_demo]</code>.</p>';
	echo do_shortcode( '[muukal_try_on_demo]' );
	echo '</div>';
}

function muukal_try_on_settings_field( $key, $label, $type = 'text', $button = false ) {
	$settings = muukal_try_on_get_settings();
	$value    = isset( $settings[ $key ] ) ? $settings[ $key ] : '';
	?>
	<tr>
		<th scope="row"><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
		<td>
			<?php if ( 'textarea' === $type ) : ?>
				<textarea class="large-text" rows="3" id="<?php echo esc_attr( $key ); ?>" name="muukal_try_on_settings[<?php echo esc_attr( $key ); ?>]"><?php echo esc_textarea( $value ); ?></textarea>
			<?php else : ?>
				<input class="regular-text" type="<?php echo esc_attr( $type ); ?>" id="<?php echo esc_attr( $key ); ?>" name="muukal_try_on_settings[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>" />
			<?php endif; ?>
			<?php if ( $button ) : ?>
				<button type="button" class="button muukal-try-on-pick-media" data-target="#<?php echo esc_attr( $key ); ?>">Choose image</button>
			<?php endif; ?>
		</td>
	</tr>
	<?php
}

function muukal_try_on_render_settings_page() {
	?>
	<div class="wrap">
		<h1>Try On Settings</h1>
		<p>Set one transparent glasses image and up to four preset model photos for the prototype.</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'muukal_try_on_settings_group' ); ?>
			<table class="form-table" role="presentation">
				<tbody>
					<?php muukal_try_on_settings_field( 'overlay_image', 'Glasses overlay image URL', 'url', true ); ?>
					<?php muukal_try_on_settings_field( 'open_label', 'Open button label' ); ?>
					<?php muukal_try_on_settings_field( 'modal_title', 'Modal title' ); ?>
					<?php muukal_try_on_settings_field( 'helper_text', 'Helper text', 'textarea' ); ?>
					<?php muukal_try_on_settings_field( 'auto_width_factor', 'Auto width factor', 'number' ); ?>
					<?php muukal_try_on_settings_field( 'auto_y_offset', 'Auto Y offset', 'number' ); ?>
					<?php
					for ( $i = 1; $i <= 4; $i++ ) {
						muukal_try_on_settings_field( 'model_' . $i . '_label', 'Model ' . $i . ' label' );
						muukal_try_on_settings_field( 'model_' . $i . '_image', 'Model ' . $i . ' image URL', 'url', true );
					}
					?>
				</tbody>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
