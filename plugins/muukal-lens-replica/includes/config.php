<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'muukal_lens_replica_range_options' ) ) {
	function muukal_lens_replica_range_options( $start, $end, $step, $decimals = 2, $include_plus = false ) {
		$options = array();

		for ( $value = $start; $value <= $end + 0.00001; $value += $step ) {
			$formatted = number_format( $value, $decimals, '.', '' );
			if ( $include_plus && $value > 0 ) {
				$formatted = '+' . $formatted;
			}
			$options[] = array( 'value' => $formatted, 'label' => $formatted );
		}

		return $options;
	}
}

if ( ! function_exists( 'muukal_lens_replica_integer_options' ) ) {
	function muukal_lens_replica_integer_options( $start, $end, $placeholder = '' ) {
		$options = array();
		if ( '' !== $placeholder ) {
			$options[] = array( 'value' => '', 'label' => $placeholder );
		}
		for ( $value = $start; $value <= $end; $value++ ) {
			$options[] = array( 'value' => (string) $value, 'label' => (string) $value );
		}
		return $options;
	}
}

if ( ! function_exists( 'muukal_lens_replica_half_step_options' ) ) {
	function muukal_lens_replica_half_step_options( $start, $end, $placeholder_value = '0', $placeholder_label = '' ) {
		$options = array();
		if ( '' !== $placeholder_label ) {
			$options[] = array( 'value' => $placeholder_value, 'label' => $placeholder_label );
		}
		for ( $value = $start; $value <= $end + 0.00001; $value += 0.5 ) {
			$formatted = number_format( $value, 1, '.', '' );
			$options[] = array( 'value' => $formatted, 'label' => $formatted );
		}
		return $options;
	}
}

$birth_year_options = array( array( 'value' => '0', 'label' => 'Birth Year' ) );
for ( $year = 2023; $year >= 1910; $year-- ) {
	$birth_year_options[] = array( 'value' => (string) $year, 'label' => (string) $year );
}

return array(
	'ui' => array(
		'open_button_label' => 'Open Muukal Lens Replica',
		'drawer_kicker'     => 'Crawled step flow',
		'drawer_title'      => 'Lens Selection Test Drawer',
		'product_copy'      => 'Bottom-sheet style wizard with accordion steps, live pricing, dependency locks, readers flow, and simulated add-to-cart payload export.',
	),
	'product' => array(
		'id'           => 1236,
		'code'         => 'ML2995',
		'describe'     => 'Amore',
		'name'         => 'Golden Geometric Exquisite Metal Eyeglasses',
		'frame_price'  => 14.96,
		'color_id'     => 16,
		'color_label'  => 'Golden',
		'size'         => 'M',
		'measurements' => '45 - 23 - 140',
	),
	'usage_options' => array(
		array( 'id' => 1, 'label' => 'Single Vision - Distance', 'short_label' => 'Single Vision', 'description' => 'General use lenses to see far.' ),
		array( 'id' => 2, 'label' => 'Near Vision - Reading', 'short_label' => 'Near Vision', 'description' => 'Reading flow with either full prescription or simple reader power.' ),
		array( 'id' => 3, 'label' => 'Bifocal - With Line', 'short_label' => 'Bifocal', 'description' => 'Near and far vision with a visible line.' ),
		array( 'id' => 4, 'label' => 'Progressive - No Line', 'short_label' => 'Progressive', 'description' => 'Near and far vision without a visible line.' ),
		array( 'id' => 5, 'label' => 'Premium Progressive - No Line', 'short_label' => 'Premium Progressive', 'description' => 'Wider field of vision and easier adaptation than standard progressive.' ),
		array( 'id' => 20, 'label' => 'Non-prescription', 'short_label' => 'Non-prescription', 'description' => 'Plain lenses with optional blue light blocking, tint, and sun options.' ),
	),
	'lens_types' => array(
		1 => array( 'id' => 1, 'label' => 'Clear Lenses', 'description' => 'Traditional transparent lenses for everyday use.', 'requires_color' => false ),
		2 => array( 'id' => 2, 'label' => 'Blue Light Blocking', 'description' => 'Built-in blue light filtering lens option present in script pricing arrays.', 'requires_color' => false, 'dom_hidden' => true ),
		3 => array( 'id' => 3, 'label' => 'Photochromic', 'description' => 'Darkens outdoors and remains clear indoors.', 'requires_color' => true ),
		4 => array( 'id' => 4, 'label' => 'Sunglass Tints', 'description' => 'Solid and gradient tints to convert frames into sunglasses.', 'requires_color' => true ),
		5 => array( 'id' => 5, 'label' => 'Mirror Sunglasses', 'description' => 'Mirror finish lens set present in pricing arrays and color map.', 'requires_color' => true, 'dom_hidden' => true ),
		6 => array( 'id' => 6, 'label' => 'Polarized Sunglasses', 'description' => 'Reduces glare and haze for clearer outdoor vision.', 'requires_color' => true ),
		7 => array( 'id' => 7, 'label' => 'Therapeutic FL-41', 'description' => 'Special tint for migraine-related light sensitivity.', 'requires_color' => true ),
		8 => array( 'id' => 8, 'label' => 'Driving', 'description' => 'Glare-reducing clear driving lenses with water-repellent coating.', 'requires_color' => false ),
	),
	'lens_colors' => array(
		2 => array( 'id' => 2, 'label' => 'Gray', 'group' => 'tint' ),
		3 => array( 'id' => 3, 'label' => 'Brown', 'group' => 'tint' ),
		4 => array( 'id' => 4, 'label' => 'Green', 'group' => 'tint' ),
		5 => array( 'id' => 5, 'label' => 'Pink', 'group' => 'tint' ),
		6 => array( 'id' => 6, 'label' => 'Yellow', 'group' => 'tint' ),
		7 => array( 'id' => 7, 'label' => 'Blue', 'group' => 'tint' ),
		8 => array( 'id' => 8, 'label' => 'Black', 'group' => 'tint' ),
		31 => array( 'id' => 31, 'label' => 'Gradation Fuchsia', 'group' => 'gradient' ),
		32 => array( 'id' => 32, 'label' => 'Gradation Blue', 'group' => 'gradient' ),
		33 => array( 'id' => 33, 'label' => 'Gradation Gray', 'group' => 'gradient' ),
		34 => array( 'id' => 34, 'label' => 'Gradation Brown', 'group' => 'gradient' ),
		35 => array( 'id' => 35, 'label' => 'Gradation Pink', 'group' => 'gradient' ),
		36 => array( 'id' => 36, 'label' => 'Gradation Navy-Blue', 'group' => 'gradient' ),
		37 => array( 'id' => 37, 'label' => 'Gradation Green', 'group' => 'gradient' ),
		38 => array( 'id' => 38, 'label' => 'Gradation Lavender', 'group' => 'gradient' ),
		41 => array( 'id' => 41, 'label' => 'Light Blue-Purple', 'group' => 'two_tone' ),
		42 => array( 'id' => 42, 'label' => 'Light Pink-Blue', 'group' => 'two_tone' ),
		43 => array( 'id' => 43, 'label' => 'Purple-Red', 'group' => 'two_tone' ),
		44 => array( 'id' => 44, 'label' => 'Red-Yellow', 'group' => 'two_tone' ),
		45 => array( 'id' => 45, 'label' => 'Blue-Green', 'group' => 'two_tone' ),
		46 => array( 'id' => 46, 'label' => 'Pink-Yellow', 'group' => 'two_tone' ),
		51 => array( 'id' => 51, 'label' => 'Photochromic Grey', 'group' => 'photochromic' ),
		52 => array( 'id' => 52, 'label' => 'Photochromic Brown', 'group' => 'photochromic' ),
		53 => array( 'id' => 53, 'label' => 'Photochromic Pink', 'group' => 'photochromic' ),
		54 => array( 'id' => 54, 'label' => 'Photochromic Purple', 'group' => 'photochromic' ),
		55 => array( 'id' => 55, 'label' => 'Photochromic Blue', 'group' => 'photochromic' ),
		71 => array( 'id' => 71, 'label' => 'Brown Mirror', 'group' => 'mirror' ),
		72 => array( 'id' => 72, 'label' => 'Orange Mirror', 'group' => 'mirror' ),
		73 => array( 'id' => 73, 'label' => 'Green Mirror', 'group' => 'mirror' ),
		74 => array( 'id' => 74, 'label' => 'Gold Mirror', 'group' => 'mirror' ),
		75 => array( 'id' => 75, 'label' => 'Silver Mirror', 'group' => 'mirror' ),
		76 => array( 'id' => 76, 'label' => 'Blue Mirror', 'group' => 'mirror' ),
		77 => array( 'id' => 77, 'label' => 'Red Mirror', 'group' => 'mirror' ),
		78 => array( 'id' => 78, 'label' => 'Pink Mirror', 'group' => 'mirror' ),
		91 => array( 'id' => 91, 'label' => 'FL-41 25% Tint', 'group' => 'fl41' ),
		92 => array( 'id' => 92, 'label' => 'FL-41 50% Tint', 'group' => 'fl41' ),
		93 => array( 'id' => 93, 'label' => 'FL-41 80% Tint', 'group' => 'fl41' ),
	),
	'lens_type_color_map' => array(
		3 => array( 51, 52, 53, 54, 55 ),
		4 => array( 2, 3, 4, 5, 6, 7, 8, 31, 32, 33, 34, 35, 36, 37, 38, 41, 42, 43, 44, 45, 46 ),
		5 => array( 71, 72, 73, 74, 75, 76, 77, 78 ),
		6 => array( 2, 3, 4, 5, 6, 7, 8 ),
		7 => array( 91, 92, 93 ),
	),
	'lens_indices' => array(
		2 => array( 'id' => 2, 'label' => 'Mid-Index 1.55' ),
		3 => array( 'id' => 3, 'label' => 'High-Index 1.61' ),
		4 => array( 'id' => 4, 'label' => 'Super High-Index 1.67' ),
		5 => array( 'id' => 5, 'label' => 'Ultra High-Index 1.74' ),
	),
	'coatings' => array(
		1 => array( 'id' => 1, 'label' => 'Standard Coatings', 'description' => 'Scratch-resistant coating.' ),
		2 => array( 'id' => 2, 'label' => 'Advanced Coatings', 'description' => 'Super hydrophobic, anti-reflective, and UV-protective.' ),
		3 => array( 'id' => 3, 'label' => 'Ultimate Coatings', 'description' => 'Oleophobic, hydrophobic, anti-reflective, and UV-protective.' ),
	),
	'bluelight' => array(
		'price' => array( 23.95, 9.75, '60% OFF' ),
		'label' => 'Add Blue Light Blocking',
		'description' => 'Available as an add-on except for bifocal, FL-41, and brown photochromic selections.',
	),
	'pricing' => array(
		'lenstype_price' => array(
			1 => array( 1 => array( 0, 0, '', 0, 0, '' ), 2 => array( 15.95, 11.95, '25% OFF', 15.95, 11.95, '25% OFF' ), 3 => array( 0, 28.95, '', 36.95, 27.95, '25% OFF' ), 4 => array( 0, 10.95, '', 0, 13.65, '' ), 5 => array( 0, 30.95, '', 0, 32.95, '' ), 6 => array( 0, 31.95, '', 39.95, 31.95, '20% OFF' ), 7 => array( 0, 49.95, '', 0, 43.95, '' ), 8 => array( 0, 33.95, '', 33.95, 28.95, '15% OFF' ) ),
			2 => array( 1 => array( 0, 0, '', 0, 0, '' ), 2 => array( 15.95, 11.95, '25% OFF', 15.95, 11.95, '25% OFF' ), 3 => array( 0, 28.95, '', 36.95, 27.95, '25% OFF' ), 4 => array( 0, 10.95, '', 0, 13.65, '' ), 5 => array( 0, 30.95, '', 0, 32.95, '' ), 6 => array( 0, 31.95, '', 39.95, 31.95, '20% OFF' ), 7 => array( 0, 49.95, '', 0, 43.95, '' ), 8 => array( 0, 33.95, '', 33.95, 28.95, '15% OFF' ) ),
			3 => array( 1 => array( 0, 0, '', 0, 16.99, '' ), 2 => array( 15.95, 11.95, '25% OFF', 35.75, 28.95, '20% OFF' ), 3 => array( 0, 30.95, '', 0, 46.95, '' ), 4 => array( 0, 10.95, '', 0, 29.95, '' ), 5 => array( 0, 30.99, '', 0, 46.99, '' ), 6 => array( 0, 31.95, '', 49.99, 37.49, '25% OFF' ), 7 => array( 0, 49.95, '', 0, 43.95, '' ), 8 => array( 0, 33.95, '', 33.95, 28.95, '15% OFF' ) ),
			4 => array( 1 => array( 0, 0, '', 0, 35.99, '' ), 2 => array( 15.95, 11.95, '25% OFF', 35.75, 28.95, '20% OFF' ), 3 => array( 0, 30.95, '', 79.95, 63.99, '20% OFF' ), 4 => array( 0, 10.95, '', 0, 46.85, '' ), 5 => array( 0, 30.99, '', 0, 67.95, '' ), 6 => array( 0, 31.95, '', 89.99, 71.49, '20% OFF' ), 7 => array( 0, 55.95, '', 0, 58.95, '' ), 8 => array( 0, 36.95, '', 36.95, 31.95, '15% OFF' ) ),
			5 => array( 1 => array( 0, 0, '', 0, 38.99, '' ), 2 => array( 15.95, 11.95, '25% OFF', 35.75, 28.95, '20% OFF' ), 3 => array( 0, 30.95, '', 89.95, 67.99, '25% OFF' ), 4 => array( 0, 10.95, '', 0, 49.95, '' ), 5 => array( 0, 30.99, '', 0, 73.99, '' ), 6 => array( 0, 31.95, '', 99.99, 79.49, '20% OFF' ), 7 => array( 0, 55.95, '', 0, 58.95, '' ), 8 => array( 0, 36.95, '', 36.95, 31.95, '15% OFF' ) ),
			20 => array( 1 => array( 0, 5.95, '', 0, 5.95, '' ), 2 => array( 15.95, 11.95, '25% OFF', 15.95, 11.95, '25% OFF' ), 3 => array( 0, 28.95, '', 36.95, 27.95, '25% OFF' ), 4 => array( 0, 10.95, '', 0, 13.65, '' ), 5 => array( 0, 30.95, '', 0, 32.95, '' ), 6 => array( 0, 31.95, '', 39.95, 31.95, '20% OFF' ), 7 => array( 0, 49.95, '', 0, 43.95, '' ), 8 => array( 0, 33.95, '', 33.95, 28.95, '15% OFF' ) ),
		),
		'lensindex_price' => array(
			1 => array( 2 => array( 0, 5.95 ), 3 => array( 23.95, 16.65, '30% OFF' ), 4 => array( 0, 39.95 ), 5 => array( 82.95, 49.95, '40% OFF' ) ),
			2 => array( 2 => array( 0, 5.95 ), 3 => array( 23.95, 16.65, '30% OFF' ), 4 => array( 0, 39.95 ), 5 => array( 82.95, 49.95, '40% OFF' ) ),
			3 => array( 2 => array( 0, 9.95 ), 3 => array( 39.95, 31.96, '20% OFF' ), 4 => array( 0, 46.95 ), 5 => array( 0, 82.95 ) ),
			4 => array( 2 => array( 0, 9.95 ), 3 => array( 39.95, 27.96, '25% OFF' ), 4 => array( 0, 58.95 ), 5 => array( 0, 81.95 ) ),
			5 => array( 2 => array( 0, 9.95 ), 3 => array( 41.95, 29.35, '30% OFF' ), 4 => array( 0, 66.95 ), 5 => array( 165.95, 79.65, '52% OFF' ) ),
			20 => array( 2 => array( 0, 0 ), 3 => array( 23.95, 16.65, '30% OFF' ), 4 => array( 0, 39.95 ), 5 => array( 81.95, 49.15, '40% OFF' ) ),
		),
		'coating_price' => array( 1 => array( 0, 0 ), 2 => array( 0, 5.95 ), 3 => array( 18.95, 9.65, '50% OFF' ) ),
		'prex_price'    => array( 1 => array( 0, 9.95 ) ),
	),
	'dependency_rules' => array(
		'step3_enabled_by_usage' => array( 1 => array( 1, 2, 3, 4, 5, 6, 7, 8 ), 2 => array( 1, 2, 3, 4, 5, 6, 7, 8 ), 3 => array( 1, 3, 4 ), 4 => array( 1, 2, 3, 4, 6, 7 ), 5 => array( 1, 2, 3, 4, 6, 7, 8 ), 20 => array( 1, 2, 3, 4, 5, 6, 7, 8 ) ),
		'step4_base_enabled_by_usage' => array( 1 => array( 2, 3, 4, 5 ), 2 => array( 2, 3, 4, 5 ), 3 => array( 2, 3 ), 4 => array( 2, 3, 4, 5 ), 5 => array( 2, 3, 4, 5 ), 20 => array( 2, 3, 4, 5 ) ),
		'bluelight_locks' => array( 'usage_3_bifocal' => true, 'lenstype_7_fl41' => true, 'lenstype_color_52_brown' => true ),
		'progressive_upgrade_prompt' => array( 'from_usage' => 4, 'to_usage' => 5, 'trigger' => 'after_step_5_before_add_to_cart' ),
	),
	'prescription_fields' => array(
		'power'         => array( 'type' => 'choice', 'options' => muukal_lens_replica_range_options( 0.25, 6.75, 0.25, 2, true ) ),
		'od_sph'        => array( 'type' => 'select', 'options' => muukal_lens_replica_range_options( -16, 12, 0.25, 2, true ) ),
		'os_sph'        => array( 'type' => 'select', 'options' => muukal_lens_replica_range_options( -16, 12, 0.25, 2, true ) ),
		'od_cyl'        => array( 'type' => 'select', 'options' => muukal_lens_replica_range_options( -6, 6, 0.25, 2, true ) ),
		'os_cyl'        => array( 'type' => 'select', 'options' => muukal_lens_replica_range_options( -6, 6, 0.25, 2, true ) ),
		'od_axis'       => array( 'type' => 'select', 'options' => muukal_lens_replica_integer_options( 1, 180, 'None' ) ),
		'os_axis'       => array( 'type' => 'select', 'options' => muukal_lens_replica_integer_options( 1, 180, 'None' ) ),
		'od_add'        => array( 'type' => 'select', 'options' => array_merge( array( array( 'value' => '0', 'label' => 'None' ) ), muukal_lens_replica_range_options( 0.75, 3.5, 0.25, 2, true ) ) ),
		'os_add'        => array( 'type' => 'select', 'options' => array_merge( array( array( 'value' => '0', 'label' => 'None' ) ), muukal_lens_replica_range_options( 0.75, 3.5, 0.25, 2, true ) ) ),
		'pd'            => array( 'type' => 'select', 'options' => muukal_lens_replica_integer_options( 52, 76 ) ),
		'od_pd'         => array( 'type' => 'select', 'options' => muukal_lens_replica_half_step_options( 26, 38, '0', 'Right PD' ) ),
		'os_pd'         => array( 'type' => 'select', 'options' => muukal_lens_replica_half_step_options( 26, 38, '0', 'Left PD' ) ),
		'npd'           => array( 'type' => 'select', 'options' => muukal_lens_replica_integer_options( 46, 72 ) ),
		'birth_year'    => array( 'type' => 'select', 'options' => $birth_year_options ),
		'od_prismnum_v' => array( 'type' => 'select', 'options' => array_merge( array( array( 'value' => '0', 'label' => 'n/a' ) ), muukal_lens_replica_range_options( 0.5, 5, 0.25, 2, false ) ) ),
		'os_prismnum_v' => array( 'type' => 'select', 'options' => array_merge( array( array( 'value' => '0', 'label' => 'n/a' ) ), muukal_lens_replica_range_options( 0.5, 5, 0.25, 2, false ) ) ),
		'od_prismdir_v' => array( 'type' => 'select', 'options' => array( array( 'value' => '0', 'label' => 'n/a' ), array( 'value' => 'Up', 'label' => 'Up' ), array( 'value' => 'Down', 'label' => 'Down' ) ) ),
		'os_prismdir_v' => array( 'type' => 'select', 'options' => array( array( 'value' => '0', 'label' => 'n/a' ), array( 'value' => 'Up', 'label' => 'Up' ), array( 'value' => 'Down', 'label' => 'Down' ) ) ),
		'od_prismnum_h' => array( 'type' => 'select', 'options' => array_merge( array( array( 'value' => '0', 'label' => 'n/a' ) ), muukal_lens_replica_range_options( 0.5, 5, 0.25, 2, false ) ) ),
		'os_prismnum_h' => array( 'type' => 'select', 'options' => array_merge( array( array( 'value' => '0', 'label' => 'n/a' ) ), muukal_lens_replica_range_options( 0.5, 5, 0.25, 2, false ) ) ),
		'od_prismdir_h' => array( 'type' => 'select', 'options' => array( array( 'value' => '0', 'label' => 'n/a' ), array( 'value' => 'In', 'label' => 'In' ), array( 'value' => 'Out', 'label' => 'Out' ) ) ),
		'os_prismdir_h' => array( 'type' => 'select', 'options' => array( array( 'value' => '0', 'label' => 'n/a' ), array( 'value' => 'In', 'label' => 'In' ), array( 'value' => 'Out', 'label' => 'Out' ) ) ),
	),
	'payload_fields' => array( 'goodsid', 'color', 'login_key', 'usage', 'lenstype', 'lenstype_color', 'lensindex', 'coating', 'pdkey', 'nearpd', 'prism', 'rxkey', 'rximg', 'od_sph', 'os_sph', 'od_cyl', 'os_cyl', 'od_axis', 'os_axis', 'od_add', 'os_add', 'pd', 'od_pd', 'os_pd', 'npd', 'birth_year', 'od_prismnum_v', 'os_prismnum_v', 'od_prismdir_v', 'os_prismdir_v', 'od_prismnum_h', 'os_prismnum_h', 'od_prismdir_h', 'os_prismdir_h', 'lens_comment', 'rx_name', 'bluelight', 'editlens', 'cartid', 'lensid', 'cartprice', 'readers', 'power' ),
);
