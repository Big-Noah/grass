<?php
/**
 * Muukal home product tabs markup.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fetch products for a tab.
 *
 * @param string $type  Tab type.
 * @param int    $limit Product count.
 * @param string $slug  Product category slug.
 * @return array<int>
 */
function muukal_get_home_tab_product_ids( $type, $limit, $slug = '' ) {
	$args = array(
		'status' => 'publish',
		'limit'  => $limit,
		'return' => 'ids',
	);

	if ( 'featured' === $type ) {
		$args['featured'] = true;
	} elseif ( 'popular' === $type ) {
		$args['orderby'] = 'popularity';
		$args['order']   = 'DESC';
	} elseif ( 'new' === $type ) {
		$args['orderby'] = 'date';
		$args['order']   = 'DESC';
	} elseif ( 'category' === $type && $slug ) {
		$args['category'] = array( $slug );
		$args['orderby']  = 'date';
		$args['order']    = 'DESC';
	}

	return wc_get_products( $args );
}

/**
 * Build tab config.
 *
 * @param array $args Shortcode args.
 * @return array
 */
function muukal_build_home_tabs( $args ) {
	$tabs = array(
		array(
			'id'    => 'recommend',
			'label' => isset( $args['recommend_label'] ) ? sanitize_text_field( $args['recommend_label'] ) : 'Recommend',
			'type'  => 'featured',
			'slug'  => '',
		),
		array(
			'id'    => 'popular',
			'label' => isset( $args['popular_label'] ) ? sanitize_text_field( $args['popular_label'] ) : 'Hot Sales',
			'type'  => 'popular',
			'slug'  => '',
		),
		array(
			'id'    => 'newin',
			'label' => isset( $args['new_label'] ) ? sanitize_text_field( $args['new_label'] ) : 'New In',
			'type'  => 'new',
			'slug'  => '',
		),
	);

	$category_slugs = isset( $args['category_slugs'] ) ? explode( ',', $args['category_slugs'] ) : array();
	$category_slugs = array_slice(
		array_values(
			array_filter(
				array_map( 'sanitize_title', $category_slugs )
			)
		),
		0,
		6
	);

	foreach ( $category_slugs as $slug ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! $term || is_wp_error( $term ) ) {
			continue;
		}

		$tabs[] = array(
			'id'    => 'cat-' . $slug,
			'label' => $term->name,
			'type'  => 'category',
			'slug'  => $slug,
		);
	}

	return $tabs;
}

/**
 * Render the home product tabs block.
 *
 * @param array $args Shortcode args.
 * @return string
 */
function muukal_render_home_product_tabs( $args ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return '';
	}

	$title    = isset( $args['title'] ) ? sanitize_text_field( $args['title'] ) : 'Featured Eyewear Collections';
	$per_tab  = isset( $args['per_tab'] ) ? absint( $args['per_tab'] ) : 8;
	$per_tab  = $per_tab > 0 ? min( $per_tab, 12 ) : 8;
	$tabs     = muukal_build_home_tabs( $args );
	$active   = ! empty( $tabs[0]['id'] ) ? $tabs[0]['id'] : '';
	$block_id = 'muukal-home-tabs-' . wp_rand( 1000, 99999 );

	if ( empty( $tabs ) ) {
		return '';
	}

	ob_start();
	?>
	<div id="<?php echo esc_attr( $block_id ); ?>" class="product-area pt-75 index-pro-box-1 muukal-home-tabs" data-muukal-home-tabs>
		<div class="container">
			<div class="row">
				<div class="col-xl-6 col-lg-6 offset-lg-3 offset-xl-3">
					<div class="area-title text-center mb15">
						<h2><?php echo esc_html( $title ); ?></h2>
					</div>
				</div>
			</div>
		</div>

		<div class="container-fluid pl-55 pr-55 ncpd960_15">
			<div class="product-tab mb-25">
				<ul class="nav product-nav" role="tablist">
					<?php foreach ( $tabs as $tab ) : ?>
						<li class="nav-item">
							<button
								type="button"
								class="nav-link<?php echo $tab['id'] === $active ? ' is-active' : ''; ?>"
								data-tab-target="<?php echo esc_attr( $tab['id'] ); ?>"
								aria-selected="<?php echo $tab['id'] === $active ? 'true' : 'false'; ?>"
							>
								<?php echo esc_html( $tab['label'] ); ?>
							</button>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<div class="container-fluid pl-55 pr-55 ncpd960_15">
			<?php foreach ( $tabs as $tab ) : ?>
				<?php
				$product_ids = muukal_get_home_tab_product_ids( $tab['type'], $per_tab, $tab['slug'] );
				$is_active   = $tab['id'] === $active;
				?>
				<div class="row muukal-home-tabs__panel<?php echo $is_active ? ' is-active' : ''; ?>" data-tab-panel="<?php echo esc_attr( $tab['id'] ); ?>" <?php echo $is_active ? '' : 'hidden'; ?>>
					<?php foreach ( $product_ids as $product_id ) : ?>
						<?php
						$product = wc_get_product( $product_id );
						if ( ! $product ) {
							continue;
						}
						$image      = $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'ip-img fir_pimg' ) );
						$product_url = get_permalink( $product_id );
						?>
						<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-sxl-4 col-xl-3 pb10 mb-30">
							<div id="goods_<?php echo esc_attr( $product_id ); ?>" class="product-wrapper">
								<a class="product-img pro_a" href="<?php echo esc_url( $product_url ); ?>">
									<?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</a>
								<div class="product-content">
									<h3 class="muukal-home-tabs__name">
										<a href="<?php echo esc_url( $product_url ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
									</h3>
									<span class="pro-price fs16"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php

	return ob_get_clean();
}

