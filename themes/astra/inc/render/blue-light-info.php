<?php
/**
 * Muukal blue light info section markup.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return local blue light section image URLs.
 *
 * @return array<string, string>
 */
function muukal_blue_light_info_images() {
	return array(
		'hero'   => ASTRA_THEME_URI . 'assets/images/blue-light/b8.png',
		'strain' => ASTRA_THEME_URI . 'assets/images/blue-light/b9.png',
		'lenses' => ASTRA_THEME_URI . 'assets/images/blue-light/b5.png',
	);
}

/**
 * Render the blue light info section.
 *
 * @param array<string, mixed> $args Shortcode attributes.
 * @return string
 */
function muukal_render_blue_light_info( $args = array() ) {
	$images       = muukal_blue_light_info_images();
	$margin_top   = ! empty( $args['margin_top'] ) ? sanitize_text_field( $args['margin_top'] ) : '80px';
	$max_width    = ! empty( $args['max_width'] ) ? sanitize_text_field( $args['max_width'] ) : '1200px';
	$hero_heading = ! empty( $args['hero_heading'] ) ? sanitize_text_field( $args['hero_heading'] ) : 'What is Blue Light?';
	$strain_title = ! empty( $args['strain_title'] ) ? sanitize_text_field( $args['strain_title'] ) : 'Problems Caused by Blue Light: Digital Eye Strain';
	$lens_title   = ! empty( $args['lens_title'] ) ? sanitize_text_field( $args['lens_title'] ) : 'Why Choose Muukal Blue-light Blocking Lenses';

	ob_start();
	?>
	<section class="muukal-blue-light-info" style="--muukal-blue-light-mt: <?php echo esc_attr( $margin_top ); ?>;">
		<div class="muukal-blue-light-info__inner" style="--muukal-blue-light-max-width: <?php echo esc_attr( $max_width ); ?>;">
			<div class="muukal-blue-light-info__hero">
				<div class="muukal-blue-light-info__hero-copy">
					<h3><?php echo esc_html( $hero_heading ); ?></h3>
					<div class="muukal-blue-light-info__copy">
						<p>Blue light on the visible light spectrum is shorter in wavelength (380 - 500 nanometers), having the highest energy of all visible light. It&rsquo;s found in sunlight and necessary for keeping us energized and awake, also regulating our circadian rhythm. The issue arises though when blue light comes from artificial sources in large unwanted quantities.</p>
						<p>Both UV light and blue light can be harmful depending on the exposure amounts and damage overtime.</p>
					</div>
				</div>
				<div class="muukal-blue-light-info__hero-media">
					<img src="<?php echo esc_url( $images['hero'] ); ?>" alt="Blue light illustration" loading="lazy" decoding="async">
				</div>
			</div>

			<div class="muukal-blue-light-info__section">
				<h3><?php echo esc_html( $strain_title ); ?></h3>
				<div class="muukal-blue-light-info__copy">
					<p>If you&rsquo;re like us, you spend several hours a day in front of a computer screen or digital device. The problem is that it isn&apos;t natural for us to be continuously looking at a screen. Prolong use of electronic devices may lead to symptoms of digital eye strain also known as Computer Vision Syndrome. Symptoms often include eye strain, eye fatigue, headaches, blurred vision, dried eyes. The American Optometric Association estimates that 50%-90% of computer users suffer symptoms of Computer Vision Syndrome. Are you feeling some of these symptoms?</p>
				</div>
				<div class="muukal-blue-light-info__image-wrap">
					<img src="<?php echo esc_url( $images['strain'] ); ?>" alt="Digital eye strain symptoms" loading="lazy" decoding="async">
				</div>
			</div>

			<div class="muukal-blue-light-info__section">
				<h3><?php echo esc_html( $lens_title ); ?></h3>
				<div class="muukal-blue-light-info__copy">
					<p>To achieve a balance between the convenience technology brings and the harm it causes, Muukal offers the most advanced blue light blocking lenses. The lenses apply our core technology that improves the efficiency from protecting eyes from blue light. Muukal offers lenses with all the refractive indexes, including UV protection, anti-reflective and extra hardness qualities.</p>
				</div>
				<div class="muukal-blue-light-info__image-wrap">
					<img src="<?php echo esc_url( $images['lenses'] ); ?>" alt="Muukal blue-light blocking lenses" loading="lazy" decoding="async">
				</div>
			</div>
		</div>
	</section>
	<?php

	return ob_get_clean();
}
