<?php
/**
 * Muukal progressive info section markup.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return local progressive info image URLs.
 *
 * @return array<string, string>
 */
function muukal_progressive_info_images() {
	return array(
		'premium_image'   => ASTRA_THEME_URI . 'assets/images/progressive/premium-progressive.jpg',
		'progressive'     => ASTRA_THEME_URI . 'assets/images/progressive/progressive.jpg',
		'bifocal'         => ASTRA_THEME_URI . 'assets/images/progressive/bifocal.jpg',
		'premium_icon'    => ASTRA_THEME_URI . 'assets/images/progressive/pp-icon.png',
		'progressive_icon'=> ASTRA_THEME_URI . 'assets/images/progressive/p-icon.png',
		'bifocal_icon'    => ASTRA_THEME_URI . 'assets/images/progressive/b-icon.png',
		'feature_sprite'  => ASTRA_THEME_URI . 'assets/images/progressive/icon-lense-n.png',
	);
}

/**
 * Render the progressive info section.
 *
 * @param array<string, mixed> $args Shortcode attributes.
 * @return string
 */
function muukal_render_progressive_info( $args = array() ) {
	$images      = muukal_progressive_info_images();
	$margin_top  = ! empty( $args['margin_top'] ) ? sanitize_text_field( $args['margin_top'] ) : '50px';
	$max_width   = ! empty( $args['max_width'] ) ? sanitize_text_field( $args['max_width'] ) : '1200px';
	$section_id  = ! empty( $args['section_id'] ) ? sanitize_html_class( $args['section_id'] ) : 'muukal-progressive-info';
	$tabs        = array(
		array(
			'id'          => 'premium',
			'label'       => 'Premium Progressive',
			'title'       => 'Premium Progressive Lenses',
			'description' => 'With improved peripheral vision and reading clarity, they let you enjoy a larger and sharper field of vision than with standard multifocals. Plus - thanks to newly designed vision corridors, first time users can adapt quicker than ever.',
			'image'       => $images['premium_image'],
			'icon'        => $images['premium_icon'],
		),
		array(
			'id'          => 'progressive',
			'label'       => 'Progressive',
			'title'       => 'Progressive Lenses',
			'description' => 'Progressive lenses are line-free multi-focals that have a seamless progression of added magnifying power for intermediate and near vision. The power of progressive lenses changes gradually from point to point on the lens surface, providing the correct lens power for seeing objects clearly at virtually any distance.',
			'image'       => $images['progressive'],
			'icon'        => $images['progressive_icon'],
		),
		array(
			'id'          => 'bifocal',
			'label'       => 'Bifocal',
			'title'       => 'Bifocal Lenses',
			'description' => 'The traditional bifocals are two lenses in one, including both near and far distances, with a visible segment line that separates the two. The upper section of the lens includes the distance prescription, while the lower section of the lens includes a nearer reading prescription.',
			'image'       => $images['bifocal'],
			'icon'        => $images['bifocal_icon'],
		),
	);
	$feature_rows = array(
		array(
			array(
				'label' => 'Clear',
				'class' => 'clear',
			),
			array(
				'label' => 'Blue Light Blocking',
				'class' => 'digital',
			),
		),
		array(
			array(
				'label' => 'Light Adjusting',
				'class' => 'sun',
			),
			array(
				'label' => 'Sun',
				'class' => 'adjusting',
			),
		),
	);
	$steps        = array(
		'Look straight ahead and make sure that your vision is clear.',
		'Pick up a book or magazine. Look down your lens and point your nose at the text.',
		'Extend the printed material out to where you normally read and gently move your head up and down. You should notice a smooth transition between all distances.',
		'Now, move the material without turning your head and notice that the print is less sharp. Point your nose in the direction of the material, raise your chin up slowly, and see the print glide into focus.',
	);

	ob_start();
	?>
	<section
		id="<?php echo esc_attr( $section_id ); ?>"
		class="muukal-progressive-info"
		style="--muukal-progressive-mt: <?php echo esc_attr( $margin_top ); ?>; --muukal-progressive-max-width: <?php echo esc_attr( $max_width ); ?>; --muukal-progressive-sprite: url('<?php echo esc_url( $images['feature_sprite'] ); ?>');"
		data-progressive-info
	>
		<div class="muukal-progressive-info__container">
			<div class="muukal-progressive-info__intro">
				<h3 class="muukal-progressive-info__title">What are Progressive Lenses?</h3>
				<div class="muukal-progressive-info__body">
					<p>If you&apos;re over age 40 and struggling to see small print with your current glasses, you probably need multifocal lenses. No worries - that doesn&apos;t mean you have to wear ugly bifocals or trifocals. For most people, line-free progressive lenses are a much better option.</p>
					<p>These progressive lenses will provide everything that you need in a pair of glasses - you&apos;ll never have to switch back and forth between reading and distance glasses. Unlike bifocal lenses, progressive lenses have no line and are optimized for not only reading and distance, but also for a third intermediate distance, such as your computer screen. The digital lens allows for a wider intermediate corridor that minimizes peripheral distortion.</p>
				</div>
			</div>
		</div>

		<div class="muukal-progressive-info__compare-wrap">
			<div class="muukal-progressive-info__compare">
				<div class="muukal-progressive-info__panels">
					<?php foreach ( $tabs as $index => $tab ) : ?>
						<article
							id="<?php echo esc_attr( $section_id . '-panel-' . $tab['id'] ); ?>"
							class="muukal-progressive-info__panel<?php echo 0 === $index ? ' is-active' : ''; ?>"
							role="tabpanel"
							aria-labelledby="<?php echo esc_attr( $section_id . '-tab-' . $tab['id'] ); ?>"
							<?php echo 0 === $index ? '' : 'hidden'; ?>
						>
							<div class="muukal-progressive-info__panel-copy">
								<h3 class="muukal-progressive-info__title"><?php echo esc_html( $tab['title'] ); ?></h3>
								<p><?php echo esc_html( $tab['description'] ); ?></p>
							</div>
							<div class="muukal-progressive-info__panel-media">
								<img src="<?php echo esc_url( $tab['image'] ); ?>" alt="<?php echo esc_attr( $tab['title'] ); ?>" loading="lazy" decoding="async">
							</div>
						</article>
					<?php endforeach; ?>
				</div>

				<div class="muukal-progressive-info__tablist" role="tablist" aria-label="Progressive lens comparison">
					<?php foreach ( $tabs as $index => $tab ) : ?>
						<button
							id="<?php echo esc_attr( $section_id . '-tab-' . $tab['id'] ); ?>"
							class="muukal-progressive-info__tab<?php echo 0 === $index ? ' is-active' : ''; ?>"
							type="button"
							role="tab"
							aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
							aria-controls="<?php echo esc_attr( $section_id . '-panel-' . $tab['id'] ); ?>"
							data-progressive-tab
						>
							<img src="<?php echo esc_url( $tab['icon'] ); ?>" alt="" loading="lazy" decoding="async">
							<span><?php echo esc_html( $tab['label'] ); ?></span>
						</button>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<div class="muukal-progressive-info__container">
			<div class="muukal-progressive-info__intro">
				<h3 class="muukal-progressive-info__title">Steps to Adapting to Progressive Lenses</h3>
				<div class="muukal-progressive-info__body">
					<p>While adapting point your nose at the object you are looking at rather than moving your head from side to side. It is normal at the start for objects to the side to seem out of focus; as you adjust the blurred sensation will disappear.</p>
					<ol class="muukal-progressive-info__steps">
						<?php foreach ( $steps as $step ) : ?>
							<li><?php echo esc_html( $step ); ?></li>
						<?php endforeach; ?>
					</ol>
					<p>By following these steps you will soon be reading, using your computer, watching TV, and driving with the ultimate crystal clarity.</p>
				</div>
			</div>
		</div>

		<div class="muukal-progressive-info__features-wrap">
			<div class="muukal-progressive-info__features">
				<div class="muukal-progressive-info__feature-column">
					<h3 class="muukal-progressive-info__feature-title">Custom Features</h3>
					<p class="muukal-progressive-info__feature-copy">Take your progressive lenses to the next level by customizing them to fit your everyday needs. Our options include:</p>
					<div class="muukal-progressive-info__feature-grid">
						<?php foreach ( $feature_rows as $row ) : ?>
							<div class="muukal-progressive-info__feature-list">
								<?php foreach ( $row as $feature ) : ?>
									<div class="muukal-progressive-info__feature-item">
										<span class="muukal-progressive-info__feature-icon muukal-progressive-info__feature-icon--<?php echo esc_attr( $feature['class'] ); ?>" aria-hidden="true"></span>
										<span><?php echo esc_html( $feature['label'] ); ?></span>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="muukal-progressive-info__howto">
					<h3 class="muukal-progressive-info__feature-title">How can you get them?</h3>
					<ol class="muukal-progressive-info__method-list">
						<li>
							<strong>Step 1:</strong>
							<p>Select any style option below</p>
						</li>
						<li>
							<strong>Step 2:</strong>
							<p>Select &ldquo;premium progressive&rdquo; or &ldquo;progressive&rdquo; lenses at lens selection</p>
						</li>
						<li>
							<strong>Step 3:</strong>
							<p>Finish your purchase and enjoy!</p>
						</li>
					</ol>
				</div>
			</div>
		</div>
	</section>
	<?php

	return ob_get_clean();
}
