<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit( "Run this file with wp eval-file.\n" );
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$category_tree = array(
	'men' => array(
		'name'   => 'Men',
		'parent' => 0,
	),
	'mens-eyeglasses' => array(
		'name'   => "Men's Eyeglasses",
		'parent' => 'men',
	),
	'mens-sunglasses' => array(
		'name'   => "Men's Sunglasses",
		'parent' => 'men',
	),
	'new-arrivals-men' => array(
		'name'   => 'New Arrivals',
		'parent' => 'men',
	),
	'ultra-light-men' => array(
		'name'   => 'Ultra-Light',
		'parent' => 'men',
	),
	'clip-on-glasses-men' => array(
		'name'   => 'Clip-On Glasses',
		'parent' => 'men',
	),
	'all-mens-glasses' => array(
		'name'   => "All Men's Glasses",
		'parent' => 'men',
	),
);

$raw_products = array(
	array(
		'gid'            => 2245,
		'source_sku'     => '8227',
		'title'          => 'Crace Gray Browline Chic Mixed Materials Eyeglasses',
		'short_name'     => 'Crace',
		'description'    => "Elevate your style with Crace hybrid eyeglasses. These sophisticated rimless glasses offer a modern take on the classic browline design, perfectly embodying an elegant and refined look. Crafted from premium metal, they offer both durability and lightweight comfort. Available in versatile shades like grey, gold, and silver, these unisex glasses are designed to perfectly complement any personal style. Whether you're aiming for a subtly professional image or a bold fashion statement, Crace eyeglasses are the ideal choice for your everyday wardrobe.",
		'price'          => '26.95',
		'image'          => 'https://img.muukal.com/goods/2245/gray_1.jpg',
		'size'           => 'L',
		'source_url'     => 'https://muukal.com/frame/goods/gid/2245/gcid/5/gn/gray-browline-chic-mixed-materials-eyeglasses.html',
		'category_slugs' => array( 'mens-eyeglasses', 'all-mens-glasses' ),
	),
	array(
		'gid'            => 2202,
		'source_sku'     => '86071',
		'title'          => 'Harvey Tortoise Horn Simple Lightweight TR90 Eyeglasses',
		'short_name'     => 'Harvey',
		'description'    => 'These unisex full-rim square glasses are made from ultra-lightweight and flexible TR90 material, providing a light and comfortable fit, even for extended wear. The soft, rounded square frame design suits most face shapes and is available in warm, classic tortoiseshell, stylish glossy black, modern translucent gray, minimalist crystal clear, soft amber brown, and subtle green tortoiseshell. The slender, delicate metal temples add a touch of elegance, making these glasses a fashionable and practical choice for both casual and formal occasions.',
		'price'          => '22.95',
		'image'          => 'https://img.muukal.com/goods/2202/tortoise_1.jpg',
		'size'           => 'L',
		'source_url'     => 'https://muukal.com/frame/goods/gid/2202/gcid/2/gn/tortoise-horn-simple-lightweight-tr90-eyeglasses.html',
		'category_slugs' => array( 'mens-eyeglasses', 'all-mens-glasses' ),
	),
	array(
		'gid'            => 2037,
		'source_sku'     => '23110',
		'title'          => 'Zoie Golden Aviator Chic Metal Eyeglasses',
		'short_name'     => 'Zoie',
		'description'    => "This unisex full-rim aviator sunglasses is crafted from sleek metal, available in three sharp finishes: gold, black, and silver. The classic pilot frame pairs with gradient lenses for sun protection, while adjustable nose pads ensure a comfortable fit for all face shapes. Sleek details add modern edge, balancing timeless style with durable design. Perfect for both casual days and polished outings-it's the versatile accessory your wardrobe needs.",
		'price'          => '23.95',
		'image'          => 'https://img.muukal.com/goods/2037/gold_1.jpg',
		'size'           => 'L',
		'source_url'     => 'https://muukal.com/frame/goods/gid/2037/gcid/16/gn/golden-aviator-chic-metal-eyeglasses.html',
		'category_slugs' => array( 'mens-eyeglasses', 'mens-sunglasses', 'all-mens-glasses' ),
	),
	array(
		'gid'            => 2107,
		'source_sku'     => '27637',
		'title'          => 'Hyannis Brown Rectangle Simple Metal Eyeglasses',
		'short_name'     => 'Hyannis',
		'description'    => "These men's full-rim rectangular eyeglasses are crafted with spring hinges for a flexible, comfortable fit. They come in four sophisticated color options: black-gold, brown, blue, and black-silver, all featuring sleek metal frames with refined detailing. Designed exclusively for stylish men, they are ideal for daily work, business meetings, and casual outings, merging durable construction with a polished, professional look.",
		'price'          => '37.95',
		'image'          => 'https://img.muukal.com/goods/2107/brown_1.jpg',
		'size'           => 'L',
		'source_url'     => 'https://muukal.com/frame/goods/gid/2107/gcid/10/gn/brown-rectangle-simple-metal-eyeglasses.html',
		'category_slugs' => array( 'mens-eyeglasses', 'ultra-light-men', 'all-mens-glasses' ),
	),
	array(
		'gid'            => 1948,
		'source_sku'     => '2460',
		'title'          => 'Shaun Black Rectangle Simple Plastic Eyeglasses',
		'short_name'     => 'Shaun',
		'description'    => 'These unisex full-rim glasses feature a stylish rectangular frame made from lightweight plastic, perfectly blending modern minimalist style with everyday comfort. They are available in five versatile colors: a striking black and pink mix, classic black, soft transparent pink, warm brown, and vibrant blue. The durable plastic material ensures longevity, and the simple rectangular shape suits a variety of styles - making them an ideal choice to add a touch of flair to your everyday look.',
		'price'          => '18.95',
		'image'          => 'https://img.muukal.com/goods/1948/black_1.jpg',
		'size'           => 'M',
		'source_url'     => 'https://muukal.com/frame/goods/gid/1948/gcid/4/gn/black-rectangle-simple-plastic-eyeglasses.html',
		'category_slugs' => array( 'mens-eyeglasses', 'all-mens-glasses' ),
	),
	array(
		'gid'            => 1725,
		'source_sku'     => '1735',
		'title'          => 'Eudora Brown Geometric Modish Plastic Eyeglasses',
		'short_name'     => 'Eudora',
		'description'    => "Make a bold statement with Eudora, the Geometric Modish Plastic Eyeglasses. These unisex sunglasses feature a chunky, oversized square frame with distinctive triple rivets on the temples. Available in translucent brown, classic black, vibrant ocean blue, and vivid pink, all paired with chic gradient lenses, they're the perfect modern accessory.",
		'price'          => '22.95',
		'image'          => 'https://img.muukal.com/goods/1725/brown_1.jpg',
		'size'           => 'L',
		'source_url'     => 'https://muukal.com/frame/goods/gid/1725/gcid/10/gn/brown-geometric-modish-plastic-eyeglasses.html',
		'category_slugs' => array( 'mens-sunglasses' ),
	),
	array(
		'gid'            => 1780,
		'source_sku'     => '915',
		'title'          => 'Arvin Golden Oval Retro Metal Eyeglasses',
		'short_name'     => 'Arvin',
		'description'    => "These women's full-rim oval sunglasses are crafted from lightweight metal, making them stylish, durable, and comfortable for all-day wear. They are available in three sophisticated finishes: bright gold with warm amber lenses for a harmonious look, matte black with cool gray lenses for a striking contrast, and brushed silver with neutral gray lenses for a subtle and refined appearance. The soft oval frame accentuates feminine elegance, while the slender metal frame ensures a lightweight and unobtrusive fit. A small gold detail at the end of the temples adds a touch of sophistication - making them the perfect choice to add classic style to any outfit.",
		'price'          => '23.95',
		'image'          => 'https://img.muukal.com/goods/1780/gold_1.jpg',
		'size'           => 'M',
		'source_url'     => 'https://muukal.com/frame/goods/gid/1780/gcid/16/gn/golden-oval-retro-metal-eyeglasses.html',
		'category_slugs' => array( 'mens-sunglasses' ),
	),
	array(
		'gid'            => 2152,
		'source_sku'     => '6002',
		'title'          => 'Hoyle Gray Aviator Classic Metal Eyeglasses',
		'short_name'     => 'Hoyle',
		'description'    => 'These unisex full-rimmed sunglasses feature a classic aviator design. The frames are crafted from premium metal and are available in stylish black, elegant gold, and sophisticated grey. Equipped with spring hinges for a flexible and comfortable fit, and nose pads for a secure and snug fit, they flatter various face shapes and meet the needs of different individuals. They are ideal for outdoor activities such as driving, hiking, and beach vacations.',
		'price'          => '19.95',
		'image'          => 'https://img.muukal.com/goods/2152/gray_1.jpg',
		'size'           => 'L',
		'source_url'     => 'https://muukal.com/frame/goods/gid/2152/gcid/5/gn/gray-aviator-classic-metal-eyeglasses.html',
		'category_slugs' => array( 'mens-sunglasses' ),
	),
	array(
		'gid'            => 1716,
		'source_sku'     => '95655',
		'title'          => 'Taryn Mix Butterfly Classic Sun-clip TR90 Eyeglasses',
		'short_name'     => 'Taryn',
		'description'    => 'The Taryn Butterfly Glasses bring elegance and charm with their medium-sized, full-rim TR90 frame. Designed for both men and women, these glasses feature a graceful butterfly shape, perfect for a chic and bold look. Available in color mix, tortoise, and pink, they add a playful yet sophisticated touch to any style. With spring hinges for added durability and comfort, they support bifocal and progressive lenses, making them both stylish and practical. They offer a sturdy yet fashionable eyewear option.',
		'price'          => '29.95',
		'image'          => 'https://img.muukal.com/goods/1716/mix_1.jpg',
		'size'           => 'M',
		'source_url'     => 'https://muukal.com/frame/goods/gid/1716/gcid/21/gn/mix-butterfly-classic-sun-clip-tr90-eyeglasses.html',
		'category_slugs' => array( 'mens-sunglasses', 'clip-on-glasses-men' ),
	),
	array(
		'gid'            => 2275,
		'source_sku'     => '81339',
		'title'          => 'Lorena Golden Rectangle Elaborate Metal Eyeglasses',
		'short_name'     => 'Lorena',
		'description'    => 'Buy glasses online, Lorena #81339. Visit Muukal Optical today to browse our collection of glasses and sunglasses.',
		'price'          => '29.95',
		'image'          => 'https://img.muukal.com/goods/2275/gold_1.jpg',
		'size'           => 'M',
		'source_url'     => 'https://muukal.com/frame/goods/gid/2275/gcid/16/gn/golden-rectangle-elaborate-metal-eyeglasses.html',
		'category_slugs' => array( 'new-arrivals-men' ),
	),
	array(
		'gid'            => 2272,
		'source_sku'     => 'MV9007',
		'title'          => 'Harold Gray Square Simple TR90 Eyeglasses',
		'short_name'     => 'Harold',
		'description'    => 'Buy glasses online, Harold #MV9007. Visit Muukal Optical today to browse our collection of glasses and sunglasses.',
		'price'          => '18.95',
		'image'          => 'https://img.muukal.com/goods/2272/gray_1.jpg',
		'size'           => 'L',
		'source_url'     => 'https://muukal.com/frame/goods/gid/2272/gcid/5/gn/gray-square-simple-tr90-eyeglasses.html',
		'category_slugs' => array( 'new-arrivals-men' ),
	),
	array(
		'gid'            => 2266,
		'source_sku'     => 'L521',
		'title'          => 'Manetta Floral Square Wide Plastic Eyeglasses',
		'short_name'     => 'Manetta',
		'description'    => 'Buy glasses online, Manetta #L521. Visit Muukal Optical today to browse our collection of glasses and sunglasses.',
		'price'          => '22.95',
		'image'          => 'https://img.muukal.com/goods/2266/floral_1.jpg',
		'size'           => 'L',
		'source_url'     => 'https://muukal.com/frame/goods/gid/2266/gcid/1/gn/floral-square-wide-plastic-eyeglasses.html',
		'category_slugs' => array( 'new-arrivals-men' ),
	),
	array(
		'gid'            => 2263,
		'source_sku'     => 'L411',
		'title'          => 'Kevin Black Rectangle Simple Plastic Eyeglasses',
		'short_name'     => 'Kevin',
		'description'    => 'Buy glasses online, Kevin #L411. Visit Muukal Optical today to browse our collection of glasses and sunglasses.',
		'price'          => '17.95',
		'image'          => 'https://img.muukal.com/goods/2263/black_1.jpg',
		'size'           => 'M',
		'source_url'     => 'https://muukal.com/frame/goods/gid/2263/gcid/4/gn/black-rectangle-simple-plastic-eyeglasses.html',
		'category_slugs' => array( 'new-arrivals-men' ),
	),
	array(
		'gid'            => 2262,
		'source_sku'     => '5566',
		'title'          => 'Brian Black Rectangle Simple Plastic Eyeglasses',
		'short_name'     => 'Brian',
		'description'    => 'Buy glasses online, Brian #5566. Visit Muukal Optical today to browse our collection of glasses and sunglasses.',
		'price'          => '27.95',
		'image'          => 'https://img.muukal.com/goods/2262/black_1.jpg',
		'size'           => 'L',
		'source_url'     => 'https://muukal.com/frame/goods/gid/2262/gcid/4/gn/black-rectangle-simple-plastic-eyeglasses.html',
		'category_slugs' => array( 'new-arrivals-men' ),
	),
	array(
		'gid'            => 2077,
		'source_sku'     => 'MC052',
		'title'          => 'Marley Blue Rectangle Simple Mixed Materials Eyeglasses',
		'short_name'     => 'Marley',
		'description'    => 'These unisex full-rim rectangular eyeglasses are crafted from mixed materials, available in three stylish options: blue, black and clear. Each pair showcases sleek rectangular frames with polished finishes and subtle structural accents, merging functional comfort with a contemporary design. Designed for both men and women, they are perfect for daily use, office environments, and casual outings, seamlessly combining timeless style with long-lasting wearability to suit any gender and occasion.',
		'price'          => '18.95',
		'image'          => 'https://img.muukal.com/goods/2077/blue_1.jpg',
		'size'           => 'L',
		'source_url'     => 'https://muukal.com/frame/goods/gid/2077/gcid/14/gn/blue-rectangle-simple-mixed-materials-eyeglasses.html',
		'category_slugs' => array( 'ultra-light-men' ),
	),
	array(
		'gid'            => 1300,
		'source_sku'     => 'AB6157',
		'title'          => 'Denmark Blue Round Simple TR90 Eyeglasses',
		'short_name'     => 'Denmark',
		'description'    => 'Buy glasses online, Denmark #AB6157. Visit Muukal Optical today to browse our collection of glasses and sunglasses.',
		'price'          => '17.95',
		'image'          => 'https://img.muukal.com/goods/1300/blue_1.jpg',
		'size'           => 'S',
		'source_url'     => 'https://muukal.com/frame/goods/gid/1300/gcid/14/gn/blue-round-simple-tr90-eyeglasses.html',
		'category_slugs' => array( 'ultra-light-men' ),
	),
	array(
		'gid'            => 2079,
		'source_sku'     => 'MC045',
		'title'          => 'Jamilla Black Rectangle Simple TR90 Eyeglasses',
		'short_name'     => 'Jamilla',
		'description'    => 'These unisex full-rim rectangular eyeglasses are crafted from premium TR90 material, known for its lightweight nature and robust durability. They come in two versatile colors: gray and black. Each pair features sleek rectangular frames with polished finishes and subtle structural detailing, merging functional comfort with a modern aesthetic. Suitable for both men and women, they are perfect for daily use, office environments, and casual outings, seamlessly combining timeless style with long-lasting wearability.',
		'price'          => '16.95',
		'image'          => 'https://img.muukal.com/goods/2079/black_1.jpg',
		'size'           => 'L',
		'source_url'     => 'https://muukal.com/frame/goods/gid/2079/gcid/4/gn/black-rectangle-simple-tr90-eyeglasses.html',
		'category_slugs' => array( 'ultra-light-men' ),
	),
	array(
		'gid'            => 1038,
		'source_sku'     => '215154',
		'title'          => 'Starlight Blue Gradient Rectangle Chic TR90 Eyeglasses',
		'short_name'     => 'Starlight',
		'description'    => 'Buy glasses online, Starlight #215154. Visit Muukal Optical today to browse our collection of glasses and sunglasses.',
		'price'          => '21.95',
		'image'          => 'https://img.muukal.com/goods/1038/blue_1.jpg',
		'size'           => 'M',
		'source_url'     => 'https://muukal.com/frame/goods/gid/1038/gcid/14/gn/blue-gradient-rectangle-chic-tr90-eyeglasses.html',
		'category_slugs' => array( 'ultra-light-men' ),
	),
	array(
		'gid'            => 2221,
		'source_sku'     => '91753',
		'title'          => 'Nesse Blue Horn Classic Metal Eyeglasses',
		'short_name'     => 'Nesse',
		'description'    => 'These unisex full-rim square glasses are crafted from lightweight and durable metal, featuring clean lines and a stylish, versatile design suitable for everyday wear. They are equipped with practical spring hinges for comfortable and flexible wear, adjustable nose pads for a personalized fit, and detachable polarized clip-on sunglasses for instant sun protection. The glasses are available in two sophisticated color options: a striking black and red combination and an elegant dark blue. This two-in-one design allows for easy switching between regular glasses and sunglasses, making them ideal for both indoor and outdoor use.',
		'price'          => '38.95',
		'image'          => 'https://img.muukal.com/goods/2221/blue_1.jpg',
		'size'           => 'M',
		'source_url'     => 'https://muukal.com/frame/goods/gid/2221/gcid/14/gn/blue-horn-classic-metal-eyeglasses.html',
		'category_slugs' => array( 'clip-on-glasses-men' ),
	),
	array(
		'gid'            => 1354,
		'source_sku'     => '2328D',
		'title'          => 'Altus Black Butterfly Classic Sun-clip TR90 Eyeglasses',
		'short_name'     => 'Altus',
		'description'    => 'Introducing Altus, the versatile cat-eye eyeglasses for women. The sleek black TR90 full-frame offers durability and comfort. This set includes multiple clip-on lenses in various colors, allowing you to easily switch your style and adapt to different lighting conditions. Perfect for any scenario, from daily wear to outdoor activities, and comes with a convenient carrying pouch for your clip-ons.',
		'price'          => '32.95',
		'image'          => 'https://img.muukal.com/goods/1354/black_1.jpg',
		'size'           => 'M',
		'source_url'     => 'https://muukal.com/frame/goods/gid/1354/gcid/4/gn/black-butterfly-classic-sun-clip-tr90-eyeglasses.html',
		'category_slugs' => array( 'clip-on-glasses-men' ),
	),
	array(
		'gid'            => 1476,
		'source_sku'     => '2350',
		'title'          => 'Blythe Tortoise Cat Eye Modish Sun-clip TR90 Eyeglasses',
		'short_name'     => 'Blythe',
		'description'    => 'Meet Blythe, a versatile cat-eye collection for the modern woman. Crafted from flexible TR90, these full-rim frames come in tortoiseshell, black, pink, red, and blue. With spring hinges for all-day comfort, they seamlessly transition from office days to weekend outings, blending retro charm with contemporary style.',
		'price'          => '25.95',
		'image'          => 'https://img.muukal.com/goods/1476/tortoise_1.jpg',
		'size'           => 'L',
		'source_url'     => 'https://muukal.com/frame/goods/gid/1476/gcid/2/gn/tortoise-cat-eye-modish-sun-clip-tr90-eyeglasses.html',
		'category_slugs' => array( 'clip-on-glasses-men' ),
	),
	array(
		'gid'            => 1884,
		'source_sku'     => '7005',
		'title'          => 'Lenoir Black Aviator Classic Sun-clip Metal Eyeglasses',
		'short_name'     => 'Lenoir',
		'description'    => 'Lenoir redefines the aviator silhouette with a modern, square-inspired design. Crafted from premium metal, this full-rim frame is ultra-light at just 12.3g, offering durability without compromising comfort. Designed for men who appreciate classic style with a contemporary edge, Lenoir features adjustable nose pads for a customized fit. It is compatible with bifocal and progressive lenses, making it a versatile choice for any occasion. Available in black, gold, and silver, this frame pairs effortlessly with both casual and professional looks. Elevate your eyewear collection with Lenoir-where sophistication meets everyday ease.',
		'price'          => '38.95',
		'image'          => 'https://img.muukal.com/goods/1884/black_1.jpg',
		'size'           => 'M',
		'source_url'     => 'https://muukal.com/frame/goods/gid/1884/gcid/4/gn/black-aviator-classic-sun-clip-metal-eyeglasses.html',
		'category_slugs' => array( 'clip-on-glasses-men' ),
	),
);

function ensure_term_with_parent( $name, $slug, $taxonomy, $parent = 0 ) {
	$term = get_term_by( 'slug', $slug, $taxonomy );
	if ( ! $term ) {
		$result = wp_insert_term(
			$name,
			$taxonomy,
			array(
				'slug'   => $slug,
				'parent' => (int) $parent,
			)
		);
		if ( is_wp_error( $result ) ) {
			throw new RuntimeException( $result->get_error_message() );
		}
		$term = get_term( $result['term_id'], $taxonomy );
	} elseif ( (int) $term->parent !== (int) $parent ) {
		wp_update_term(
			$term->term_id,
			$taxonomy,
			array(
				'parent' => (int) $parent,
			)
		);
		$term = get_term( $term->term_id, $taxonomy );
	}
	return $term;
}

function find_shape_slug_from_url( $source_url ) {
	$map = array(
		'aviator'   => 'aviator',
		'browline'  => 'browline',
		'cat-eye'   => 'cat-eye',
		'oval'      => 'oval',
		'rectangle' => 'rectangle',
		'round'     => 'round',
		'square'    => 'square',
		'horn'      => 'horn',
		'butterfly' => 'butterfly',
		'geometric' => 'geometric',
	);
	foreach ( $map as $needle => $slug ) {
		if ( false !== strpos( $source_url, $needle ) ) {
			return $slug;
		}
	}
	return 'rectangle';
}

function find_material_slug_from_url( $source_url ) {
	$map = array(
		'mixed-materials' => 'mixed-materials',
		'tr90'            => 'tr90',
		'metal'           => 'metal',
		'plastic'         => 'plastic',
		'acetate'         => 'acetate',
	);
	foreach ( $map as $needle => $slug ) {
		if ( false !== strpos( $source_url, $needle ) ) {
			return $slug;
		}
	}
	return 'plastic';
}

function find_color_slug_from_url( $source_url ) {
	$map = array(
		'golden'   => 'golden',
		'tortoise' => 'tortoise',
		'floral'   => 'floral',
		'black'    => 'black',
		'blue'     => 'blue',
		'gray'     => 'gray',
		'brown'    => 'brown',
		'mix'      => 'mix',
	);
	foreach ( $map as $needle => $slug ) {
		if ( false !== strpos( $source_url, $needle ) ) {
			return $slug;
		}
	}
	return 'black';
}

function find_style_slug_from_categories( $category_slugs ) {
	if ( in_array( 'clip-on-glasses-men', $category_slugs, true ) || in_array( 'mens-sunglasses', $category_slugs, true ) ) {
		return 'edgy-styles';
	}
	if ( in_array( 'new-arrivals-men', $category_slugs, true ) || in_array( 'ultra-light-men', $category_slugs, true ) ) {
		return 'daily-styles';
	}
	return 'classical-but-better';
}

function find_feature_slug_from_categories( $category_slugs ) {
	if ( in_array( 'clip-on-glasses-men', $category_slugs, true ) ) {
		return 'clip-on';
	}
	if ( in_array( 'ultra-light-men', $category_slugs, true ) ) {
		return 'lightweight';
	}
	return 'progressive';
}

function find_size_slug( $size ) {
	$map = array(
		'S' => 'small',
		'M' => 'medium',
		'L' => 'large',
	);
	return isset( $map[ strtoupper( $size ) ] ) ? $map[ strtoupper( $size ) ] : 'medium';
}

function build_product_attributes_meta() {
	$order = array( 'pa_gender', 'pa_shape', 'pa_style', 'pa_color', 'pa_material', 'pa_size', 'pa_feature' );
	$meta  = array();
	foreach ( $order as $position => $taxonomy ) {
		$meta[ $taxonomy ] = array(
			'name'         => $taxonomy,
			'value'        => '',
			'position'     => $position,
			'is_visible'   => 1,
			'is_variation' => 0,
			'is_taxonomy'  => 1,
		);
	}
	return $meta;
}

function ensure_attachment_for_product( $post_id, $image_url ) {
	$current_thumb_id = get_post_thumbnail_id( $post_id );
	$current_source   = get_post_meta( $post_id, '_muukal_source_image', true );
	if ( $current_thumb_id && $current_source === $image_url ) {
		return $current_thumb_id;
	}

	$attachment_id = media_sideload_image( $image_url, $post_id, null, 'id' );
	if ( is_wp_error( $attachment_id ) ) {
		return 0;
	}

	set_post_thumbnail( $post_id, $attachment_id );
	update_post_meta( $post_id, '_muukal_source_image', esc_url_raw( $image_url ) );
	return $attachment_id;
}

$category_ids = array();
foreach ( $category_tree as $slug => $config ) {
	$parent_id = 0;
	if ( $config['parent'] ) {
		$parent_id = $category_ids[ $config['parent'] ];
	}
	$term                 = ensure_term_with_parent( $config['name'], $slug, 'product_cat', $parent_id );
	$category_ids[ $slug ] = (int) $term->term_id;
}

$created = 0;
$updated = 0;

foreach ( $raw_products as $product ) {
	$sku = 'TEST-MEN-' . $product['gid'];

	$existing_id = wc_get_product_id_by_sku( $sku );
	$postarr     = array(
		'post_type'    => 'product',
		'post_status'  => 'publish',
		'post_title'   => $product['title'],
		'post_name'    => sanitize_title( $product['short_name'] . '-' . $product['gid'] ),
		'post_content' => $product['description'] . "\n\nImported as men category test data from " . $product['source_url'],
		'post_excerpt' => '',
	);

	if ( $existing_id ) {
		$postarr['ID'] = $existing_id;
		$post_id       = wp_update_post( $postarr, true );
		$updated++;
	} else {
		$post_id = wp_insert_post( $postarr, true );
		$created++;
	}

	if ( is_wp_error( $post_id ) ) {
		throw new RuntimeException( $post_id->get_error_message() );
	}

	wp_set_object_terms( $post_id, array( 'simple' ), 'product_type' );

	$product_terms = array( $category_ids['men'] );
	foreach ( $product['category_slugs'] as $category_slug ) {
		$product_terms[] = $category_ids[ $category_slug ];
	}
	wp_set_object_terms( $post_id, array_unique( $product_terms ), 'product_cat' );

	$shape_slug    = find_shape_slug_from_url( $product['source_url'] );
	$material_slug = find_material_slug_from_url( $product['source_url'] );
	$color_slug    = find_color_slug_from_url( $product['source_url'] );
	$style_slug    = find_style_slug_from_categories( $product['category_slugs'] );
	$feature_slug  = find_feature_slug_from_categories( $product['category_slugs'] );
	$size_slug     = find_size_slug( $product['size'] );

	wp_set_object_terms( $post_id, array( 'men' ), 'pa_gender' );
	wp_set_object_terms( $post_id, array( $shape_slug ), 'pa_shape' );
	wp_set_object_terms( $post_id, array( $style_slug ), 'pa_style' );
	wp_set_object_terms( $post_id, array( $color_slug ), 'pa_color' );
	wp_set_object_terms( $post_id, array( $material_slug ), 'pa_material' );
	wp_set_object_terms( $post_id, array( $size_slug ), 'pa_size' );
	wp_set_object_terms( $post_id, array( $feature_slug ), 'pa_feature' );

	update_post_meta( $post_id, '_sku', $sku );
	update_post_meta( $post_id, '_regular_price', $product['price'] );
	update_post_meta( $post_id, '_price', $product['price'] );
	update_post_meta( $post_id, '_stock_status', 'instock' );
	update_post_meta( $post_id, '_visibility', 'visible' );
	update_post_meta( $post_id, '_manage_stock', 'no' );
	update_post_meta( $post_id, '_sold_individually', 'no' );
	update_post_meta( $post_id, '_product_attributes', build_product_attributes_meta() );
	update_post_meta( $post_id, '_muukal_source_url', esc_url_raw( $product['source_url'] ) );
	update_post_meta( $post_id, '_muukal_source_sku', $product['source_sku'] );
	update_post_meta( $post_id, '_muukal_import_group', 'men-test-data' );

	ensure_attachment_for_product( $post_id, $product['image'] );
}

echo wp_json_encode(
	array(
		'created'         => $created,
		'updated'         => $updated,
		'imported_unique' => count( $raw_products ),
		'categories'      => $category_ids,
	),
	JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
) . PHP_EOL;
