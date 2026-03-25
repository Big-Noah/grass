<?php
/**
 * Plugin Name: Muukal Filter Backend
 * Description: Registers filter attributes and applies category, attribute, price, and sort conditions to WooCommerce product archives.
 */

if (!defined('ABSPATH')) {
    exit;
}

function muukal_filter_attribute_definitions() {
    return array(
        'gender' => array(
            'label' => 'Gender',
            'terms' => array('Men', 'Women', 'Kids'),
        ),
        'shape' => array(
            'label' => 'Shape',
            'terms' => array('Aviator', 'Browline', 'Cat-Eye', 'Geometric', 'Oval', 'Rectangle', 'Round', 'Square', 'Horn', 'Butterfly'),
        ),
        'style' => array(
            'label' => 'Style',
            'terms' => array('Classical but Better', 'Daily Styles', 'Edgy Styles'),
        ),
        'color' => array(
            'label' => 'Color',
            'terms' => array('Floral', 'Tortoise', 'Striped', 'Black', 'Gray', 'Clear', 'White', 'Pink', 'Red', 'Brown', 'Orange', 'Yellow', 'Green', 'Blue', 'Purple', 'Golden', 'Silver', 'Bronze', 'Rose Gold', 'Khaki', 'Mix', 'Multicolor'),
        ),
        'material' => array(
            'label' => 'Material',
            'terms' => array('Metal', 'Board', 'Plastic', 'Titanium', 'TR90', 'Acetate', 'Mixed Materials'),
        ),
        'size' => array(
            'label' => 'Size',
            'terms' => array('Extra Small', 'Small', 'Medium', 'Large'),
        ),
        'feature' => array(
            'label' => 'Feature',
            'terms' => array('Retro', 'Minimalist', 'Crafted Grace', 'Timeless Texture', 'Thick & Durable', 'Wickedly Chic', 'Lightweight', 'Spring Hinge', 'Adjustable Nose Pads'),
        ),
    );
}

function muukal_filter_sort_map() {
    return array(
        'recommended' => array(
            'orderby' => 'menu_order title',
            'order' => 'ASC',
            'meta_key' => '',
        ),
        'newest' => array(
            'orderby' => 'date',
            'order' => 'DESC',
            'meta_key' => '',
        ),
        'price_desc' => array(
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'meta_key' => '_price',
        ),
        'price_asc' => array(
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'meta_key' => '_price',
        ),
        'best_sellers' => array(
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'meta_key' => 'total_sales',
        ),
    );
}

function muukal_filter_attribute_request_value($taxonomy_key) {
    if (!isset($_GET[$taxonomy_key])) {
        return '';
    }

    return sanitize_text_field(wp_unslash($_GET[$taxonomy_key]));
}

function muukal_filter_normalize_size_slug($value) {
    $map = array(
        'xs' => 'extra-small',
        's' => 'small',
        'm' => 'medium',
        'l' => 'large',
    );

    $value = strtolower(sanitize_text_field(wp_unslash($value)));

    return isset($map[$value]) ? $map[$value] : sanitize_title($value);
}

function muukal_filter_requested_term_slug($taxonomy_key) {
    $raw_value = muukal_filter_attribute_request_value($taxonomy_key);
    if ($raw_value === '') {
        return '';
    }

    if ($taxonomy_key === 'size') {
        return muukal_filter_normalize_size_slug($raw_value);
    }

    return sanitize_title($raw_value);
}

function muukal_filter_current_archive_url() {
    if (is_product_category() || is_product_tag() || is_product_taxonomy()) {
        $term = get_queried_object();
        if ($term instanceof WP_Term) {
            return get_term_link($term);
        }
    }

    if (function_exists('wc_get_page_permalink')) {
        return wc_get_page_permalink('shop');
    }

    return home_url('/');
}

function muukal_filter_collection_term_slugs() {
    return array(
        'new-arrivals',
        'rectangle',
        'cat-eye',
        'clip-on',
        'spring-hinge',
        'color-of-year',
        'timeless-texture',
        'crafted-grace',
        'thick',
        'retro',
        'chic',
        'rimless',
        'floral',
        'tortoise',
        'view-all',
        'current-promotions',
    );
}

function muukal_filter_sort_label($sort_key) {
    $labels = array(
        'recommended' => 'Recommended',
        'newest' => 'Newest Arrivals',
        'price_desc' => 'Price: High to Low',
        'price_asc' => 'Price: Low to High',
        'best_sellers' => 'Best Sellers',
    );

    return isset($labels[$sort_key]) ? $labels[$sort_key] : 'Recommended';
}

function muukal_filter_build_url($overrides = array(), $removals = array()) {
    $params = $_GET;

    foreach ($overrides as $key => $value) {
        if ($value === '' || $value === null) {
            unset($params[$key]);
            continue;
        }
        $params[$key] = $value;
    }

    foreach ($removals as $key) {
        unset($params[$key]);
    }

    return add_query_arg($params, muukal_filter_current_archive_url());
}

function muukal_filter_render_dropdown($key, $label, $terms) {
    $current_value = muukal_filter_attribute_request_value($key);
    $current_label = $label;

    foreach ($terms as $term) {
        if (strcasecmp($current_value, $term->name) === 0 || $current_value === $term->slug) {
            $current_label = $term->name;
            break;
        }
    }

    ob_start();
    ?>
    <li class="dropdown muukal-filter-item">
        <button class="muukal-filter-toggle" type="button">
            <span><?php echo esc_html($current_label === $label ? $label : $current_label); ?></span>
            <span class="muukal-filter-arrow">▼</span>
        </button>
        <div class="muukal-filter-menu">
            <a class="muukal-filter-option<?php echo $current_value === '' ? ' is-active' : ''; ?>" href="<?php echo esc_url(muukal_filter_build_url(array(), array($key))); ?>">
                All
            </a>
            <?php foreach ($terms as $term) : ?>
                <?php $is_active = strcasecmp($current_value, $term->name) === 0 || $current_value === $term->slug; ?>
                <a class="muukal-filter-option<?php echo $is_active ? ' is-active' : ''; ?>" href="<?php echo esc_url(muukal_filter_build_url(array($key => $term->slug))); ?>">
                    <?php echo esc_html($term->name); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </li>
    <?php

    return ob_get_clean();
}

function muukal_filter_render_price_dropdown() {
    $min_price = isset($_GET['min_price']) ? sanitize_text_field(wp_unslash($_GET['min_price'])) : '';
    $max_price = isset($_GET['max_price']) ? sanitize_text_field(wp_unslash($_GET['max_price'])) : '';

    $ranges = array(
        array('label' => 'Under $20', 'min' => '', 'max' => '20'),
        array('label' => '$20 - $50', 'min' => '20', 'max' => '50'),
        array('label' => '$50 - $100', 'min' => '50', 'max' => '100'),
        array('label' => '$100+', 'min' => '100', 'max' => ''),
    );

    ob_start();
    ?>
    <li class="dropdown muukal-filter-item">
        <button class="muukal-filter-toggle" type="button">
            <span>Price</span>
            <span class="muukal-filter-arrow">▼</span>
        </button>
        <div class="muukal-filter-menu">
            <a class="muukal-filter-option<?php echo ($min_price === '' && $max_price === '') ? ' is-active' : ''; ?>" href="<?php echo esc_url(muukal_filter_build_url(array(), array('min_price', 'max_price'))); ?>">
                All
            </a>
            <?php foreach ($ranges as $range) : ?>
                <?php $is_active = $min_price === $range['min'] && $max_price === $range['max']; ?>
                <a class="muukal-filter-option<?php echo $is_active ? ' is-active' : ''; ?>" href="<?php echo esc_url(muukal_filter_build_url(array('min_price' => $range['min'], 'max_price' => $range['max']))); ?>">
                    <?php echo esc_html($range['label']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </li>
    <?php

    return ob_get_clean();
}

function muukal_filter_render_sort_dropdown() {
    $current_sort = isset($_GET['sort']) ? sanitize_key(wp_unslash($_GET['sort'])) : 'recommended';
    $sort_options = array(
        'recommended' => 'Recommended',
        'newest' => 'Newest Arrivals',
        'price_desc' => 'Price: High to Low',
        'price_asc' => 'Price: Low to High',
        'best_sellers' => 'Best Sellers',
    );

    ob_start();
    ?>
    <li class="dropdown muukal-filter-item">
        <button class="muukal-filter-toggle" type="button">
            <span>Sort By: <?php echo esc_html(muukal_filter_sort_label($current_sort)); ?></span>
            <span class="muukal-filter-arrow">▼</span>
        </button>
        <div class="muukal-filter-menu">
            <?php foreach ($sort_options as $value => $label) : ?>
                <a class="muukal-filter-option<?php echo $current_sort === $value ? ' is-active' : ''; ?>" href="<?php echo esc_url(muukal_filter_build_url(array('sort' => $value))); ?>">
                    <?php echo esc_html($label); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </li>
    <?php

    return ob_get_clean();
}

function muukal_filter_hidden_query_fields_v2($exclude = array()) {
    foreach ($_GET as $key => $value) {
        if (in_array($key, $exclude, true)) {
            continue;
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                ?>
                <input type="hidden" name="<?php echo esc_attr($key); ?>[]" value="<?php echo esc_attr(sanitize_text_field(wp_unslash($item))); ?>">
                <?php
            }
            continue;
        }
        ?>
        <input type="hidden" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr(sanitize_text_field(wp_unslash($value))); ?>">
        <?php
    }
}

function muukal_filter_render_dropdown_v2($key, $label, $terms) {
    $current_value = muukal_filter_attribute_request_value($key);
    $current_label = $label;

    foreach ($terms as $term) {
        if (strcasecmp($current_value, $term->name) === 0 || $current_value === $term->slug) {
            $current_label = $term->name;
            break;
        }
    }

    ob_start();
    ?>
    <li class="dropdown muukal-filter-item">
        <button class="muukal-filter-toggle" type="button" aria-expanded="false">
            <span><?php echo esc_html($current_label === $label ? $label : $current_label); ?></span>
            <span class="muukal-filter-arrow">&#9662;</span>
        </button>
        <div class="muukal-filter-menu">
            <a class="muukal-filter-option<?php echo $current_value === '' ? ' is-active' : ''; ?>" href="<?php echo esc_url(muukal_filter_build_url(array(), array($key))); ?>">All</a>
            <?php foreach ($terms as $term) : ?>
                <?php $is_active = strcasecmp($current_value, $term->name) === 0 || $current_value === $term->slug; ?>
                <a class="muukal-filter-option<?php echo $is_active ? ' is-active' : ''; ?>" href="<?php echo esc_url(muukal_filter_build_url(array($key => $term->slug))); ?>">
                    <?php echo esc_html($term->name); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </li>
    <?php

    return ob_get_clean();
}

function muukal_filter_render_price_dropdown_v2() {
    $min_price   = isset($_GET['min_price']) ? sanitize_text_field(wp_unslash($_GET['min_price'])) : '';
    $max_price   = isset($_GET['max_price']) ? sanitize_text_field(wp_unslash($_GET['max_price'])) : '';
    $price_floor = 0;
    $price_cap   = 300;

    if (function_exists('wc_get_products')) {
        $lowest = wc_get_products(array('status' => 'publish', 'limit' => 1, 'return' => 'objects', 'orderby' => 'price', 'order' => 'ASC'));
        $highest = wc_get_products(array('status' => 'publish', 'limit' => 1, 'return' => 'objects', 'orderby' => 'price', 'order' => 'DESC'));

        if (!empty($lowest[0]) && $lowest[0] instanceof WC_Product) {
            $price_floor = max(0, (int) floor((float) $lowest[0]->get_price()));
        }
        if (!empty($highest[0]) && $highest[0] instanceof WC_Product) {
            $price_cap = max($price_floor + 10, (int) ceil((float) $highest[0]->get_price()));
        }
    }

    $price_cap   = (int) ceil($price_cap / 10) * 10;
    $current_min = $min_price !== '' ? max($price_floor, (int) $min_price) : $price_floor;
    $current_max = $max_price !== '' ? min($price_cap, (int) $max_price) : $price_cap;
    $current_max = max($current_min, $current_max);

    ob_start();
    ?>
    <li class="dropdown muukal-filter-item muukal-filter-item-price">
        <button class="muukal-filter-toggle" type="button" aria-expanded="false">
            <span>Price</span>
            <span class="muukal-filter-arrow">&#9662;</span>
        </button>
        <div class="muukal-filter-menu muukal-filter-menu-price">
            <form class="muukal-filter-price-form" method="get" action="<?php echo esc_url(muukal_filter_current_archive_url()); ?>">
                <?php muukal_filter_hidden_query_fields_v2(array('min_price', 'max_price')); ?>
                <div class="muukal-price-slider" data-min="<?php echo esc_attr($price_floor); ?>" data-max="<?php echo esc_attr($price_cap); ?>">
                    <div class="muukal-price-track"></div>
                    <div class="muukal-price-progress"></div>
                    <input class="muukal-price-range muukal-price-range-min" type="range" min="<?php echo esc_attr($price_floor); ?>" max="<?php echo esc_attr($price_cap); ?>" step="1" value="<?php echo esc_attr($current_min); ?>">
                    <input class="muukal-price-range muukal-price-range-max" type="range" min="<?php echo esc_attr($price_floor); ?>" max="<?php echo esc_attr($price_cap); ?>" step="1" value="<?php echo esc_attr($current_max); ?>">
                    <div class="muukal-price-values">
                        <span class="muukal-price-value-min"></span>
                        <span class="muukal-price-value-max"></span>
                    </div>
                    <div class="muukal-price-inputs">
                        <label><span>Min</span><input class="muukal-price-input muukal-price-input-min" type="number" min="<?php echo esc_attr($price_floor); ?>" max="<?php echo esc_attr($price_cap); ?>" step="1" name="min_price" value="<?php echo esc_attr($current_min); ?>"></label>
                        <label><span>Max</span><input class="muukal-price-input muukal-price-input-max" type="number" min="<?php echo esc_attr($price_floor); ?>" max="<?php echo esc_attr($price_cap); ?>" step="1" name="max_price" value="<?php echo esc_attr($current_max); ?>"></label>
                    </div>
                </div>
                <div class="muukal-filter-price-actions">
                    <a class="muukal-filter-clear" href="<?php echo esc_url(muukal_filter_build_url(array(), array('min_price', 'max_price'))); ?>">Clear</a>
                    <button class="muukal-filter-apply" type="submit">Apply</button>
                </div>
            </form>
        </div>
    </li>
    <?php

    return ob_get_clean();
}

function muukal_filter_render_sort_dropdown_v2() {
    $current_sort = isset($_GET['sort']) ? sanitize_key(wp_unslash($_GET['sort'])) : 'recommended';
    $sort_options = array(
        'recommended' => 'Recommended',
        'newest' => 'Newest Arrivals',
        'price_desc' => 'Price: High to Low',
        'price_asc' => 'Price: Low to High',
        'best_sellers' => 'Best Sellers',
    );

    ob_start();
    ?>
    <li class="dropdown muukal-filter-item">
        <button class="muukal-filter-toggle" type="button" aria-expanded="false">
            <span>Sort By: <?php echo esc_html(muukal_filter_sort_label($current_sort)); ?></span>
            <span class="muukal-filter-arrow">&#9662;</span>
        </button>
        <div class="muukal-filter-menu">
            <?php foreach ($sort_options as $value => $label) : ?>
                <a class="muukal-filter-option<?php echo $current_sort === $value ? ' is-active' : ''; ?>" href="<?php echo esc_url(muukal_filter_build_url(array('sort' => $value))); ?>">
                    <?php echo esc_html($label); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </li>
    <?php

    return ob_get_clean();
}

function muukal_filter_shortcode($atts = array()) {
    if (!function_exists('wc_attribute_taxonomy_name')) {
        return '';
    }

    $atts = shortcode_atts(
        array(
            'show' => 'gender,shape,style,color,material,size,feature,price,sort',
        ),
        $atts,
        'muukal_filter_nav'
    );

    $requested_sections = array_map('trim', explode(',', $atts['show']));
    $definitions = muukal_filter_attribute_definitions();

    ob_start();
    ?>
    <style>
        .muukal-filter-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .muukal-filter-item {
            position: relative;
        }
        .muukal-filter-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            min-width: 112px;
            min-height: 44px;
            border: 1px solid #d8d8d8;
            background: #fff;
            color: #1f1f1f;
            font-size: 14px;
            line-height: 1;
            padding: 11px 16px;
            border-radius: 999px;
            cursor: pointer;
        }
        .muukal-filter-arrow {
            font-size: 10px;
            transition: transform 0.2s ease;
        }
        .muukal-filter-item:hover .muukal-filter-arrow,
        .muukal-filter-item:focus-within .muukal-filter-arrow {
            transform: rotate(180deg);
        }
        .muukal-filter-menu {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            z-index: 30;
            display: none;
            min-width: 240px;
            max-height: 360px;
            overflow-y: auto;
            padding: 12px;
            border: 1px solid #e5e5e5;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
        }
        .muukal-filter-item:hover .muukal-filter-menu,
        .muukal-filter-item:focus-within .muukal-filter-menu {
            display: block;
        }
        .muukal-filter-option {
            display: block;
            padding: 10px 12px;
            border-radius: 10px;
            color: #222;
            text-decoration: none;
            white-space: nowrap;
            font-size: 14px;
            line-height: 1.3;
        }
        .muukal-filter-option:hover,
        .muukal-filter-option.is-active {
            background: #f4f4f4;
        }
        .muukal-filter-menu-price {
            min-width: 320px;
        }
        .muukal-filter-price-form {
            margin: 0;
        }
        .muukal-price-slider {
            position: relative;
            padding-top: 12px;
        }
        .muukal-price-track,
        .muukal-price-progress {
            position: absolute;
            top: 20px;
            height: 4px;
            border-radius: 999px;
        }
        .muukal-price-track {
            left: 0;
            right: 0;
            background: #ececec;
        }
        .muukal-price-progress {
            background: #111827;
        }
        .muukal-price-range {
            position: absolute;
            top: 12px;
            left: 0;
            width: 100%;
            height: 20px;
            margin: 0;
            -webkit-appearance: none;
            appearance: none;
            background: transparent;
            pointer-events: none;
        }
        .muukal-price-range::-webkit-slider-thumb {
            width: 16px;
            height: 16px;
            border: 2px solid #111827;
            border-radius: 50%;
            background: #fff;
            -webkit-appearance: none;
            appearance: none;
            pointer-events: auto;
        }
        .muukal-price-range::-moz-range-thumb {
            width: 16px;
            height: 16px;
            border: 2px solid #111827;
            border-radius: 50%;
            background: #fff;
            pointer-events: auto;
        }
        .muukal-price-range::-moz-range-track {
            background: transparent;
        }
        .muukal-price-values {
            display: flex;
            justify-content: space-between;
            margin-top: 28px;
            color: #111827;
            font-size: 13px;
            font-weight: 600;
        }
        .muukal-price-inputs {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        .muukal-price-inputs label {
            display: flex;
            flex-direction: column;
            gap: 6px;
            color: #475467;
            font-size: 13px;
        }
        .muukal-price-input {
            height: 40px;
            padding: 0 10px;
            border: 1px solid #d0d5dd;
            border-radius: 10px;
        }
        .muukal-filter-price-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-top: 14px;
        }
        .muukal-filter-clear,
        .muukal-filter-apply {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            padding: 0 14px;
            border-radius: 999px;
            font-size: 13px;
            text-decoration: none;
            cursor: pointer;
        }
        .muukal-filter-clear {
            color: #475467;
            border: 1px solid #d0d5dd;
            background: #fff;
        }
        .muukal-filter-apply {
            color: #fff;
            border: 1px solid #111827;
            background: #111827;
        }
        .woocommerce.elementor-widget-loop-grid.elementor-grid-4 > .elementor-widget-container > .elementor-loop-container.elementor-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
        @media (max-width: 1024px) {
            .woocommerce.elementor-widget-loop-grid.elementor-grid-4 > .elementor-widget-container > .elementor-loop-container.elementor-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
        @media (max-width: 767px) {
            .muukal-filter-nav {
                gap: 10px;
            }
            .muukal-filter-item,
            .muukal-filter-toggle {
                width: 100%;
            }
            .muukal-filter-menu {
                position: static;
                min-width: 100%;
                margin-top: 8px;
                box-shadow: none;
            }
            .woocommerce.elementor-widget-loop-grid.elementor-grid-4 > .elementor-widget-container > .elementor-loop-container.elementor-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 520px) {
            .woocommerce.elementor-widget-loop-grid.elementor-grid-4 > .elementor-widget-container > .elementor-loop-container.elementor-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <ul class="nav navbar-nav filter-nav muukal-filter-nav">
        <?php
        foreach ($requested_sections as $section) {
            if ($section === 'price') {
                echo muukal_filter_render_price_dropdown_v2();
                continue;
            }

            if ($section === 'sort') {
                echo muukal_filter_render_sort_dropdown_v2();
                continue;
            }

            if (!isset($definitions[$section])) {
                continue;
            }

            $taxonomy = wc_attribute_taxonomy_name($section);
            if (!taxonomy_exists($taxonomy)) {
                continue;
            }

            $terms = get_terms(
                array(
                    'taxonomy'   => $taxonomy,
                    'hide_empty' => false,
                )
            );

            if (is_wp_error($terms) || empty($terms)) {
                continue;
            }

            echo muukal_filter_render_dropdown_v2($section, $definitions[$section]['label'], $terms);
        }
        ?>
    </ul>
    <script>
        (function() {
            function formatPrice(value) {
                return '$' + Math.round(Number(value) || 0);
            }

            function syncPriceSlider(root) {
                var minRange = root.querySelector('.muukal-price-range-min');
                var maxRange = root.querySelector('.muukal-price-range-max');
                var minInput = root.querySelector('.muukal-price-input-min');
                var maxInput = root.querySelector('.muukal-price-input-max');
                var minValue = root.querySelector('.muukal-price-value-min');
                var maxValue = root.querySelector('.muukal-price-value-max');
                var progress = root.querySelector('.muukal-price-progress');
                var minBound = Number(root.getAttribute('data-min') || 0);
                var maxBound = Number(root.getAttribute('data-max') || 0);
                var min = Number(minRange.value || minBound);
                var max = Number(maxRange.value || maxBound);

                if (min > max) {
                    if (document.activeElement === minRange || document.activeElement === minInput) {
                        max = min;
                        maxRange.value = String(max);
                    } else {
                        min = max;
                        minRange.value = String(min);
                    }
                }

                minInput.value = String(min);
                maxInput.value = String(max);
                minValue.textContent = formatPrice(min);
                maxValue.textContent = formatPrice(max);

                if (maxBound > minBound) {
                    var left = ((min - minBound) / (maxBound - minBound)) * 100;
                    var right = ((max - minBound) / (maxBound - minBound)) * 100;
                    progress.style.left = left + '%';
                    progress.style.width = Math.max(right - left, 0) + '%';
                }
            }

            document.querySelectorAll('.muukal-price-slider').forEach(function(root) {
                var minRange = root.querySelector('.muukal-price-range-min');
                var maxRange = root.querySelector('.muukal-price-range-max');
                var minInput = root.querySelector('.muukal-price-input-min');
                var maxInput = root.querySelector('.muukal-price-input-max');

                [minRange, maxRange, minInput, maxInput].forEach(function(input) {
                    input.addEventListener('input', function() {
                        if (input === minInput) {
                            minRange.value = input.value;
                        }
                        if (input === maxInput) {
                            maxRange.value = input.value;
                        }
                        syncPriceSlider(root);
                    });
                });

                syncPriceSlider(root);
            });
        })();
    </script>
    <?php

    return ob_get_clean();
}
add_shortcode('muukal_filter_nav', 'muukal_filter_shortcode');

function muukal_collection_nav_shortcode($atts = array()) {
    if (!taxonomy_exists('product_cat')) {
        return '';
    }

    $atts = shortcode_atts(
        array(
            'parent' => '',
            'include' => implode(',', muukal_filter_collection_term_slugs()),
            'show_all' => 'yes',
            'show_recommend' => 'yes',
            'recommend_label' => 'RECOMMEND',
            'recommend_url' => '',
        ),
        $atts,
        'muukal_collection_nav'
    );

    $include_slugs = array_filter(array_map('trim', explode(',', $atts['include'])));
    $terms = array();

    foreach ($include_slugs as $slug) {
        $term = get_term_by('slug', $slug, 'product_cat');
        if ($term instanceof WP_Term) {
            $terms[] = $term;
        }
    }

    if (empty($terms)) {
        return '';
    }

    $current_term = get_queried_object();
    $current_term_id = ($current_term instanceof WP_Term && $current_term->taxonomy === 'product_cat') ? (int) $current_term->term_id : 0;
    $all_url = '';

    if ($atts['parent'] !== '') {
        $parent_term = get_term_by('slug', sanitize_title($atts['parent']), 'product_cat');
        if ($parent_term instanceof WP_Term) {
            $all_url = get_term_link($parent_term);
        }
    }

    if ($all_url === '' && function_exists('wc_get_page_permalink')) {
        $all_url = wc_get_page_permalink('shop');
    }

    $recommend_url = $atts['recommend_url'] !== '' ? esc_url_raw($atts['recommend_url']) : $all_url;
    $recommend_active = $current_term_id === 0;

    ob_start();
    ?>
    <style>
        .muukal-collection-nav-wrap {
            margin: 0 0 28px;
            text-align: center;
        }
        .muukal-collection-nav {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px 26px;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .muukal-collection-nav-item {
            display: flex;
        }
        .muukal-collection-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #111827;
            text-decoration: none;
            font-size: 17px;
            font-weight: 500;
            line-height: 1.2;
            letter-spacing: 0.01em;
            padding: 6px 0;
            border-bottom: 2px solid transparent;
            transition: border-color 0.2s ease, color 0.2s ease, opacity 0.2s ease;
        }
        .muukal-collection-link:hover,
        .muukal-collection-link.is-active {
            border-color: #111;
        }
        .muukal-collection-link:hover {
            opacity: 0.72;
        }
        .muukal-collection-link.is-recommend {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }
        @media (max-width: 767px) {
            .muukal-collection-nav {
                justify-content: flex-start;
                gap: 8px 18px;
                overflow-x: auto;
                flex-wrap: nowrap;
                padding-bottom: 6px;
                scrollbar-width: none;
            }
            .muukal-collection-nav::-webkit-scrollbar {
                display: none;
            }
            .muukal-collection-link {
                white-space: nowrap;
                font-size: 15px;
            }
            .muukal-collection-link.is-recommend {
                font-size: 16px;
            }
        }
    </style>
    <div class="text-center mb10 muukal-collection-nav-wrap">
        <div class="mt20"></div>
        <ul class="muukal-collection-nav">
            <?php if ($atts['show_recommend'] === 'yes' && $recommend_url !== '') : ?>
                <li class="muukal-collection-nav-item">
                    <a class="topic_o_link fs18 muukal-collection-link muukal-collection-recommend is-recommend<?php echo $recommend_active ? ' is-active' : ''; ?>" href="<?php echo esc_url($recommend_url); ?>">
                        <?php echo esc_html($atts['recommend_label']); ?>
                    </a>
                </li>
            <?php elseif ($atts['show_all'] === 'yes' && $all_url !== '') : ?>
                <li class="muukal-collection-nav-item">
                    <a class="topic_o_link fs18 muukal-collection-link<?php echo $current_term_id === 0 ? ' is-active' : ''; ?>" href="<?php echo esc_url($all_url); ?>">
                        View All
                    </a>
                </li>
            <?php endif; ?>
            <?php foreach ($terms as $term) : ?>
                <li class="muukal-collection-nav-item">
                    <a class="topic_o_link fs18 muukal-collection-link<?php echo $current_term_id === (int) $term->term_id ? ' is-active' : ''; ?>" href="<?php echo esc_url(get_term_link($term)); ?>">
                        <?php echo esc_html($term->name); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode('muukal_collection_nav', 'muukal_collection_nav_shortcode');

function muukal_filter_ensure_attributes_exist() {
    if (!function_exists('wc_get_attribute_taxonomies') || !function_exists('wc_create_attribute')) {
        return;
    }

    $definitions = muukal_filter_attribute_definitions();
    $existing = wc_get_attribute_taxonomies();
    $existing_by_name = array();

    foreach ($existing as $attribute) {
        $existing_by_name[$attribute->attribute_name] = $attribute;
    }

    $created = false;

    foreach ($definitions as $slug => $definition) {
        if (isset($existing_by_name[$slug])) {
            continue;
        }

        $result = wc_create_attribute(
            array(
                'name'         => $definition['label'],
                'slug'         => $slug,
                'type'         => 'select',
                'order_by'     => 'menu_order',
                'has_archives' => false,
            )
        );

        if (!is_wp_error($result)) {
            $created = true;
        }
    }

    if ($created) {
        delete_transient('wc_attribute_taxonomies');
        if (class_exists('WC_Cache_Helper')) {
            WC_Cache_Helper::invalidate_cache_group('woocommerce-attributes');
        }
    }

    if ($created || !get_option('muukal_seed_attribute_terms_pending')) {
        update_option('muukal_seed_attribute_terms_pending', '1', false);
    }
}
add_action('admin_init', 'muukal_filter_ensure_attributes_exist');

function muukal_filter_seed_attribute_terms() {
    if (!get_option('muukal_seed_attribute_terms_pending')) {
        return;
    }

    $definitions = muukal_filter_attribute_definitions();
    $all_seeded = true;

    foreach ($definitions as $slug => $definition) {
        $taxonomy = wc_attribute_taxonomy_name($slug);

        if (!taxonomy_exists($taxonomy)) {
            $all_seeded = false;
            continue;
        }

        foreach ($definition['terms'] as $term_name) {
            if (!term_exists($term_name, $taxonomy)) {
                wp_insert_term($term_name, $taxonomy);
            }
        }
    }

    if ($all_seeded) {
        delete_option('muukal_seed_attribute_terms_pending');
    }
}
add_action('init', 'muukal_filter_seed_attribute_terms', 20);

function muukal_apply_catalog_filters($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if (!function_exists('is_shop')) {
        return;
    }

    if (!(is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag())) {
        return;
    }

    $tax_query = (array) $query->get('tax_query');
    $meta_query = (array) $query->get('meta_query');

    foreach (array_keys(muukal_filter_attribute_definitions()) as $key) {
        $requested_slug = muukal_filter_requested_term_slug($key);
        if ($requested_slug === '') {
            continue;
        }

        $taxonomy = wc_attribute_taxonomy_name($key);
        if (!taxonomy_exists($taxonomy)) {
            continue;
        }

        $tax_query[] = array(
            'taxonomy' => $taxonomy,
            'field'    => 'slug',
            'terms'    => array($requested_slug),
        );
    }

    $min_price = isset($_GET['min_price']) ? floatval(wp_unslash($_GET['min_price'])) : null;
    $max_price = isset($_GET['max_price']) ? floatval(wp_unslash($_GET['max_price'])) : null;

    if ($min_price !== null || $max_price !== null) {
        $price_clause = array(
            'key'     => '_price',
            'type'    => 'DECIMAL(10,2)',
            'compare' => 'BETWEEN',
            'value'   => array(
                $min_price !== null ? $min_price : 0,
                $max_price !== null ? $max_price : 999999,
            ),
        );

        if ($min_price !== null && $max_price === null) {
            $price_clause['compare'] = '>=';
            $price_clause['value'] = $min_price;
        } elseif ($min_price === null && $max_price !== null) {
            $price_clause['compare'] = '<=';
            $price_clause['value'] = $max_price;
        }

        $meta_query[] = $price_clause;
    }

    $sort = isset($_GET['sort']) ? sanitize_key(wp_unslash($_GET['sort'])) : 'recommended';
    $sort_map = muukal_filter_sort_map();

    if (isset($sort_map[$sort])) {
        $ordering = $sort_map[$sort];
        $query->set('orderby', $ordering['orderby']);
        $query->set('order', $ordering['order']);

        if (!empty($ordering['meta_key'])) {
            $query->set('meta_key', $ordering['meta_key']);
        }
    }

    if (!empty($tax_query)) {
        if (count($tax_query) > 1 && !isset($tax_query['relation'])) {
            $tax_query['relation'] = 'AND';
        }
        $query->set('tax_query', $tax_query);
    }

    if (!empty($meta_query)) {
        if (count($meta_query) > 1 && !isset($meta_query['relation'])) {
            $meta_query['relation'] = 'AND';
        }
        $query->set('meta_query', $meta_query);
    }
}
add_action('pre_get_posts', 'muukal_apply_catalog_filters');
