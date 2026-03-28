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
 * Get default price bounds for the slider.
 *
 * @param array $atts Shortcode attributes.
 * @return array<string, float>
 */
function muukal_product_filter_archive_get_price_bounds( $atts ) {
	$min_price = 0.0;
	$max_price = 300.0;

	if ( function_exists( 'wc_get_products' ) ) {
		$base_args = array(
			'status' => 'publish',
			'limit'  => 1,
			'return' => 'objects',
		);

		if ( ! empty( $atts['category'] ) ) {
			$base_args['category'] = array( sanitize_title( $atts['category'] ) );
		}

		$lowest = wc_get_products(
			array_merge(
				$base_args,
				array(
					'orderby' => 'price',
					'order'   => 'ASC',
				)
			)
		);

		$highest = wc_get_products(
			array_merge(
				$base_args,
				array(
					'orderby' => 'price',
					'order'   => 'DESC',
				)
			)
		);

		if ( ! empty( $lowest[0] ) && $lowest[0] instanceof WC_Product ) {
			$min_price = (float) $lowest[0]->get_price();
		}

		if ( ! empty( $highest[0] ) && $highest[0] instanceof WC_Product ) {
			$max_price = (float) $highest[0]->get_price();
		}
	}

	$max_price = $max_price > $min_price ? $max_price : $min_price + 100;
	$min_price = floor( $min_price );
	$max_price = ceil( $max_price / 10 ) * 10;

	return array(
		'min' => max( 0, $min_price ),
		'max' => max( 10, $max_price ),
	);
}

/**
 * Get filter reset URL.
 *
 * @return string
 */
function muukal_product_filter_archive_get_clear_url() {
	return (string) remove_query_arg(
		array( 'gender', 'shape', 'style', 'color', 'material', 'size', 'feature', 'min_price', 'max_price', 'sort_by', 'mu_page' )
	);
}

/**
 * Get archive landing URL for breadcrumbs.
 *
 * @return string
 */
function muukal_product_filter_archive_get_archive_url() {
	$page_id = get_queried_object_id();

	if ( $page_id ) {
		$url = get_permalink( $page_id );

		if ( $url ) {
			return $url;
		}
	}

	return home_url( '/' );
}

/**
 * Get color image map keyed by slug.
 *
 * @return array<string, string>
 */
function muukal_product_filter_archive_get_color_image_map() {
	if ( ! function_exists( 'muukal_swatch_default_palette' ) ) {
		return array();
	}

	$map = array();

	foreach ( muukal_swatch_default_palette() as $item ) {
		if ( empty( $item['color_slug'] ) || empty( $item['dot_image'] ) ) {
			continue;
		}

		$map[ sanitize_title( $item['color_slug'] ) ] = (string) $item['dot_image'];
	}

	return $map;
}

/**
 * Get shape image map keyed by slug.
 *
 * @return array<string, string>
 */
function muukal_product_filter_archive_get_shape_image_map() {
	return array(
		'aviator'   => '//static.muukal.com/public/static/img/home/shape/shape_1.png',
		'browline'  => '//static.muukal.com/public/static/img/home/shape/shape_2.png',
		'cat-eye'   => '//static.muukal.com/public/static/img/home/shape/shape_3.png',
		'geometric' => '//static.muukal.com/public/static/img/home/shape/shape_4.png',
		'oval'      => '//static.muukal.com/public/static/img/home/shape/shape_5.png',
		'rectangle' => '//static.muukal.com/public/static/img/home/shape/shape_6.png',
		'round'     => '//static.muukal.com/public/static/img/home/shape/shape_7.png',
		'square'    => '//static.muukal.com/public/static/img/home/shape/shape_8.png',
		'butterfly' => '//static.muukal.com/public/static/img/home/shape/shape_9.png',
		'horn'      => '//static.muukal.com/public/static/img/home/shape/shape_10.png',
	);
}

/**
 * Build a URL with one selected value removed.
 *
 * @param string $key   Filter key.
 * @param string $value Selected value.
 * @param array  $atts  Shortcode attributes.
 * @return string
 */
function muukal_product_filter_archive_get_remove_value_url( $key, $value, $atts ) {
	$selected = muukal_product_filter_archive_get_effective_selected_terms( $key, $atts );
	$selected = array_values(
		array_filter(
			$selected,
			static function( $item ) use ( $value ) {
				return $item !== $value;
			}
		)
	);

	$url = remove_query_arg( 'mu_page' );

	if ( empty( $selected ) ) {
		return (string) remove_query_arg( $key, $url );
	}

	return (string) add_query_arg( $key, implode( ',', $selected ), $url );
}

/**
 * Build query args for filtered product archive.
 *
 * @param array $atts Shortcode attributes.
 * @return array
 */
function muukal_product_filter_archive_build_query_args( $atts ) {
	$filter_config  = muukal_product_filter_archive_get_filter_config();
	$tax_query      = array();
	$meta_query     = array();
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
		$meta_query[] = array(
			'key'     => '_price',
			'type'    => 'DECIMAL',
			'compare' => 'BETWEEN',
			'value'   => array(
				'' !== $min_price ? (float) $min_price : 0,
				'' !== $max_price ? (float) $max_price : 999999,
			),
		);
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
	$selected  = muukal_product_filter_archive_get_effective_selected_terms( $key, $atts );
	$terms     = muukal_product_filter_archive_get_terms( $config['taxonomy'] );
	$color_map = 'color' === $key ? muukal_product_filter_archive_get_color_image_map() : array();
	$shape_map = 'shape' === $key ? muukal_product_filter_archive_get_shape_image_map() : array();
	$panel_cls = 'filter-opt-box';

	if ( 'shape' === $key ) {
		$panel_cls .= ' filter-shape-box';
	} elseif ( 'style' === $key ) {
		$panel_cls .= ' filter-style-box';
	} elseif ( 'material' === $key ) {
		$panel_cls .= ' filter-material-box';
	} elseif ( 'feature' === $key ) {
		$panel_cls .= ' filter-feature-box';
	} elseif ( 'color' === $key ) {
		$panel_cls .= ' filter-color-box';
	}
	?>
	<li class="dropdown muukal-filter-item">
		<a class="dropdown-toggle<?php echo ! empty( $selected ) ? ' filter-nav-choose' : ''; ?>" href="#" role="button" aria-expanded="false">
			<span><?php echo esc_html( $config['label'] ); ?></span>
			<span class="muukal-filter-arrow"></span>
		</a>
		<div class="dropdown-menu <?php echo esc_attr( $panel_cls ); ?>">
			<?php foreach ( $terms as $term ) : ?>
				<?php $is_selected = in_array( $term->slug, $selected, true ); ?>
				<div fv="<?php echo esc_attr( $key ); ?>" val="<?php echo esc_attr( $term->slug ); ?>" class="filter-icon muukal-filter-option<?php echo $is_selected ? ' filter-icon-choose' : ''; ?>">
					<span class="muukal-filter-check" aria-hidden="true"></span>
					<?php if ( 'color' === $key ) : ?>
						<span class="color-icon-border">
							<?php if ( ! empty( $color_map[ $term->slug ] ) ) : ?>
								<img class="color-icon-image" src="<?php echo esc_url( $color_map[ $term->slug ] ); ?>" alt="<?php echo esc_attr( $term->name ); ?>">
							<?php else : ?>
								<span class="color-icon color-<?php echo esc_attr( $term->slug ); ?>"></span>
							<?php endif; ?>
						</span>
						<span class="color-name">&nbsp;<?php echo esc_html( $term->name ); ?></span>
					<?php elseif ( 'shape' === $key ) : ?>
						<?php if ( ! empty( $shape_map[ $term->slug ] ) ) : ?>
							<span class="shape_icon"><img src="<?php echo esc_url( $shape_map[ $term->slug ] ); ?>" alt="<?php echo esc_attr( $term->name ); ?>"></span>
						<?php endif; ?>
						<span>&nbsp;<?php echo esc_html( $term->name ); ?></span>
					<?php else : ?>
						<span>&nbsp;<?php echo esc_html( $term->name ); ?></span>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</li>
	<?php
}

/**
 * Render price slider group.
 *
 * @param array  $atts      Shortcode attributes.
 * @param string $min_price Current minimum price.
 * @param string $max_price Current maximum price.
 * @return void
 */
function muukal_product_filter_archive_render_price_group( $atts, $min_price, $max_price ) {
	$bounds     = muukal_product_filter_archive_get_price_bounds( $atts );
	$slider_min = '' !== $min_price ? (float) $min_price : (float) $bounds['min'];
	$slider_max = '' !== $max_price ? (float) $max_price : (float) $bounds['max'];
	?>
	<li class="dropdown muukal-filter-item muukal-filter-item-price">
		<a class="dropdown-toggle<?php echo ( '' !== $min_price || '' !== $max_price ) ? ' filter-nav-choose' : ''; ?>" href="#" role="button" aria-expanded="false">
			<span><?php echo esc_html__( 'Price', 'astra' ); ?></span>
			<span class="muukal-filter-arrow"></span>
		</a>
		<div class="dropdown-menu filter-opt-box muukal-filter-price-panel">
			<div class="price-filter">
				<div class="muukal-price-slider" data-min="<?php echo esc_attr( $bounds['min'] ); ?>" data-max="<?php echo esc_attr( $bounds['max'] ); ?>">
					<div class="muukal-price-track"></div>
					<div class="muukal-price-progress"></div>
					<div class="muukal-price-handle muukal-price-handle-min" aria-hidden="true"></div>
					<div class="muukal-price-handle muukal-price-handle-max" aria-hidden="true"></div>
					<input class="muukal-price-range muukal-price-range-min" type="range" min="<?php echo esc_attr( $bounds['min'] ); ?>" max="<?php echo esc_attr( $bounds['max'] ); ?>" step="1" value="<?php echo esc_attr( $slider_min ); ?>">
					<input class="muukal-price-range muukal-price-range-max" type="range" min="<?php echo esc_attr( $bounds['min'] ); ?>" max="<?php echo esc_attr( $bounds['max'] ); ?>" step="1" value="<?php echo esc_attr( $slider_max ); ?>">
				</div>
				<div class="muukal-price-actions">
					<input class="muukal-price-amount" type="text" readonly value="">
					<button class="btn muukal-price-submit" type="button"><?php echo esc_html__( 'Apply', 'astra' ); ?></button>
					<input class="muukal-price-input muukal-price-input-min" type="hidden" name="min_price" value="<?php echo esc_attr( $slider_min ); ?>">
					<input class="muukal-price-input muukal-price-input-max" type="hidden" name="max_price" value="<?php echo esc_attr( $slider_max ); ?>">
				</div>
			</div>
		</div>
	</li>
	<?php
}

/**
 * Render sort group dropdown.
 *
 * @param string $sort_by Current sort key.
 * @return void
 */
function muukal_product_filter_archive_render_sort_group( $sort_by ) {
	$options       = muukal_product_filter_archive_get_sort_options();
	$current_label = isset( $options[ $sort_by ] ) ? $options[ $sort_by ] : $options['recommended'];
	?>
	<li class="dropdown muukal-filter-item muukal-filter-item-sort">
		<a class="dropdown-toggle" href="#" role="button" aria-expanded="false">
			<span><?php echo esc_html( sprintf( 'Sort By: %s', $current_label ) ); ?></span>
			<span class="muukal-filter-arrow"></span>
		</a>
		<div class="dropdown-menu filter-opt-box">
			<?php foreach ( $options as $value => $label ) : ?>
				<div fv="sort_by" val="<?php echo esc_attr( $value ); ?>" class="filter-icon muukal-filter-option<?php echo $sort_by === $value ? ' filter-icon-choose' : ''; ?>">
					<span class="muukal-filter-check" aria-hidden="true"></span>
					<span>&nbsp;<?php echo esc_html( $label ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</li>
	<?php
}

/**
 * Render selected filter breadcrumb.
 *
 * @param array  $atts        Shortcode attributes.
 * @param string $sort_by     Current sort key.
 * @param int    $found_posts Query result count.
 * @return void
 */
function muukal_product_filter_archive_render_selected_filters( $atts, $sort_by, $found_posts ) {
	$filter_config = muukal_product_filter_archive_get_filter_config();
	$sort_options  = muukal_product_filter_archive_get_sort_options();
	?>
	<ol class="breadcrumb muukal-filter-breadcrumb">
		<li class="breadcrumb-item"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html__( 'Home', 'astra' ); ?></a></li>
		<li class="breadcrumb-item"><a href="<?php echo esc_url( muukal_product_filter_archive_get_archive_url() ); ?>"><?php echo esc_html__( 'All Glasses', 'astra' ); ?></a></li>
		<li class="breadcrumb-count"><?php echo esc_html( sprintf( _n( '%d result', '%d results', $found_posts, 'astra' ), $found_posts ) ); ?></li>
		<?php foreach ( $filter_config as $key => $config ) : ?>
			<?php foreach ( muukal_product_filter_archive_get_effective_selected_terms( $key, $atts ) as $slug ) : ?>
				<?php $term = get_term_by( 'slug', $slug, $config['taxonomy'] ); ?>
				<?php if ( ! $term || is_wp_error( $term ) ) : ?>
					<?php continue; ?>
				<?php endif; ?>
				<li class="breadcrumb-filder-item">
					<span class="filder-item-span"><?php echo esc_html( strtolower( $config['label'] ) . ': ' . $term->name ); ?></span>
					<span fv="<?php echo esc_attr( $key ); ?>" val="<?php echo esc_attr( $slug ); ?>" class="filder-item-cross" aria-label="<?php echo esc_attr__( 'Remove filter', 'astra' ); ?>">&times;</span>
				</li>
			<?php endforeach; ?>
		<?php endforeach; ?>
		<?php if ( ! empty( $_GET['min_price'] ) || ! empty( $_GET['max_price'] ) ) : ?>
			<li class="breadcrumb-filder-item">
				<span class="filder-item-span">
					<?php
					echo esc_html(
						sprintf(
							'price: %s - %s',
							! empty( $_GET['min_price'] ) ? wp_unslash( $_GET['min_price'] ) : '0',
							! empty( $_GET['max_price'] ) ? wp_unslash( $_GET['max_price'] ) : 'Any'
						)
					);
					?>
				</span>
				<span fv="price" class="filder-item-cross" aria-label="<?php echo esc_attr__( 'Remove filter', 'astra' ); ?>">&times;</span>
			</li>
		<?php endif; ?>
		<?php if ( $sort_by && 'recommended' !== $sort_by ) : ?>
			<li class="breadcrumb-filder-item">
				<span class="filder-item-span"><?php echo esc_html( 'sort: ' . ( isset( $sort_options[ $sort_by ] ) ? $sort_options[ $sort_by ] : $sort_by ) ); ?></span>
				<span fv="sort_by" val="<?php echo esc_attr( $sort_by ); ?>" class="filder-item-cross" aria-label="<?php echo esc_attr__( 'Remove filter', 'astra' ); ?>">&times;</span>
			</li>
		<?php endif; ?>
		<li class="breadcrumb-clear"><a href="<?php echo esc_url( muukal_product_filter_archive_get_clear_url() ); ?>"><?php echo esc_html__( 'Clear filter', 'astra' ); ?></a></li>
	</ol>
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
	<div class="muukal-filter-archive product-area pl-55 pr-55">
		<div class="container-fluid muukal-filter-shell">
			<?php muukal_product_filter_archive_render_selected_filters( $atts, $sort_by, (int) $query->found_posts ); ?>
			<div class="muukal-filter-toolbar">
				<ul class="nav navbar-nav filter-nav muukal-filter-nav muukal-filter-groups">
					<?php foreach ( muukal_product_filter_archive_get_filter_config() as $key => $config ) : ?>
						<?php muukal_product_filter_archive_render_filter_group( $key, $config, $atts ); ?>
					<?php endforeach; ?>
					<?php muukal_product_filter_archive_render_price_group( $atts, $min_price, $max_price ); ?>
					<?php muukal_product_filter_archive_render_sort_group( $sort_by ); ?>
				</ul>
			</div>
		</div>

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
