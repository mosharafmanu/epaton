<?php

/**
 * Epaton – Responsive Image Sizes
 *
 * Strategy:
 * - Small shared width ladder, all uncropped (height follows uploaded ratio).
 * - Single square crop for logos/cards where a fixed ratio is unavoidable.
 * - CSS aspect-ratio helpers control display frames without generating extra files.
 * - Default WordPress sizes disabled to prevent server bloat.
 *
 * Total custom sizes: 7 (6 uncropped + 1 square)
 */

add_action( 'after_setup_theme', 'epaton_register_image_sizes' );
function epaton_register_image_sizes() {
	// Shared uncropped width ladder – height depends on uploaded image
	add_image_size( 'epaton-100', 100, 9999, false );
	add_image_size( 'epaton-300', 300, 9999, false );
	add_image_size( 'epaton-600', 600, 9999, false );
	add_image_size( 'epaton-750', 750, 9999, false );
	add_image_size( 'epaton-900', 900, 9999, false );
	add_image_size( 'epaton-1200', 1200, 9999, false );

	// Single cropped size – logos, cards, tiles
	add_image_size( 'epaton-square', 366, 366, true );
}

/**
 * Enable responsive images with srcset
 */
add_filter( 'max_srcset_image_width', 'epaton_max_srcset_image_width' );
function epaton_max_srcset_image_width() {
    return 3840; // 2x retina of 1920px design
}

/**
 * Add custom sizes to media library dropdown
 */
add_filter( 'image_size_names_choose', 'epaton_custom_image_sizes_choose' );
function epaton_custom_image_sizes_choose( $sizes ) {
	return array_merge(
		$sizes,
		array(
			'epaton-100'    => __( 'Shared Width 100', 'epaton' ),
			'epaton-300'    => __( 'Shared Width 300', 'epaton' ),
			'epaton-600'    => __( 'Shared Width 600', 'epaton' ),
			'epaton-750'    => __( 'Shared Width 750', 'epaton' ),
			'epaton-900'    => __( 'Shared Width 900', 'epaton' ),
			'epaton-1200'   => __( 'Shared Width 1200', 'epaton' ),
			'epaton-square' => __( 'Square 366', 'epaton' ),
		)
	);
}

/**
 * Enable WebP upload support
 */
add_filter( 'mime_types', 'epaton_enable_webp_upload' );
function epaton_enable_webp_upload( $mimes ) {
    $mimes['webp'] = 'image/webp';
    return $mimes;
}

/**
 * Disable default WordPress image sizes to save server space
 */
add_filter( 'intermediate_image_sizes_advanced', 'epaton_disable_default_image_sizes' );
function epaton_disable_default_image_sizes( $sizes ) {
    unset( $sizes['medium'] );
    unset( $sizes['medium_large'] );
    unset( $sizes['large'] );
    unset( $sizes['1536x1536'] );
    unset( $sizes['2048x2048'] );

    return $sizes;
}
