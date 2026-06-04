<?php
/**
 * Icon Renderer Helper
 *
 * Renders icons (SVG or raster images) for buttons, USPs, and UI elements.
 * Optimized for small icons with proper SVG inline rendering.
 *
 * @package epaton
 */

if ( ! function_exists( 'epaton_render_icon' ) ) {
	/**
	 * Render an icon (SVG inline or img tag for raster)
	 *
	 * Detects SVG files and renders them inline for better styling control.
	 * Falls back to img tag for raster images (PNG, JPG, etc.)
	 *
	 * @param array|int $icon ACF image array with 'ID' and 'url' keys, or attachment ID.
	 * @param array     $args {
	 *     Optional configuration.
	 *
	 *     @type string $class CSS class for icon wrapper. Default ''.
	 *     @type string $alt   Alt text for raster images. Default uses image meta alt.
	 *     @type bool   $echo  Echo or return HTML. Default true.
	 * }
	 * @return string|void HTML or void if echo is true.
	 */
	function epaton_render_icon( $icon, $args = [] ) {
		$defaults = [
			'class' => '',
			'alt'   => '',
			'echo'  => true,
		];

		$args = wp_parse_args( $args, $defaults );

		// Handle different input formats
		if ( is_array( $icon ) ) {
			$file_id = isset( $icon['ID'] ) ? intval( $icon['ID'] ) : 0;
			$url     = isset( $icon['url'] ) ? $icon['url'] : '';
			$alt     = ! empty( $args['alt'] ) ? $args['alt'] : ( $icon['alt'] ?? '' );
		} elseif ( is_numeric( $icon ) ) {
			$file_id = intval( $icon );
			$url     = wp_get_attachment_url( $file_id );
			$alt     = ! empty( $args['alt'] ) ? $args['alt'] : get_post_meta( $file_id, '_wp_attachment_image_alt', true );
		} else {
			return;
		}

		// Bail if no valid ID or URL
		if ( empty( $file_id ) || empty( $url ) ) {
			return;
		}

		$file_path = get_attached_file( $file_id );

		if ( ! file_exists( $file_path ) ) {
			return;
		}

		$extension = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );
		$mime_type = mime_content_type( $file_path );

		// Handle SVG - render inline for better control
		if ( $extension === 'svg' && in_array( $mime_type, [ 'image/svg+xml', 'text/plain' ], true ) ) {
			$svg_content = file_get_contents( $file_path );

			if ( strpos( $svg_content, '<svg' ) !== false ) {
				// Add class and accessibility attributes
				$aria_label = $alt ?: 'Icon';
				$class_attr = $args['class'] ? ' class="' . esc_attr( $args['class'] ) . '"' : '';
				
				// Inject class and aria-label into SVG tag
				$svg_tag = preg_replace(
					'/<svg/',
					'<svg' . $class_attr . ' role="img" aria-label="' . esc_attr( $aria_label ) . '"',
					$svg_content,
					1
				);
				
				// Remove XML declaration if present
				$svg_tag = preg_replace( '/<\?xml.*?\?>/', '', $svg_tag );

				if ( $args['echo'] ) {
					echo $svg_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					return;
				}

				return $svg_tag;
			}
		}

		// Handle raster images (PNG, JPG, etc.) - use img tag
		$class_attr = $args['class'] ? ' class="' . esc_attr( $args['class'] ) . '"' : '';
		$html = sprintf(
			'<img src="%s" alt="%s"%s />',
			esc_url( $url ),
			esc_attr( $alt ),
			$class_attr
		);

		if ( $args['echo'] ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		return $html;
	}
}

