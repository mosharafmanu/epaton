<?php
/**
 * Video Renderer Helper Functions
 *
 * Renders videos from multiple sources with different behaviors:
 * - Self-hosted (WordPress media library)
 * - YouTube (iframe embed)
 * - Vimeo (MP4 URL)
 * - CDN/External URL
 *
 * Supports three behaviors:
 * - autoplay: Auto-play with custom controls
 * - hover: Play on hover, pause on leave
 * - onclick-popup: Click to open in modal
 *
 * @package epaton
 */

if ( ! function_exists( 'epaton_render_video' ) ) {
	/**
	 * Render video with behavior support
	 *
	 * @param string|array $video_field ACF field name or video data array
	 * @param array $args {
	 *     Optional render arguments.
	 *
	 *     @type string $behavior           Video behavior: 'autoplay', 'hover', 'onclick-popup'. Default 'autoplay'.
	 *     @type bool   $autoplay           Auto-play video on load. Default true.
	 *     @type bool   $autoplay_on_scroll Play when scrolled into viewport. Default false.
	 *     @type string $class              Additional CSS classes for video element. Default ''.
	 *     @type string $container_class    Additional CSS classes for container. Default ''.
	 *     @type bool   $controls           Show custom controls (autoplay behavior only). Default true.
	 *     @type bool   $popup_autoplay     Auto-play in popup (onclick-popup only). Default true.
	 *     @type bool   $popup_controls     Show controls in popup (onclick-popup only). Default true.
	 *     @type bool   $muted              Mute video. Default false.
	 *     @type bool   $loop               Loop video. Default false.
	 *     @type string $width              Video width. Default '100%'.
	 *     @type string $height             Video height. Default 'auto'.
	 *     @type bool   $echo               Echo or return HTML. Default true.
	 * }
	 * @return string|void HTML video element
	 */
	function epaton_render_video( $video_field, $args = [] ) {
		// CUSTOMIZE: Default video parameters
		$defaults = [
			'behavior'           => 'autoplay',
			'autoplay'           => true,
			'autoplay_on_scroll' => false,
			'class'              => '',
			'container_class'    => '',
			'controls'           => true,
			'popup_autoplay'     => true,
			'popup_controls'     => true,
			'muted'              => false,
			'loop'               => false,
			'width'              => '100%',
			'height'             => 'auto',
			'echo'               => true,
		];
		$args = wp_parse_args( $args, $defaults );

		// Get video data
		if ( is_string( $video_field ) ) {
			$video_data = get_field( $video_field );
		} else {
			$video_data = $video_field;
		}

		// Validate video data
		if ( empty( $video_data ) || ! is_array( $video_data ) ) {
			return '';
		}

		$video_source = $video_data['video_source'] ?? '';

		if ( empty( $video_source ) ) {
			return '';
		}

		// Generate video HTML based on source
		$video_html = '';
		switch ( $video_source ) {
			case 'self_host':
				$video_html = epaton_render_self_hosted_video( $video_data, $args );
				break;
			case 'youtube':
				$video_html = epaton_render_youtube_video( $video_data, $args );
				break;
			case 'vimeo':
				$video_html = epaton_render_vimeo_video( $video_data, $args );
				break;
			case 'cdn':
				$video_html = epaton_render_cdn_video( $video_data, $args );
				break;
		}

		if ( empty( $video_html ) ) {
			return '';
		}

		// Build container
		$container_class = 'video-container';
		if ( $args['container_class'] ) {
			$container_class .= ' ' . esc_attr( $args['container_class'] );
		}

		$html = '<div class="' . $container_class . '" data-behavior="' . esc_attr( $args['behavior'] ) . '">';

		// Add video HTML
		$html .= $video_html;

		// Add play overlay for onclick-popup behavior
		if ( 'onclick-popup' === $args['behavior'] ) {
			$html .= '<div class="video-play-overlay">';
			$html .= '<button class="video-play-button" aria-label="' . esc_attr__( 'Play Video', 'epaton' ) . '">';
			$html .= '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="26" viewBox="0 0 22 26" fill="none">';
			$html .= '<path d="M4.07991 0.394447C3.25275 -0.114144 2.21321 -0.130911 1.36928 0.344147C0.525358 0.819204 0 1.71343 0 2.6859V22.3589C0 23.3313 0.525358 24.2256 1.36928 24.7006C2.21321 25.1757 3.25275 25.1533 4.07991 24.6503L20.176 14.8138C20.9752 14.3276 21.4614 13.4613 21.4614 12.5224C21.4614 11.5835 20.9752 10.7228 20.176 10.2309L4.07991 0.394447Z" fill="#fff"/>';
			$html .= '</svg>';
			$html .= '</button>';
			$html .= '</div>';
		}

		// Add custom controls for autoplay behavior
		if ( 'autoplay' === $args['behavior'] && $args['controls'] && 'youtube' !== $video_source ) {
			// Add low power mode overlay (for autoplay-on-scroll or regular autoplay)
			if ( $args['autoplay_on_scroll'] || $args['autoplay'] ) {
				$html .= '<div class="video-low-power-overlay">';
				$html .= '<button class="video-play-button low-power-play-btn" aria-label="' . esc_attr__( 'Play Video', 'epaton' ) . '">';
				$html .= '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="26" viewBox="0 0 22 26" fill="none">';
				$html .= '<path d="M4.07991 0.394447C3.25275 -0.114144 2.21321 -0.130911 1.36928 0.344147C0.525358 0.819204 0 1.71343 0 2.6859V22.3589C0 23.3313 0.525358 24.2256 1.36928 24.7006C2.21321 25.1757 3.25275 25.1533 4.07991 24.6503L20.176 14.8138C20.9752 14.3276 21.4614 13.4613 21.4614 12.5224C21.4614 11.5835 20.9752 10.7228 20.176 10.2309L4.07991 0.394447Z" fill="black"/>';
				$html .= '</svg>';
				$html .= '</button>';
				$html .= '</div>';
			}

			// Add custom controls
			if ( function_exists( 'epaton_render_video_autoplay_controls' ) ) {
				$html .= epaton_render_video_autoplay_controls( [ 'echo' => false ] );
			}
		}

		$html .= '</div>';

		if ( $args['echo'] ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $html;
		}
	}
}

if ( ! function_exists( 'epaton_render_self_hosted_video' ) ) {
	/**
	 * Render self-hosted video with behavior support
	 *
	 * @param array $video_data Video data from ACF
	 * @param array $args Render arguments
	 * @return string HTML video element
	 */
	function epaton_render_self_hosted_video( $video_data, $args ) {
		$video_file = $video_data['video_self_host_file'] ?? '';
		$poster = $video_data['video_self_host_poster'] ?? '';

		if ( empty( $video_file ) || ! isset( $video_file['url'] ) ) {
			return '';
		}

		$video_url = esc_url( $video_file['url'] );
		$poster_url = '';
		if ( $poster && isset( $poster['url'] ) ) {
			$poster_url = esc_url( $poster['url'] );
		}

		// Fallback: If no poster and autoplay_on_scroll is enabled, try to get product featured image
		if ( empty( $poster_url ) && $args['autoplay_on_scroll'] ) {
			global $product;
			if ( $product && method_exists( $product, 'get_image_id' ) ) {
				$image_id = $product->get_image_id();
				if ( $image_id ) {
					$poster_url = wp_get_attachment_image_url( $image_id, 'full' );
				}
			}
		}

		$autoplay = false;
		$muted = $args['muted']; // Use the muted parameter from args

		// Handle autoplay behavior with parameters
		if ( 'autoplay' === $args['behavior'] ) {
			// Always add autoplay attribute for 'autoplay' behavior to load first frame/poster
			// JavaScript will pause immediately if autoplay parameter is false
			$autoplay = true;

			// For autoplay-on-scroll or when autoplay is true, mute is required by browser policy
			if ( $args['autoplay_on_scroll'] || $args['autoplay'] ) {
				$muted = true;
			}
		} elseif ( 'hover' === $args['behavior'] ) {
			$autoplay = false;
			$muted = true; // Browsers require muted for hover autoplay
		} elseif ( 'onclick-popup' === $args['behavior'] ) {
			$autoplay = false;
			// If autoplay is enabled, mute is required by browser autoplay policy
			if ( $args['autoplay'] ) {
				$muted = true;
			}
			// Otherwise use the muted parameter from args
		}

		$html = '<video ';
		$html .= 'width="' . esc_attr( $args['width'] ) . '" ';
		$html .= 'height="' . esc_attr( $args['height'] ) . '" ';
		if ( $poster_url ) {
			$html .= 'poster="' . $poster_url . '" ';
		}
		// For autoplay behavior, don't show default controls (we'll use custom controls)
		if ( $args['controls'] && 'autoplay' !== $args['behavior'] ) {
			$html .= 'controls ';
		}
		if ( $autoplay ) {
			$html .= 'autoplay ';
		}
		if ( $muted ) {
			$html .= 'muted ';
		}
		if ( $args['loop'] ) {
			$html .= 'loop ';
		}
		// Add preload attribute for autoplay-on-scroll to ensure poster/metadata loads
		if ( $args['autoplay_on_scroll'] ) {
			$html .= 'preload="metadata" ';
		}
		$html .= 'playsinline '; // Mobile inline playback
		$html .= 'data-behavior="' . esc_attr( $args['behavior'] ) . '" ';
		// Store the desired muted state for JavaScript to apply after autoplay
		$html .= 'data-desired-muted="' . ( $args['muted'] ? 'true' : 'false' ) . '" ';
		// Add data-autoplay-on-scroll attribute for JavaScript to handle
		if ( $args['autoplay_on_scroll'] ) {
			$html .= 'data-autoplay-on-scroll="true" ';
		}
		// Add data attribute to pause immediately if autoplay is false
		if ( 'autoplay' === $args['behavior'] && ! $args['autoplay'] && ! $args['autoplay_on_scroll'] ) {
			$html .= 'data-pause-on-load="true" ';
		}
		// Add data attributes for onclick-popup behavior
		if ( 'onclick-popup' === $args['behavior'] ) {
			// Popup video autoplay
			if ( $args['popup_autoplay'] ) {
				$html .= 'data-popup-autoplay="true" ';
			}
			// Popup video controls
			if ( $args['popup_controls'] ) {
				$html .= 'data-popup-controls="true" ';
			}
		}
		if ( $args['class'] ) {
			$html .= 'class="' . esc_attr( $args['class'] ) . '" ';
		}
		$html .= '>';
		$html .= '<source src="' . $video_url . '" type="' . esc_attr( $video_file['mime_type'] ) . '">';
		$html .= esc_html__( 'Your browser does not support the video tag.', 'epaton' );
		$html .= '</video>';

		return $html;
	}
}

if ( ! function_exists( 'epaton_render_youtube_video' ) ) {
	/**
	 * Render YouTube video with behavior support
	 *
	 * Note: YouTube videos use iframe embed and don't support hover behavior.
	 * For onclick-popup, the thumbnail is shown with play overlay.
	 *
	 * @param array $video_data Video data from ACF
	 * @param array $args Render arguments
	 * @return string HTML iframe element
	 */
	function epaton_render_youtube_video( $video_data, $args ) {
		$youtube_url = $video_data['video_youtube_url'] ?? '';

		if ( ! $youtube_url ) {
			return '';
		}

		// Extract video ID from various YouTube URL formats
		$video_id = '';
		if ( preg_match( '/youtube\.com\/watch\?v=([^\&\?\/]+)/', $youtube_url, $matches ) ) {
			$video_id = $matches[1];
		} elseif ( preg_match( '/youtube\.com\/embed\/([^\&\?\/]+)/', $youtube_url, $matches ) ) {
			$video_id = $matches[1];
		} elseif ( preg_match( '/youtu\.be\/([^\&\?\/]+)/', $youtube_url, $matches ) ) {
			$video_id = $matches[1];
		}

		if ( ! $video_id ) {
			return '';
		}

		// For onclick-popup, show thumbnail with play overlay
		if ( 'onclick-popup' === $args['behavior'] ) {
			$thumbnail_url = 'https://img.youtube.com/vi/' . $video_id . '/maxresdefault.jpg';
			$html = '<img src="' . esc_url( $thumbnail_url ) . '" alt="' . esc_attr__( 'Video Thumbnail', 'epaton' ) . '" ';
			$html .= 'data-youtube-id="' . esc_attr( $video_id ) . '" ';
			if ( $args['class'] ) {
				$html .= 'class="' . esc_attr( $args['class'] ) . '" ';
			}
			$html .= 'style="width: 100%; height: auto; display: block;">';
			return $html;
		}

		// Build YouTube embed URL with parameters
		$embed_url = 'https://www.youtube.com/embed/' . $video_id;
		$params = [];

		// CUSTOMIZE: YouTube embed parameters
		if ( $args['autoplay'] ) {
			$params[] = 'autoplay=1';
		}
		if ( $args['muted'] ) {
			$params[] = 'mute=1';
		}
		if ( $args['loop'] ) {
			$params[] = 'loop=1';
			$params[] = 'playlist=' . $video_id; // Required for loop
		}
		$params[] = 'playsinline=1';
		$params[] = 'rel=0'; // Don't show related videos from other channels

		if ( ! empty( $params ) ) {
			$embed_url .= '?' . implode( '&', $params );
		}

		$html = '<iframe ';
		$html .= 'width="' . esc_attr( $args['width'] ) . '" ';
		$html .= 'height="' . esc_attr( $args['height'] ) . '" ';
		$html .= 'src="' . esc_url( $embed_url ) . '" ';
		$html .= 'frameborder="0" ';
		$html .= 'allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" ';
		$html .= 'allowfullscreen>';
		$html .= '</iframe>';

		return $html;
	}
}

if ( ! function_exists( 'epaton_render_vimeo_video' ) ) {
	/**
	 * Render Vimeo video with behavior support
	 *
	 * Uses HTML5 video tag with MP4 URL (Vimeo CDN or API).
	 *
	 * @param array $video_data Video data from ACF
	 * @param array $args Render arguments
	 * @return string HTML video element
	 */
	function epaton_render_vimeo_video( $video_data, $args ) {
		$vimeo_url = $video_data['video_vimeo_url'] ?? '';
		$poster = $video_data['video_vimeo_poster'] ?? '';

		if ( ! $vimeo_url ) {
			return '';
		}

		$poster_url = '';
		if ( $poster && isset( $poster['url'] ) ) {
			$poster_url = esc_url( $poster['url'] );
		}

		// Fallback: If no poster and autoplay_on_scroll is enabled, try to get product featured image
		if ( empty( $poster_url ) && $args['autoplay_on_scroll'] ) {
			global $product;
			if ( $product && method_exists( $product, 'get_image_id' ) ) {
				$image_id = $product->get_image_id();
				if ( $image_id ) {
					$poster_url = wp_get_attachment_image_url( $image_id, 'full' );
				}
			}
		}

		$autoplay = false;
		$muted = $args['muted']; // Use the muted parameter from args

		// Handle autoplay behavior with parameters
		if ( 'autoplay' === $args['behavior'] ) {
			// Always add autoplay attribute for 'autoplay' behavior to load first frame/poster
			// JavaScript will pause immediately if autoplay parameter is false
			$autoplay = true;

			// For autoplay-on-scroll or when autoplay is true, mute is required by browser policy
			if ( $args['autoplay_on_scroll'] || $args['autoplay'] ) {
				$muted = true;
			}
		} elseif ( 'hover' === $args['behavior'] ) {
			$autoplay = false;
			$muted = true; // Browsers require muted for hover autoplay
		} elseif ( 'onclick-popup' === $args['behavior'] ) {
			$autoplay = false;
			// If autoplay is enabled, mute is required by browser autoplay policy
			if ( $args['autoplay'] ) {
				$muted = true;
			}
			// Otherwise use the muted parameter from args
		}

		$html = '<video ';
		$html .= 'width="' . esc_attr( $args['width'] ) . '" ';
		$html .= 'height="' . esc_attr( $args['height'] ) . '" ';
		if ( $poster_url ) {
			$html .= 'poster="' . $poster_url . '" ';
		}
		// For autoplay behavior, don't show default controls (we'll use custom controls)
		if ( $args['controls'] && 'autoplay' !== $args['behavior'] ) {
			$html .= 'controls ';
		}
		if ( $autoplay ) {
			$html .= 'autoplay ';
		}
		if ( $muted ) {
			$html .= 'muted ';
		}
		if ( $args['loop'] ) {
			$html .= 'loop ';
		}
		// Add preload attribute for autoplay-on-scroll to ensure poster/metadata loads
		if ( $args['autoplay_on_scroll'] ) {
			$html .= 'preload="metadata" ';
		}
		$html .= 'playsinline '; // Mobile inline playback
		$html .= 'data-behavior="' . esc_attr( $args['behavior'] ) . '" ';
		// Store the desired muted state for JavaScript to apply after autoplay
		$html .= 'data-desired-muted="' . ( $args['muted'] ? 'true' : 'false' ) . '" ';
		// Add data-autoplay-on-scroll attribute for JavaScript to handle
		if ( $args['autoplay_on_scroll'] ) {
			$html .= 'data-autoplay-on-scroll="true" ';
		}
		// Add data attribute to pause immediately if autoplay is false
		if ( 'autoplay' === $args['behavior'] && ! $args['autoplay'] && ! $args['autoplay_on_scroll'] ) {
			$html .= 'data-pause-on-load="true" ';
		}
		// Add data attributes for onclick-popup behavior
		if ( 'onclick-popup' === $args['behavior'] ) {
			// Popup video autoplay
			if ( $args['popup_autoplay'] ) {
				$html .= 'data-popup-autoplay="true" ';
			}
			// Popup video controls
			if ( $args['popup_controls'] ) {
				$html .= 'data-popup-controls="true" ';
			}
		}
		if ( $args['class'] ) {
			$html .= 'class="' . esc_attr( $args['class'] ) . '" ';
		}
		$html .= '>';
		$html .= '<source src="' . esc_url( $vimeo_url ) . '" type="video/mp4">';
		$html .= esc_html__( 'Your browser does not support the video tag.', 'epaton' );
		$html .= '</video>';

		return $html;
	}
}

if ( ! function_exists( 'epaton_render_cdn_video' ) ) {
	/**
	 * Render CDN-hosted video with behavior support
	 *
	 * @param array $video_data Video data from ACF
	 * @param array $args Render arguments
	 * @return string HTML video element
	 */
	function epaton_render_cdn_video( $video_data, $args ) {
		$cdn_url = $video_data['video_cdn_url'] ?? '';
		$poster = $video_data['video_cdn_poster'] ?? '';

		if ( ! $cdn_url ) {
			return '';
		}

		$poster_url = '';
		if ( $poster && isset( $poster['url'] ) ) {
			$poster_url = esc_url( $poster['url'] );
		}

		// Fallback: If no poster and autoplay_on_scroll is enabled, try to get product featured image
		if ( empty( $poster_url ) && $args['autoplay_on_scroll'] ) {
			global $product;
			if ( $product && method_exists( $product, 'get_image_id' ) ) {
				$image_id = $product->get_image_id();
				if ( $image_id ) {
					$poster_url = wp_get_attachment_image_url( $image_id, 'full' );
				}
			}
		}

		$autoplay = false;
		$muted = $args['muted']; // Use the muted parameter from args

		// Handle autoplay behavior with parameters
		if ( 'autoplay' === $args['behavior'] ) {
			// Always add autoplay attribute for 'autoplay' behavior to load first frame/poster
			// JavaScript will pause immediately if autoplay parameter is false
			$autoplay = true;

			// For autoplay-on-scroll or when autoplay is true, mute is required by browser policy
			if ( $args['autoplay_on_scroll'] || $args['autoplay'] ) {
				$muted = true;
			}
		} elseif ( 'hover' === $args['behavior'] ) {
			$autoplay = false;
			$muted = true; // Browsers require muted for hover autoplay
		} elseif ( 'onclick-popup' === $args['behavior'] ) {
			$autoplay = false;
			// If autoplay is enabled, mute is required by browser autoplay policy
			if ( $args['autoplay'] ) {
				$muted = true;
			}
			// Otherwise use the muted parameter from args
		}

		$html = '<video ';
		$html .= 'width="' . esc_attr( $args['width'] ) . '" ';
		$html .= 'height="' . esc_attr( $args['height'] ) . '" ';
		if ( $poster_url ) {
			$html .= 'poster="' . $poster_url . '" ';
		}
		// For autoplay behavior, don't show default controls (we'll use custom controls)
		if ( $args['controls'] && 'autoplay' !== $args['behavior'] ) {
			$html .= 'controls ';
		}
		if ( $autoplay ) {
			$html .= 'autoplay ';
		}
		if ( $muted ) {
			$html .= 'muted ';
		}
		if ( $args['loop'] ) {
			$html .= 'loop ';
		}
		// Add preload attribute for autoplay-on-scroll to ensure poster/metadata loads
		if ( $args['autoplay_on_scroll'] ) {
			$html .= 'preload="metadata" ';
		}
		$html .= 'playsinline '; // Mobile inline playback
		$html .= 'data-behavior="' . esc_attr( $args['behavior'] ) . '" ';
		// Store the desired muted state for JavaScript to apply after autoplay
		$html .= 'data-desired-muted="' . ( $args['muted'] ? 'true' : 'false' ) . '" ';
		// Add data-autoplay-on-scroll attribute for JavaScript to handle
		if ( $args['autoplay_on_scroll'] ) {
			$html .= 'data-autoplay-on-scroll="true" ';
		}
		// Add data attribute to pause immediately if autoplay is false
		if ( 'autoplay' === $args['behavior'] && ! $args['autoplay'] && ! $args['autoplay_on_scroll'] ) {
			$html .= 'data-pause-on-load="true" ';
		}
		// Add data attributes for onclick-popup behavior
		if ( 'onclick-popup' === $args['behavior'] ) {
			// Popup video autoplay
			if ( $args['popup_autoplay'] ) {
				$html .= 'data-popup-autoplay="true" ';
			}
			// Popup video controls
			if ( $args['popup_controls'] ) {
				$html .= 'data-popup-controls="true" ';
			}
		}
		if ( $args['class'] ) {
			$html .= 'class="' . esc_attr( $args['class'] ) . '" ';
		}
		$html .= '>';
		$html .= '<source src="' . esc_url( $cdn_url ) . '" type="video/mp4">';
		$html .= esc_html__( 'Your browser does not support the video tag.', 'epaton' );
		$html .= '</video>';

		return $html;
	}
}


