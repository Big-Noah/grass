<?php
/**
 * Muukal product filter archive renderer.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get supported filter configuration.
 *
 * @return array<string, array<string, string>>
 */
function muukal_product_filter_archive_get_filter_config() {
	return array(
		'gender'   => array(
			'label'    => __( 'Gender', 'astra' ),
			'taxonomy' => 'pa_gender',
		),
		'shape'    => array(
			'label'    => __( 'Shape', 'astra' ),
			'taxonomy' => 'pa_shape',
		),
		'style'    => array(
			'label'    => __( 'Style', 'astra' ),
			'taxonomy' => 'pa_style',
		),
		'color'    => array(
			'label'    => __( 'Color', 'astra' ),
			'taxonomy' => 'pa_color',
		),
		'material' => array(
			'label'    => __( 'Material', 'astra' ),
			'taxonomy' => 'pa_material',
		),
		'size'     => array(
			'label'    => __( 'Size', 'astra' ),
			'taxonomy' => 'pa_size',
		),
		'feature'  => array(
			'label'    => __( 'Feature', 'astra' ),
			'taxonomy' => 'pa_feature',
		),
	);
}

/**
 * Normalize selected slug values from query vars.
 *
 * @param string $key Filter key.
 * @return array<int, string>
 */
function muukal_product_filter_archive_get_selected_terms( $key ) {
	if ( empty( $_GET[ $key ] ) ) {
		return array();
	}

	$value = wp_unslash( $_GET[ $key ] );
	$items = is_array( $value ) ? $value : explode( ',', (string) $value );
	$items = array_map( 'sanitize_title', $items );
	$items = array_filter( array_unique( $items ) );

	return array_values( $items );
}

/**
 * Get effective selected terms from query vars or shortcode defaults.
 *
 * @param string $key  Filter key.
 * @param array  $atts Shortcode attributes.
 * @return array<int, string>
 */
function muukal_product_filter_archive_get_effective_selected_terms( $key, $atts ) {
	$selected = muukal_product_filter_archive_get_selected_terms( $key );

	if ( ! empty( $selected ) ) {
		return $selected;
	}

	if ( empty( $atts[ 'default_' . $key ] ) ) {
		return array();
	}

	return array_values(
		array_filter(
			array_map(
				'sanitize_title',
				explode( ',', $atts[ 'default_' . $key ] )
			)
		)
	);
}

/**
 * Get available terms for a taxonomy.
 *
 * @param string $taxonomy Taxonomy name.
 * @return array<int, WP_Term>
 */
function muukal_product_filter_archive_get_terms( $taxonomy ) {
	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'orderby'    => 'menu_order',
			'order'      => 'ASC',
		)
	);

	if ( is_wp_error( $terms ) ) {
		return array();
	}

	return $terms;
}

/**
 * Build query args for filtered product archive.
 *
 * @param array $atts Shortcode attributes.
 * @return array
 */
function muukal_product_filter_archive_build_query_args( $atts ) {
	$filter_config = muukal_product_filter_archive_get_filter_config();
	$tax_query     = array();
	$meta_query    = array();
	$posts_per_page = isset( $atts['per_page'] ) ? max( 1, absint( $atts['per_page'] ) ) : 12;
	$current_page   = isset( $_GET['mu_page'] ) ? max( 1, absint( $_GET['mu_page'] ) ) : 1;
	$sort           = isset( $_GET['sort_by'] ) ? sanitize_key( wp_unslash( $_GET['sort_by'] ) ) : sanitize_key( $atts['sort_by'] );
	$min_price      = isset( $_GET['min_price'] ) ? wc_format_decimal( wp_unslash( $_GET['min_price'] ) ) : '';
	$max_price      = isset( $_GET['max_price'] ) ? wc_format_decimal( wp_unslash( $_GET['max_price'] ) ) : '';

	foreach ( $filter_config as $key => $config ) {
		$selected = muukal_product_filter_archive_get_selected_terms( $key );

		if ( empty( $selected ) ) {
			$selected = muukal_product_filter_archive_get_effective_selected_terms( $key, $atts );
		}

		if ( empty( $selected ) ) {
			continue;
		}

		$tax_query[] = array(
			'taxonomy' => $config['taxonomy'],
			'field'    => 'slug',
			'terms'    => $selected,
		);
	}

	if ( ! empty( $atts['category'] ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => array( sanitize_title( $atts['category'] ) ),
		);
	}

	if ( '' !== $min_price || '' !== $max_price ) {
		$price_query = array(
			'key'     => '_price',
			'type'    => 'DECIMAL',
			'compare' => 'BETWEEN',
			'value'   => array(
				'' !== $min_price ? (float) $min_price : 0,
				'' !== $max_price ? (float) $max_price : 999999,
			),
		);

		$meta_query[] = $price_query;
	}

	$query_args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'paged'          => $current_page,
		'posts_per_page' => $posts_per_page,
	);

	if ( ! empty( $tax_query ) ) {
		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = 'AND';
		}

		$query_args['tax_query'] = $tax_query;
	}

	if ( ! empty( $meta_query ) ) {
		$query_args['meta_query'] = $meta_query;
	}

	switch ( $sort ) {
		case 'newest':
			$query_args['orderby'] = 'date';
			$query_args['order']   = 'DESC';
			break;
		case 'price_high':
			$query_args['meta_key'] = '_price';
			$query_args['orderby']  = 'meta_value_num';
			$query_args['order']    = 'DESC';
			break;
		case 'price_low':
			$query_args['meta_key'] = '_price';
			$query_args['orderby']  = 'meta_value_num';
			$query_args['order']    = 'ASC';
			break;
		case 'title':
			$query_args['orderby'] = 'title';
			$query_args['order']   = 'ASC';
			break;
		default:
			$query_args['orderby'] = 'menu_order title';
			$query_args['order']   = 'ASC';
			break;
	}

	return $query_args;
}

/**
 * Render filter group dropdown.
 *
 * @param string $key    Filter key.
 * @param array  $config Filter config.
 * @param array  $atts   Shortcode attributes.
 * @return void
 */
function muukal_product_filter_archive_render_filter_group( $key, $config, $atts ) {
	$selected = muukal_product_filter_archive_get_effective_selected_terms( $key, $atts );
	$terms    = muukal_product_filter_archive_get_terms( $config['taxonomy'] );
	$summary  = $config['label'];

	if ( ! empty( $selected ) ) {
		$summary .= ': ' . count( $selected );
	}

	?>
	<details class="muukal-filter-group">
		<summary class="muukal-filter-summary"><?php echo esc_html( $summary ); ?></summary>
		<div class="muukal-filter-panel">
			<?php foreach ( $terms as $term ) : ?>
				<label class="muukal-filter-option">
					<input type="checkbox" name="<?php echo esc_attr( $key ); ?>[]" value="<?php echo esc_attr( $term->slug ); ?>"<?php checked( in_array( $term->slug, $selected, true ) ); ?>>
					<span><?php echo esc_html( $term->name ); ?></span>
				</label>
			<?php endforeach; ?>
		</div>
	</details>
	<?php
}

/**
 * Render sort group dropdown.
 *
 * @param string $sort_by Current sort key.
 * @return void
 */
function muukal_product_filter_archive_render_sort_group( $sort_by ) {
	$options = muukal_product_filter_archive_get_sort_options();

	$current_label = isset( $options[ $sort_by ] ) ? $options[ $sort_by ] : $options['recommended'];
	?>
	<details class="muukal-filter-group muukal-filter-group-sort">
		<summary class="muukal-filter-summary"><?php echo esc_html( sprintf( 'Sort By: %s', $current_label ) ); ?></summary>
		<div class="muukal-filter-panel">
			<?php foreach ( $options as $value => $label ) : ?>
				<label class="muukal-filter-option">
					<input type="radio" name="sort_by" value="<?php echo esc_attr( $value ); ?>"<?php checked( $sort_by, $value ); ?>>
					<span><?php echo esc_html( $label ); ?></span>
				</label>
			<?php endforeach; ?>
		</div>
	</details>
	<?php
}

/**
 * Get sort options.
 *
 * @return array<string, string>
 */
function muukal_product_filter_archive_get_sort_options() {
	return array(
		'recommended' => __( 'Recommended', 'astra' ),
		'newest'      => __( 'Newest Arrivals', 'astra' ),
		'price_high'  => __( 'Price: High to Low', 'astra' ),
		'price_low'   => __( 'Price: Low to High', 'astra' ),
		'title'       => __( 'Title', 'astra' ),
	);
}

/**
 * Render selected filter chips.
 *
 * @param array  $atts   Shortcode attributes.
 * @param string $sort_by Current sort key.
 * @return void
 */
function muukal_product_filter_archive_render_selected_filters( $atts, $sort_by ) {
	$filter_config = muukal_product_filter_archive_get_filter_config();
	$sort_options  = muukal_product_filter_archive_get_sort_options();
	$has_filters   = false;

	ob_start();

	foreach ( $filter_config as $key => $config ) {
		$selected = muukal_product_filter_archive_get_effective_selected_terms( $key, $atts );

		foreach ( $selected as $slug ) {
			$term = get_term_by( 'slug', $slug, $config['taxonomy'] );

			if ( ! $term || is_wp_error( $term ) ) {
				continue;
			}

			$has_filters = true;
			?>
			<span class="muukal-selected-filter"><?php echo esc_html( $config['label'] . ': ' . $term->name ); ?></span>
			<?php
		}
	}

	if ( ! empty( $_GET['min_price'] ) || ! empty( $_GET['max_price'] ) ) {
		$has_filters = true;
		?>
		<span class="muukal-selected-filter">
			<?php
			echo esc_html(
				sprintf(
					'Price: %s - %s',
					'' !== wp_unslash( $_GET['min_price'] ) ? wp_unslash( $_GET['min_price'] ) : '0',
					'' !== wp_unslash( $_GET['max_price'] ) ? wp_unslash( $_GET['max_price'] ) : 'Any'
				)
			);
			?>
		</span>
		<?php
	}

	if ( $sort_by && 'recommended' !== $sort_by ) {
		$has_filters = true;
		?>
		<span class="muukal-selected-filter"><?php echo esc_html( sprintf( 'Sort: %s', isset( $sort_options[ $sort_by ] ) ? $sort_options[ $sort_by ] : $sort_by ) ); ?></span>
		<?php
	}

	$content = trim( ob_get_clean() );

	if ( ! $has_filters || '' === $content ) {
		return;
	}
	?>
	<div class="muukal-selected-filters">
		<div class="muukal-selected-filters-list">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<a class="muukal-filter-reset" href="<?php echo esc_url( remove_query_arg( array( 'gender', 'shape', 'style', 'color', 'material', 'size', 'feature', 'min_price', 'max_price', 'sort_by', 'mu_page' ) ) ); ?>"><?php echo esc_html__( 'Clear filter', 'astra' ); ?></a>
	</div>
	<?php
}

/**
 * Render pagination links for the archive.
 *
 * @param WP_Query $query Product query.
 * @return void
 */
function muukal_product_filter_archive_render_pagination( $query ) {
	if ( $query->max_num_pages <= 1 ) {
		return;
	}

	$current_page = isset( $_GET['mu_page'] ) ? max( 1, absint( $_GET['mu_page'] ) ) : 1;
	?>
	<nav class="muukal-filter-pagination" aria-label="<?php echo esc_attr__( 'Product pagination', 'astra' ); ?>">
		<?php for ( $page = 1; $page <= (int) $query->max_num_pages; $page++ ) : ?>
			<?php
			$page_url = add_query_arg( 'mu_page', $page );
			$page_url = remove_query_arg( array( 'paged' ), $page_url );
			?>
			<a class="muukal-filter-page<?php echo $page === $current_page ? ' is-active' : ''; ?>" href="<?php echo esc_url( $page_url ); ?>">
				<?php echo esc_html( $page ); ?>
			</a>
		<?php endfor; ?>
	</nav>
	<?php
}

/**
 * Render the filtered product archive.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function muukal_render_product_filter_archive( $atts ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return '';
	}

	$query_args = muukal_product_filter_archive_build_query_args( $atts );
	$query      = new WP_Query( $query_args );
	$sort_by    = isset( $_GET['sort_by'] ) ? sanitize_key( wp_unslash( $_GET['sort_by'] ) ) : sanitize_key( $atts['sort_by'] );
	$min_price  = isset( $_GET['min_price'] ) ? wc_format_decimal( wp_unslash( $_GET['min_price'] ) ) : '';
	$max_price  = isset( $_GET['max_price'] ) ? wc_format_decimal( wp_unslash( $_GET['max_price'] ) ) : '';

	ob_start();
	?>
	<div class="muukal-filter-archive">
		<form class="muukal-filter-toolbar" method="get">
			<div class="muukal-filter-groups">
				<?php foreach ( muukal_product_filter_archive_get_filter_config() as $key => $config ) : ?>
					<?php muukal_product_filter_archive_render_filter_group( $key, $config, $atts ); ?>
				<?php endforeach; ?>
				<details class="muukal-filter-group muukal-filter-group-price">
					<summary class="muukal-filter-summary"><?php echo esc_html__( 'Price', 'astra' ); ?></summary>
					<div class="muukal-filter-panel muukal-filter-price-panel">
						<label>
							<span><?php echo esc_html__( 'Min', 'astra' ); ?></span>
							<input type="number" min="0" step="0.01" name="min_price" value="<?php echo esc_attr( $min_price ); ?>">
						</label>
						<label>
							<span><?php echo esc_html__( 'Max', 'astra' ); ?></span>
							<input type="number" min="0" step="0.01" name="max_price" value="<?php echo esc_attr( $max_price ); ?>">
						</label>
					</div>
				</details>
				<?php muukal_product_filter_archive_render_sort_group( $sort_by ); ?>
			</div>
			<div class="muukal-filter-actions">
				<div class="muukal-filter-buttons">
					<button type="submit" class="muukal-filter-submit"><?php echo esc_html__( 'Apply Filters', 'astra' ); ?></button>
					<a class="muukal-filter-reset" href="<?php echo esc_url( remove_query_arg( array( 'gender', 'shape', 'style', 'color', 'material', 'size', 'feature', 'min_price', 'max_price', 'sort_by', 'mu_page' ) ) ); ?>"><?php echo esc_html__( 'Reset', 'astra' ); ?></a>
				</div>
			</div>
		</form>

		<?php muukal_product_filter_archive_render_selected_filters( $atts, $sort_by ); ?>

		<div class="muukal-filter-results-bar">
			<p class="muukal-filter-results-count">
				<?php
				printf(
					/* translators: %d: product count */
					esc_html__( '%d products', 'astra' ),
					(int) $query->found_posts
				);
				?>
			</p>
		</div>

		<?php if ( $query->have_posts() ) : ?>
			<div class="muukal-filter-grid">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					echo muukal_render_product_loop_item(
						array(
							'product_id' => get_the_ID(),
						)
					); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				endwhile;
				?>
			</div>
			<?php muukal_product_filter_archive_render_pagination( $query ); ?>
		<?php else : ?>
			<div class="muukal-filter-empty">
				<p><?php echo esc_html__( 'No products matched the selected filters.', 'astra' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
	<?php

	wp_reset_postdata();

	return ob_get_clean();
}
