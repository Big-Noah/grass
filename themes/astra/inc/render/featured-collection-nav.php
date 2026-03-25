<?php
/**
 * Muukal featured collection nav markup.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get default featured collection items.
 *
 * @return array<string, string>
 */
function muukal_featured_collection_nav_default_items() {
	return array(
		'new-arrivals'  => 'NEW IN',
		'cat-eye'       => 'CAT-EYE',
		'rectangle'     => 'RECTANGLE',
		'spring-hinge'  => 'SPRING HINGES',
		'rimless'       => 'RIMLESS',
		'tortoise'      => 'TORTOISE',
		'clip-on'       => 'CLIP-ON',
	);
}

/**
 * Build shortcode item definitions from the user-provided list.
 *
 * Supported formats:
 * - slug
 * - slug:LABEL
 *
 * @param string $items Raw items attribute.
 * @return array<int, array<string, string>>
 */
function muukal_featured_collection_nav_build_items( $items ) {
	$defaults = muukal_featured_collection_nav_default_items();
	$entries  = array_filter( array_map( 'trim', explode( ',', (string) $items ) ) );
	$built    = array();

	foreach ( $entries as $entry ) {
		$parts = array_map( 'trim', explode( ':', $entry, 2 ) );
		$slug  = sanitize_title( $parts[0] );

		if ( '' === $slug ) {
			continue;
		}

		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! ( $term instanceof WP_Term ) ) {
			continue;
		}

		$label = isset( $parts[1] ) && '' !== $parts[1]
			? sanitize_text_field( $parts[1] )
			: ( $defaults[ $slug ] ?? strtoupper( $term->name ) );

		$built[] = array(
			'slug'  => $slug,
			'label' => $label,
			'url'   => get_term_link( $term ),
			'id'    => (string) $term->term_id,
		);
	}

	return array_filter(
		$built,
		static function ( $item ) {
			return ! is_wp_error( $item['url'] );
		}
	);
}

/**
 * Render the featured collection nav.
 *
 * @param array $args Shortcode attributes.
 * @return string
 */
function muukal_render_featured_collection_nav( $args ) {
	$default_items = implode( ',', array_keys( muukal_featured_collection_nav_default_items() ) );
	$items         = muukal_featured_collection_nav_build_items( $args['items'] ?? $default_items );

	if ( empty( $items ) ) {
		return '';
	}

	$current_term    = get_queried_object();
	$current_term_id = ( $current_term instanceof WP_Term && 'product_cat' === $current_term->taxonomy ) ? (int) $current_term->term_id : 0;
	$recommend_label = sanitize_text_field( $args['recommend_label'] ?? 'RECOMMEND' );
	$recommend_url   = '';

	if ( ! empty( $args['recommend_url'] ) ) {
		$recommend_url = esc_url_raw( $args['recommend_url'] );
	} elseif ( function_exists( 'wc_get_page_permalink' ) ) {
		$recommend_url = wc_get_page_permalink( 'shop' );
	}

	ob_start();
	?>
	<div class="product-tab mb-25 muukal-featured-collection-nav">
		<ul class="nav product-nav" role="tablist" aria-label="<?php echo esc_attr__( 'Featured collections', 'astra' ); ?>">
			<li class="nav-item">
				<?php if ( '' !== $recommend_url ) : ?>
					<a class="nav-link recommend<?php echo 0 === $current_term_id ? ' is-active' : ''; ?>" href="<?php echo esc_url( $recommend_url ); ?>">
						<?php echo esc_html( $recommend_label ); ?>
					</a>
				<?php else : ?>
					<span class="nav-link recommend is-active">
						<?php echo esc_html( $recommend_label ); ?>
					</span>
				<?php endif; ?>
			</li>
			<?php foreach ( $items as $item ) : ?>
				<li class="nav-item">
					<a class="nav-link<?php echo $current_term_id === (int) $item['id'] ? ' is-active' : ''; ?>" href="<?php echo esc_url( $item['url'] ); ?>">
						<?php echo esc_html( $item['label'] ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php

	return ob_get_clean();
}
