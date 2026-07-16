<?php
/**
 * Responsive Picture Helper
 *
 * Renders responsive picture elements with srcset and sizes. Supports both
 * predefined image tokens (for theme consistency) and WordPress-registered sizes.
 *
 * @package epaton
 */

if (!function_exists('epaton_render_responsive_picture')) {
    /**
     * Render a responsive picture element
     *
     * Generates optimized responsive images using either predefined size tokens
     * or WordPress-registered image sizes. Supports lazy loading and fetch priority.
     *
     * @param array $image ACF image array with 'ID' and 'url' keys, or field name.
     * @param array $args {
     *     Optional configuration.
     *
     *     @type string $class         CSS class for img tag. Default ''.
     *     @type string $alt           Alt text. Default uses image meta alt.
     *     @type string $sizes         Responsive sizes attribute. Default '(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 33vw'.
     *     @type bool   $lazy          Enable lazy loading. Default true.
     *     @type string $fetchpriority 'high', 'low', or 'auto'. Default 'auto'.
     *     @type bool   $echo          Echo or return HTML. Default true.
     *     @type string $size_group    Predefined token group for consistency. Default null.
     * }
     * @return string|void HTML or void if echo is true.
     */
    function epaton_render_responsive_picture($image, $args = []) {
        $defaults = [
            'class'              => '',
            'alt'                => '',
            'sizes'              => '(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 33vw',
            'lazy'               => true,
            'fetchpriority'      => 'auto',
            'echo'               => true,
            'size_group'         => null,
            'mobile_size_group'  => null,
            'mobile_media_query' => '(max-width: 767px)',
        ];
        $args = wp_parse_args($args, $defaults);

        // Accept array or field name
        if (is_array($image) && isset($image['ID'], $image['url'])) {
            $image_data = $image;
        } else {
            // CUSTOMIZE: If not using ACF, remove this block and require array input
            $image_data = get_field($image);
            if (!$image_data) {
                $image_data = get_sub_field($image);
            }
        }

        if (!$image_data || !isset($image_data['ID'], $image_data['url'])) {
            return;
        }

        $file_id = intval($image_data['ID']);
        $alt = esc_attr($args['alt'] ? $args['alt'] : ($image_data['alt'] ?? ''));
        $class = esc_attr($args['class']);
        $sizes = esc_attr($args['sizes']);

        // Use predefined size token if specified
		if (!empty($args['size_group'])) {
			// Semantic size groups mapped to shared uncropped widths
			$token_map = [
				'hero'         => 'epaton-1200',
				'inner-hero'   => 'epaton-900',
				'half-col-lg'  => 'epaton-900',
				'half-col-sm'  => 'epaton-750',
				'media-content' => 'epaton-750',
				'full-width'   => 'epaton-1200',
				'card'         => 'epaton-600',
				'news-card'    => 'epaton-600',
				'square'       => 'epaton-square',
			];
			$variant_map = [
				'epaton-1200' => [ 'epaton-300', 'epaton-600', 'epaton-750', 'epaton-900', 'epaton-1200' ],
				'epaton-900'  => [ 'epaton-300', 'epaton-600', 'epaton-750', 'epaton-900' ],
				'epaton-750'  => [ 'epaton-300', 'epaton-600', 'epaton-750' ],
				'epaton-600'  => [ 'epaton-100', 'epaton-300', 'epaton-600' ],
				'epaton-square' => [ 'epaton-square' ],
			];

            if (isset($token_map[$args['size_group']])) {
                $token = $token_map[$args['size_group']];
                $token_src = wp_get_attachment_image_src($file_id, $token);

                if (!$token_src) {
                    return;
                }

                $token_url = $token_src[0];
                $token_width = $token_src[1];
                $token_height = $token_src[2];
                $srcset_array = [];

                if (isset($variant_map[$token])) {
                    foreach ($variant_map[$token] as $variant) {
                        $variant_src = wp_get_attachment_image_src($file_id, $variant);
                        if ($variant_src) {
                            $srcset_array[$variant_src[1]] = $variant_src[0] . ' ' . $variant_src[1] . 'w';
                        }
                    }
                }

                $image_meta = wp_get_attachment_metadata($file_id);
                if (!empty($image_meta['width']) && !empty($image_data['url'])) {
                    $srcset_array[intval($image_meta['width'])] = $image_data['url'] . ' ' . intval($image_meta['width']) . 'w';
                }

                if (empty($srcset_array)) {
                    $srcset_array[$token_width] = $token_url . ' ' . $token_width . 'w';
                }
                ksort($srcset_array);
                $srcset = implode(', ', $srcset_array);

                $loading_attr = $args['lazy'] ? 'lazy' : 'eager';
                $fetchpriority_attr = '';

                if ('auto' !== $args['fetchpriority'] && in_array($args['fetchpriority'], ['high', 'low'], true)) {
                    $fetchpriority_attr = ' fetchpriority="' . esc_attr($args['fetchpriority']) . '"';
                }

                $mobile_source = '';
                if (!empty($args['mobile_size_group']) && isset($token_map[$args['mobile_size_group']])) {
                    $mobile_token = $token_map[$args['mobile_size_group']];
                    $mobile_src = wp_get_attachment_image_src($file_id, $mobile_token);
                    $mobile_srcset = '';
                    $mobile_srcset_array = [];

                    if ($mobile_src) {
                        if (isset($variant_map[$mobile_token])) {
                            foreach ($variant_map[$mobile_token] as $mobile_variant) {
                                $mobile_variant_src = wp_get_attachment_image_src($file_id, $mobile_variant);
                                if ($mobile_variant_src) {
                                    $mobile_srcset_array[$mobile_variant_src[1]] = $mobile_variant_src[0] . ' ' . $mobile_variant_src[1] . 'w';
                                }
                            }
                        }

                        if (!empty($image_meta['width']) && !empty($image_data['url'])) {
                            $mobile_srcset_array[intval($image_meta['width'])] = $image_data['url'] . ' ' . intval($image_meta['width']) . 'w';
                        }

                        if (empty($mobile_srcset_array)) {
                            $mobile_srcset_array[$mobile_src[1]] = $mobile_src[0] . ' ' . $mobile_src[1] . 'w';
                        }
                        ksort($mobile_srcset_array);
                        $mobile_srcset = implode(', ', $mobile_srcset_array);

                        $mobile_source = sprintf(
                            '<source media="%s" srcset="%s" sizes="100vw" />',
                            esc_attr($args['mobile_media_query']),
                            esc_attr($mobile_srcset)
                        );
                    }
                }

                $html = sprintf(
                    '<picture>%s<img src="%s" srcset="%s" sizes="%s" width="%d" height="%d" alt="%s" class="%s" loading="%s" decoding="async"%s /></picture>',
                    $mobile_source,
                    esc_url($token_url),
                    esc_attr($srcset),
                    esc_attr($args['sizes']),
                    esc_attr($token_width),
                    esc_attr($token_height),
                    $alt,
                    $class,
                    esc_attr($loading_attr),
                    $fetchpriority_attr
                );

                if ($args['echo']) {
                    echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    return;
                }

                return $html;
            }
        }

        // Handle SVG images
        $file_path = get_attached_file($file_id);

        if (!file_exists($file_path)) {
            return;
        }

        $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        $mime_type = mime_content_type($file_path);

        if ($extension === 'svg' && in_array($mime_type, ['image/svg+xml', 'text/plain'], true)) {
            $svg_content = file_get_contents($file_path);

            if (strpos($svg_content, '<svg') !== false) {
                $aria_label = $alt ?: 'SVG Image';
                $class_attr = $class ? ' class="' . $class . '"' : '';
                $svg_tag = preg_replace('/<svg/', '<svg' . $class_attr . ' role="img" aria-label="' . $aria_label . '"', $svg_content, 1);
                $svg_tag = preg_replace('/<\?xml.*?\?>/', '', $svg_tag);

                if ($args['echo']) {
                    echo $svg_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    return;
                }

                return $svg_tag;
            }
        } else {
            // Build srcset from registered sizes
            $srcset_array = [];
            $image_meta = wp_get_attachment_metadata($file_id);

            if ($image_meta) {
                $original_width = $image_meta['width'] ?? 0;
                $original_height = $image_meta['height'] ?? 0;

                // Add original size first
                if ($original_width > 0) {
                    $srcset_array[] = $image_data['url'] . ' ' . $original_width . 'w';
                }

                // Add registered image sizes
                if (isset($image_meta['sizes']) && !empty($image_meta['sizes'])) {
                    foreach ($image_meta['sizes'] as $size_name => $size_data) {
                        $size_url = wp_get_attachment_image_src($file_id, $size_name);
                        if ($size_url && isset($size_url[1])) {
                            $srcset_array[] = $size_url[0] . ' ' . $size_url[1] . 'w';
                        }
                    }
                }

                // Generate scaled variants if we need more sizes
                if (count($srcset_array) < 4 && $original_width > 0) {
                    $scales = [0.5, 0.75, 1.5, 2];

                    foreach ($scales as $scale) {
                        $scaled_width = intval($original_width * $scale);
                        $size_exists = false;

                        foreach ($srcset_array as $srcset_item) {
                            if (strpos($srcset_item, $scaled_width . 'w') !== false) {
                                $size_exists = true;
                                break;
                            }
                        }

                        if (!$size_exists) {
                            $scaled_image = wp_get_attachment_image_src($file_id, [$scaled_width, $original_height]);
                            if ($scaled_image) {
                                $srcset_array[] = $scaled_image[0] . ' ' . $scaled_image[1] . 'w';
                            }
                        }
                    }
                }
            } else {
                // Fallback to original
                $srcset_array[] = $image_data['url'] . ' 1920w';
            }

            // Sort by width ascending
            usort($srcset_array, function ($a, $b) {
                preg_match('/(\d+)w/', $a, $matches_a);
                preg_match('/(\d+)w/', $b, $matches_b);
                $width_a = intval($matches_a[1] ?? 0);
                $width_b = intval($matches_b[1] ?? 0);
                return $width_a - $width_b;
            });

            $srcset = implode(', ', $srcset_array);

            // Build picture element
            $html = '<picture>';
            $html .= '<img ';
            $html .= 'src="' . esc_url($image_data['url']) . '" ';
            $html .= 'srcset="' . esc_attr($srcset) . '" ';
            $html .= 'sizes="' . $sizes . '" ';
            $html .= 'alt="' . $alt . '" ';
			if ( ! empty( $image_meta['width'] ) && ! empty( $image_meta['height'] ) ) {
				$html .= 'width="' . absint( $image_meta['width'] ) . '" ';
				$html .= 'height="' . absint( $image_meta['height'] ) . '" ';
			}
			$html .= 'decoding="async" ';
            if ($class) {
                $html .= 'class="' . $class . '" ';
            }
            if ($args['lazy']) {
                $html .= 'loading="lazy" ';
            }
            if ('auto' !== $args['fetchpriority'] && in_array($args['fetchpriority'], ['high', 'low'], true)) {
                $html .= 'fetchpriority="' . esc_attr($args['fetchpriority']) . '" ';
            }
            $html .= '/>';
            $html .= '</picture>';

            if ($args['echo']) {
                echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            } else {
                return $html;
            }
        }
    }
}
