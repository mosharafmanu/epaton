<?php
/**
 * Front-end performance helpers.
 *
 * @package epaton
 */

if ( ! function_exists( 'epaton_get_page_layouts' ) ) {
	/**
	 * Return the current page's flexible content rows without advancing ACF loops.
	 *
	 * @return array
	 */
	function epaton_get_page_layouts() {
		static $layouts = null;

		if ( null !== $layouts ) {
			return $layouts;
		}

		$layouts = [];

		if ( ! is_singular() || ! function_exists( 'get_field' ) ) {
			return $layouts;
		}

		$rows = get_field( 'cms', get_queried_object_id() );

		if ( is_array( $rows ) ) {
			$layouts = $rows;
		}

		return $layouts;
	}
}

if ( ! function_exists( 'epaton_page_has_contact_form' ) ) {
	/**
	 * Check whether the current response can render a contact form.
	 *
	 * @return bool
	 */
	function epaton_page_has_contact_form() {
		foreach ( epaton_get_page_layouts() as $layout ) {
			if ( 'contact_panel' === ( $layout['acf_fc_layout'] ?? '' ) ) {
				return true;
			}
		}

		$post = get_post();

		return $post && has_shortcode( (string) $post->post_content, 'contact-form-7' );
	}
}

if ( ! function_exists( 'epaton_page_has_video' ) ) {
	/**
	 * Check flexible content recursively for a configured video.
	 *
	 * @return bool
	 */
	function epaton_page_has_video() {
		$contains_video = static function ( $value ) use ( &$contains_video ) {
			if ( ! is_array( $value ) ) {
				return false;
			}

			foreach ( $value as $key => $item ) {
				if ( is_string( $key ) && '_media_type' === substr( $key, -11 ) && 'video' === $item ) {
					return true;
				}

				if ( is_array( $item ) && $contains_video( $item ) ) {
					return true;
				}
			}

			return false;
		};

		return $contains_video( epaton_get_page_layouts() );
	}
}

/**
 * Remove Contact Form 7 assets from pages that cannot render a form.
 */
function epaton_conditionally_dequeue_plugin_assets() {
	if ( epaton_page_has_contact_form() ) {
		return;
	}

	foreach ( [ 'contact-form-7', 'contact-form-7-rtl' ] as $handle ) {
		wp_dequeue_style( $handle );
	}

	foreach ( [ 'contact-form-7', 'swv' ] as $handle ) {
		wp_dequeue_script( $handle );
	}
}
add_action( 'wp_enqueue_scripts', 'epaton_conditionally_dequeue_plugin_assets', 99 );

/**
 * Preload the normal variable font used by above-the-fold content.
 */
function epaton_preload_primary_font() {
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( get_template_directory_uri() . '/assets/fonts/instrument-sans/instrument-sans-latin-variable.woff2' )
	);
}
add_action( 'wp_head', 'epaton_preload_primary_font', 1 );

if ( ! function_exists( 'epaton_get_hero_preload' ) ) {
	/**
	 * Get preload data for the first hero image.
	 *
	 * @return array|false
	 */
	function epaton_get_hero_preload() {
		$layouts = epaton_get_page_layouts();
		$first   = $layouts[0] ?? [];
		$layout  = $first['acf_fc_layout'] ?? '';

		if ( 'hero_section' === $layout ) {
			$image = $first['hero_image'] ?? [];
			$media_type = $first['hero_media_type'] ?? 'image';
			$size  = 'epaton-1200';
			$sizes = '(max-width: 767px) 100vw, 62vw';
		} elseif ( 'inner_hero' === $layout ) {
			$image = $first['inner_hero_image'] ?? [];
			$media_type = $first['inner_hero_media_type'] ?? 'image';
			$size  = 'epaton-900';
			$sizes = '(max-width: 767px) 100vw, 50vw';
		} else {
			return false;
		}

		if ( 'video' === $media_type ) {
			return false;
		}

		$image_id = is_array( $image ) ? (int) ( $image['ID'] ?? 0 ) : (int) $image;

		if ( ! $image_id ) {
			return false;
		}

		$src = wp_get_attachment_image_url( $image_id, $size );

		if ( ! $src ) {
			return false;
		}

		return [
			'src'    => $src,
			'srcset' => wp_get_attachment_image_srcset( $image_id, $size ),
			'sizes'  => $sizes,
		];
	}
}

/**
 * Preload the above-the-fold hero image so discovery does not wait for body parsing.
 */
function epaton_preload_hero_image() {
	$preload = epaton_get_hero_preload();

	if ( ! $preload ) {
		return;
	}

	printf(
		'<link rel="preload" as="image" href="%1$s"%2$s imagesizes="%3$s">' . "\n",
		esc_url( $preload['src'] ),
		$preload['srcset'] ? ' imagesrcset="' . esc_attr( $preload['srcset'] ) . '"' : '',
		esc_attr( $preload['sizes'] )
	);
}
add_action( 'wp_head', 'epaton_preload_hero_image', 2 );
