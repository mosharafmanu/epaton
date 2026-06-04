<?php
/**
 * Site Settings Helpers
 *
 * Easy access to site settings from ACF options. Includes header, footer,
 * and contact info with rendering functions for common components.
 *
 * @package epaton
 */

// Header Options

if ( ! function_exists( 'epaton_get_site_logo' ) ) {
	/**
	 * Get site logo from settings
	 *
	 * @return array|false Returns logo array or false if ACF not available.
	 */
	function epaton_get_site_logo() {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		return get_field( 'site_logo', 'options' );
	}
}



if ( ! function_exists( 'epaton_render_site_logo' ) ) {
	/**
	 * Render site logo with responsive image and link to home
	 *
	 * @param array $args {
	 *     Optional customization.
	 *
	 *     @type string $class         CSS class for the image. Default 'site-logo'.
	 *     @type string $alt           Alt text. Default site name.
	 *     @type string $link_class    CSS class for link. Default 'site-logo-link'.
	 * }
	 * @return void
	 */
	function epaton_render_site_logo( $args = [] ) {
		if ( ! function_exists( 'epaton_render_responsive_picture' ) ) {
			return;
		}

		$logo = epaton_get_site_logo();

		if ( ! $logo ) {
			return;
		}

		$defaults = [
			'class'      => 'site-logo',
			'alt'        => get_bloginfo( 'name' ),
			'link_class' => 'site-logo-link',
		];
		$args = wp_parse_args( $args, $defaults );

		$home_url = home_url( '/' );
		?>
		<a href="<?php echo esc_url( $home_url ); ?>" class="<?php echo esc_attr( $args['link_class'] ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php
			epaton_render_responsive_picture(
				$logo,
				[
					'class' => $args['class'],
					'alt'   => $args['alt'],
					'sizes' => '(max-width: 768px) 100px, 160px',
				]
			);
			?>
		</a>
		<?php
	}
}

if ( ! function_exists( 'epaton_get_header_button' ) ) {
	/**
	 * Get header button link settings
	 *
	 * @return array|false Returns ACF link array or false if ACF not available.
	 */
	function epaton_get_header_button() {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		return get_field( 'header_button', 'options' );
	}
}

if ( ! function_exists( 'epaton_get_header_button_mobile_phone' ) ) {
	/**
	 * Get header button mobile phone number from settings.
	 *
	 * @return string
	 */
	function epaton_get_header_button_mobile_phone() {
		if ( ! function_exists( 'get_field' ) ) {
			return '';
		}

		return (string) get_field( 'header_button_mobile_phone', 'options' );
	}
}

if ( ! function_exists( 'epaton_render_header_button' ) ) {
	/**
	 * Render header button with separate desktop and mobile links.
	 * Desktop uses the configured ACF link, mobile icon uses tel: link.
	 *
	 * @param array $args {
	 *     Optional customization.
	 *
	 *     @type string $class           CSS class for the button. Default 'header-btn'.
	 *     @type string $text_class      CSS class for text wrapper. Default 'header-btn-text'.
	 *     @type string $icon_class      CSS class for icon wrapper. Default 'header-btn-icon'.
	 *     @type bool   $show_text       Show button text. Default true.
	 *     @type bool   $show_icon       Show button icon. Default true.
	 *     @type bool   $echo            Echo or return. Default true.
	 * }
	 * @return string|void
	 */
		function epaton_render_header_button( $args = [] ) {
			$defaults = [
				'class'              => 'site-btn header-btn',
				'desktop_class'      => 'header-btn-desktop',
				'mobile_class'       => 'header-btn-mobile',
				'text_class'         => 'header-btn-text',
				'icon_class'         => 'header-btn-icon',
				'show_text'          => true,
				'show_icon'          => true,
				'show_mobile_button' => true,
				'echo'               => true,
			];
			$args = wp_parse_args( $args, $defaults );

			$button       = epaton_get_header_button();
			$mobile_phone = epaton_get_header_button_mobile_phone();

			if ( ! $button || ! is_array( $button ) ) {
				return;
		}

		// ACF Link field returns: title, url, target
			$text   = $button['title'] ?? '';
			$url    = $button['url'] ?? '';
			$target = $button['target'] ?? '';

			// Need at least the desktop URL to render the desktop button.
			if ( empty( $url ) ) {
				return;
			}

		// Build target attributes
		$target_attr = '';
		if ( '_blank' === $target ) {
			$target_attr = ' target="_blank" rel="noopener noreferrer"';
			}

			$desktop_aria_label = ! empty( $text ) ? $text : __( 'Contact Us', 'epaton' );
			$mobile_phone_clean = preg_replace( '/[^0-9+]/', '', $mobile_phone );
			$mobile_phone_href  = ! empty( $mobile_phone_clean ) ? 'tel:' . $mobile_phone_clean : '';
			$mobile_aria_label  = ! empty( $mobile_phone ) ? sprintf( __( 'Call %s', 'epaton' ), $mobile_phone ) : __( 'Call Us', 'epaton' );

			ob_start();
			?>
			<a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr( trim( $args['class'] . ' ' . $args['desktop_class'] ) ); ?>" aria-label="<?php echo esc_attr( $desktop_aria_label ); ?>"<?php echo $target_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php if ( $args['show_text'] && ! empty( $text ) ) : ?>
					<span class="<?php echo esc_attr( $args['text_class'] ); ?>" aria-hidden="true"><?php echo esc_html( $text ); ?></span>
				<?php endif; ?>
			</a>

			<?php if ( $args['show_mobile_button'] && $args['show_icon'] && ! empty( $mobile_phone_href ) ) : ?>
				<a href="<?php echo esc_attr( $mobile_phone_href ); ?>" class="<?php echo esc_attr( trim( $args['class'] . ' ' . $args['mobile_class'] ) ); ?>" aria-label="<?php echo esc_attr( $mobile_aria_label ); ?>">
					<span class="<?php echo esc_attr( $args['icon_class'] ); ?>" aria-hidden="true">
						<?php get_template_part( 'assets/svgs/header-phone-icon' ); ?>
					</span>
				</a>
			<?php endif; ?>
			<?php
			$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}



// Contact Information

if ( ! function_exists( 'epaton_format_phone_number' ) ) {
	/**
	 * Format phone number - remove or keep country code
	 *
	 * @param string $phone Phone number to format.
	 * @param string $format Format type: 'international' (with +44) or 'local' (without +44). Default 'international'.
	 * @return string Formatted phone number.
	 */
	function epaton_format_phone_number( $phone, $format = 'international' ) {
		if ( empty( $phone ) ) {
			return '';
		}

		// If format is local, remove country code
		if ( 'local' === $format ) {
			// Remove +44 and any spaces/formatting
			$phone = preg_replace( '/^\+44\s*/', '', $phone );
			// Add leading 0 if not present
			if ( ! preg_match( '/^0/', $phone ) ) {
				$phone = '0' . $phone;
			}
		}

		return $phone;
	}
}

if ( ! function_exists( 'epaton_get_office_locations' ) ) {
	/**
	 * Get all office locations from settings
	 *
	 * @return array|false Returns array of office locations or false if ACF not available.
	 */
	function epaton_get_office_locations() {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		return get_field( 'office_locations', 'options' );
	}
}

if ( ! function_exists( 'epaton_get_office_by_name' ) ) {
	/**
	 * Get specific office location by name
	 *
	 * Supports flexible matching:
	 * - Exact match (e.g., 'Head Office' matches 'Head Office')
	 * - Partial match (e.g., 'Manufacturing' matches any office with 'Manufacturing' in the name)
	 *
	 * @param string $office_name Office name to search for (e.g., 'Head Office', 'Manufacturing').
	 * @return array|false Returns office data array or false if not found.
	 */
	function epaton_get_office_by_name( $office_name ) {
		$offices = epaton_get_office_locations();

		if ( ! $offices || ! is_array( $offices ) ) {
			return false;
		}

		// First try exact match
		foreach ( $offices as $office ) {
			if ( isset( $office['office_name'] ) && $office['office_name'] === $office_name ) {
				return $office;
			}
		}

		// If no exact match, try partial match (case-insensitive)
		$search_lower = strtolower( $office_name );
		foreach ( $offices as $office ) {
			if ( isset( $office['office_name'] ) ) {
				$office_name_lower = strtolower( $office['office_name'] );
				// Check if search term is contained in office name
				if ( strpos( $office_name_lower, $search_lower ) !== false ) {
					return $office;
				}
			}
		}

		return false;
	}
}

if ( ! function_exists( 'epaton_get_office_flag' ) ) {
	/**
	 * Get country flag image for a specific office
	 *
	 * @param string $office_name Office name (e.g., 'Head Office').
	 * @return array|false Returns flag image array or false if not found.
	 */
	function epaton_get_office_flag( $office_name ) {
		$office = epaton_get_office_by_name( $office_name );

		if ( ! $office || ! isset( $office['office_flag'] ) ) {
			return false;
		}

		return $office['office_flag'];
	}
}

if ( ! function_exists( 'epaton_get_office_full_name' ) ) {
	/**
	 * Get full office name for a specific office
	 *
	 * @param string $office_name Office name (e.g., 'Head Office').
	 * @return string|false Returns full office name or office name if not set.
	 */
	function epaton_get_office_full_name( $office_name ) {
		$office = epaton_get_office_by_name( $office_name );

		if ( ! $office ) {
			return false;
		}

		// Return full name if set, otherwise return office name
		if ( ! empty( $office['office_full_name'] ) ) {
			return $office['office_full_name'];
		}

		return $office['office_name'] ?? false;
	}
}

if ( ! function_exists( 'epaton_get_office_address' ) ) {
	/**
	 * Get address for a specific office
	 *
	 * @param string $office_name Office name (e.g., 'Head Office').
	 * @return string|false Returns address string or false if not found.
	 */
	function epaton_get_office_address( $office_name ) {
		$office = epaton_get_office_by_name( $office_name );

		if ( ! $office || ! isset( $office['office_address'] ) ) {
			return false;
		}

		return $office['office_address'];
	}
}

if ( ! function_exists( 'epaton_get_office_phone' ) ) {
	/**
	 * Get main phone number for a specific office
	 *
	 * @param string $office_name Office name (e.g., 'Head Office').
	 * @return string|false Returns phone number or false if not found.
	 */
	function epaton_get_office_phone( $office_name ) {
		$office = epaton_get_office_by_name( $office_name );

		if ( ! $office || ! isset( $office['office_phone'] ) ) {
			return false;
		}

		return $office['office_phone'];
	}
}

if ( ! function_exists( 'epaton_get_office_email' ) ) {
	/**
	 * Get main email address for a specific office
	 *
	 * @param string $office_name Office name (e.g., 'Head Office').
	 * @return string|false Returns email address or false if not found.
	 */
	function epaton_get_office_email( $office_name ) {
		$office = epaton_get_office_by_name( $office_name );

		if ( ! $office || ! isset( $office['office_email'] ) ) {
			return false;
		}

		return $office['office_email'];
	}
}

if ( ! function_exists( 'epaton_get_office_additional_phones' ) ) {
	/**
	 * Get additional phone numbers for a specific office
	 *
	 * @param string $office_name Office name (e.g., 'Head Office').
	 * @return array|false Returns array of additional phone numbers or false if not found.
	 */
	function epaton_get_office_additional_phones( $office_name ) {
		$office = epaton_get_office_by_name( $office_name );

		if ( ! $office || ! isset( $office['office_additional_phones'] ) ) {
			return false;
		}

		return $office['office_additional_phones'];
	}
}

if ( ! function_exists( 'epaton_get_office_all_phones' ) ) {
	/**
	 * Get all phone numbers for a specific office (main + additional)
	 *
	 * @param string $office_name Office name (e.g., 'Head Office').
	 * @return array|false Returns array of all phone numbers or false if not found.
	 */
	function epaton_get_office_all_phones( $office_name ) {
		$office = epaton_get_office_by_name( $office_name );

		if ( ! $office ) {
			return false;
		}

		$phones = [];

		// Add main phone
		if ( ! empty( $office['office_phone'] ) ) {
			$phones[] = $office['office_phone'];
		}

		// Add additional phones
		if ( ! empty( $office['office_additional_phones'] ) && is_array( $office['office_additional_phones'] ) ) {
			foreach ( $office['office_additional_phones'] as $additional_phone ) {
				if ( ! empty( $additional_phone['phone_number'] ) ) {
					$phones[] = $additional_phone['phone_number'];
				}
			}
		}

		return ! empty( $phones ) ? $phones : false;
	}
}

if ( ! function_exists( 'epaton_render_office_phone' ) ) {
	/**
	 * Render clickable phone link (tel:) for a specific office
	 *
	 * @param array $args {
	 *     Required and optional customization.
	 *
	 *     @type string $office_name  Office name (required). E.g., 'Head Office', 'Manufacturing'.
	 *     @type string $class        CSS class for link. Default 'office-phone-link'.
	 *     @type string $icon_class   CSS class for icon. Default 'phone-icon'.
	 *     @type string $text_class   CSS class for text. Default 'phone-text'.
	 *     @type string $label_class  CSS class for label. Default 'phone-label'.
	 *     @type string $label        Label text (e.g., 'Tel'). Default empty.
	 *     @type bool   $show_icon    Show icon. Default false.
	 *     @type bool   $show_text    Show phone number text. Default true.
	 *     @type bool   $show_label   Show phone label. Default false.
	 *     @type bool   $echo         Echo or return. Default true.
	 * }
	 * @return string|void
	 */
	function epaton_render_office_phone( $args = [] ) {
		$defaults = [
			'office_name' => '',
			'class'       => 'office-phone-link',
			'icon_class'  => 'phone-icon',
			'text_class'  => 'phone-text',
			'label_class' => 'phone-label',
			'label'       => '',
			'show_icon'   => false,
			'show_text'   => true,
			'show_label'  => false,
			'echo'        => true,
		];
		$args = wp_parse_args( $args, $defaults );

		if ( empty( $args['office_name'] ) ) {
			return;
		}

		$phone = epaton_get_office_phone( $args['office_name'] );

		if ( ! $phone ) {
			return;
		}

		// Clean phone number for tel: link
		$phone_clean = preg_replace( '/[^0-9+]/', '', $phone );

		ob_start();
		?>
		<a href="tel:<?php echo esc_attr( $phone_clean ); ?>" class="<?php echo esc_attr( $args['class'] ); ?>">
			<?php if ( $args['show_icon'] ) : ?>
				<span class="<?php echo esc_attr( $args['icon_class'] ); ?>">
					<?php
					$icon_path = get_template_directory() . '/assets/svgs/phone-icon.php';
					if ( file_exists( $icon_path ) ) {
						include $icon_path;
					}
					?>
				</span>
			<?php endif; ?>
			<?php if ( $args['show_label'] && ! empty( $args['label'] ) ) : ?>
				<span class="<?php echo esc_attr( $args['label_class'] ); ?>"><?php echo esc_html( $args['label'] ); ?>:</span>
			<?php endif; ?>
			<?php if ( $args['show_text'] ) : ?>
				<span class="<?php echo esc_attr( $args['text_class'] ); ?>"><?php echo esc_html( $phone ); ?></span>
			<?php endif; ?>
		</a>
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'epaton_render_office_phones' ) ) {
	/**
	 * Render all phone numbers for a specific office (main + additional)
	 *
	 * @param array $args {
	 *     Required and optional customization.
	 *
	 *     @type string $office_name      Office name (required). E.g., 'Head Office', 'Manufacturing'.
	 *     @type string $container_class  CSS class for container. Default 'office-phones'.
	 *     @type string $list_class       CSS class for ul element. Default 'office-phones-list'.
	 *     @type string $item_class       CSS class for li elements. Default 'office-phone-item'.
	 *     @type string $link_class       CSS class for anchor elements. Default 'office-phone-link'.
	 *     @type string $text_class       CSS class for text. Default 'phone-text'.
	 *     @type bool   $echo             Echo or return. Default true.
	 * }
	 * @return string|void
	 */
	function epaton_render_office_phones( $args = [] ) {
		$defaults = [
			'office_name'     => '',
			'container_class' => 'office-phones',
			'list_class'      => 'office-phones-list',
			'item_class'      => 'office-phone-item',
			'link_class'      => 'office-phone-link',
			'text_class'      => 'phone-text',
			'echo'            => true,
		];
		$args = wp_parse_args( $args, $defaults );

		if ( empty( $args['office_name'] ) ) {
			return;
		}

		$phones = epaton_get_office_all_phones( $args['office_name'] );

		if ( ! $phones || ! is_array( $phones ) ) {
			return;
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( $args['container_class'] ); ?>">
			<ul class="<?php echo esc_attr( $args['list_class'] ); ?>">
				<?php foreach ( $phones as $phone ) : ?>
					<?php
					if ( empty( $phone ) ) {
						continue;
					}

					$phone_clean = preg_replace( '/[^0-9+]/', '', $phone );
					?>
					<li class="<?php echo esc_attr( $args['item_class'] ); ?>">
						<a href="tel:<?php echo esc_attr( $phone_clean ); ?>" class="<?php echo esc_attr( $args['link_class'] ); ?>">
							<span class="<?php echo esc_attr( $args['text_class'] ); ?>"><?php echo esc_html( $phone ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'epaton_render_office_email' ) ) {
	/**
	 * Render clickable email link (mailto:) for a specific office
	 *
	 * @param array $args {
	 *     Required and optional customization.
	 *
	 *     @type string $office_name  Office name (required). E.g., 'Head Office', 'Manufacturing'.
	 *     @type string $class        CSS class for link. Default 'office-email-link'.
	 *     @type string $icon_class   CSS class for icon. Default 'email-icon'.
	 *     @type string $text_class   CSS class for text. Default 'email-text'.
	 *     @type string $label_class  CSS class for label. Default 'email-label'.
	 *     @type string $label        Label text (e.g., 'Email'). Default empty.
	 *     @type bool   $show_icon    Show icon. Default false.
	 *     @type bool   $show_text    Show email text. Default true.
	 *     @type bool   $show_label   Show email label. Default false.
	 *     @type bool   $echo         Echo or return. Default true.
	 * }
	 * @return string|void
	 */
	function epaton_render_office_email( $args = [] ) {
		$defaults = [
			'office_name' => '',
			'class'       => 'office-email-link',
			'icon_class'  => 'email-icon',
			'text_class'  => 'email-text',
			'label_class' => 'email-label',
			'label'       => '',
			'show_icon'   => false,
			'show_text'   => true,
			'show_label'  => false,
			'echo'        => true,
		];
		$args = wp_parse_args( $args, $defaults );

		if ( empty( $args['office_name'] ) ) {
			return;
		}

		$email = epaton_get_office_email( $args['office_name'] );

		if ( ! $email ) {
			return;
		}

		ob_start();
		?>
		<a href="mailto:<?php echo esc_attr( $email ); ?>" class="<?php echo esc_attr( $args['class'] ); ?>">
			<?php if ( $args['show_icon'] ) : ?>
				<span class="<?php echo esc_attr( $args['icon_class'] ); ?>">
					<?php
					$icon_path = get_template_directory() . '/assets/svgs/email-icon.php';
					if ( file_exists( $icon_path ) ) {
						include $icon_path;
					}
					?>
				</span>
			<?php endif; ?>
			<?php if ( $args['show_label'] && ! empty( $args['label'] ) ) : ?>
				<span class="<?php echo esc_attr( $args['label_class'] ); ?>"><?php echo esc_html( $args['label'] ); ?>:</span>
			<?php endif; ?>
			<?php if ( $args['show_text'] ) : ?>
				<span class="<?php echo esc_attr( $args['text_class'] ); ?>"><?php echo esc_html( $email ); ?></span>
			<?php endif; ?>
		</a>
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'epaton_render_office_flag' ) ) {
	/**
	 * Render office country flag image
	 *
	 * @param array $args {
	 *     Required and optional customization.
	 *
	 *     @type string $office_name  Office name (required). E.g., 'Head Office', 'Manufacturing'.
	 *     @type string $class        CSS class for image. Default 'office-flag'.
	 *     @type string $size         Image size. Default 'thumbnail'.
	 *     @type bool   $echo         Echo or return. Default true.
	 * }
	 * @return string|void
	 */
	function epaton_render_office_flag( $args = [] ) {
		$defaults = [
			'office_name' => '',
			'class'       => 'office-flag',
			'size'        => 'thumbnail',
			'echo'        => true,
		];
		$args = wp_parse_args( $args, $defaults );

		if ( empty( $args['office_name'] ) ) {
			return;
		}

		$flag = epaton_get_office_flag( $args['office_name'] );

		if ( ! $flag || ! is_array( $flag ) ) {
			return;
		}

		$flag_url = $flag['url'] ?? '';
		$flag_alt = $flag['alt'] ?? $args['office_name'] . ' Flag';

		if ( ! $flag_url ) {
			return;
		}

		ob_start();
		?>
		<img src="<?php echo esc_url( $flag_url ); ?>" alt="<?php echo esc_attr( $flag_alt ); ?>" class="<?php echo esc_attr( $args['class'] ); ?>" />
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'epaton_render_office_address' ) ) {
	/**
	 * Render office address
	 *
	 * @param array $args {
	 *     Required and optional customization.
	 *
	 *     @type string $office_name  Office name (required). E.g., 'Head Office', 'Manufacturing'.
	 *     @type string $class        CSS class for container. Default 'office-address'.
	 *     @type string $icon_class   CSS class for icon. Default 'address-icon'.
	 *     @type bool   $show_icon    Show icon. Default false.
	 *     @type string $icon_variant Icon variant: 'light' or 'bold'. Default 'light'.
	 *     @type bool   $echo         Echo or return. Default true.
	 *     @type bool   $nl2br        Convert line breaks to <br> tags. Default true.
	 * }
	 * @return string|void
	 */
	function epaton_render_office_address( $args = [] ) {
		$defaults = [
			'office_name'  => '',
			'class'        => 'office-address',
			'icon_class'   => 'address-icon',
			'show_icon'    => false,
			'icon_variant' => 'light',
			'echo'         => true,
			'nl2br'        => true,
		];
		$args = wp_parse_args( $args, $defaults );

		if ( empty( $args['office_name'] ) ) {
			return;
		}

		$address = epaton_get_office_address( $args['office_name'] );

		if ( ! $address ) {
			return;
		}

		// Convert line breaks to <br> tags if needed
		if ( $args['nl2br'] ) {
			$address = nl2br( $address );
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( $args['class'] ); ?>">
			<?php if ( $args['show_icon'] ) : ?>
				<span class="<?php echo esc_attr( $args['icon_class'] ); ?>">
					<?php
					$icon_file = 'location-icon-' . sanitize_key( $args['icon_variant'] ) . '.php';
					$icon_path = get_template_directory() . '/assets/svgs/' . $icon_file;
					if ( file_exists( $icon_path ) ) {
						include $icon_path;
					}
					?>
				</span>
			<?php endif; ?>
			<address><?php echo wp_kses_post( $address ); ?></address>
		</div>
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'epaton_render_office_contact_info' ) ) {
	/**
	 * Render complete contact information for a specific office
	 *
	 * @param array $args {
	 *     Required and optional customization.
	 *
	 *     @type string $office_name       Office name (required). E.g., 'Head Office', 'Manufacturing'.
	 *     @type string $container_class   CSS class for container. Default 'office-contact-info'.
	 *     @type string $title_class       CSS class for office title. Default 'office-title'.
	 *     @type bool   $show_title        Show office name as title. Default true.
	 *     @type bool   $use_full_name     Use full office name for title. Default false.
	 *     @type bool   $show_flag         Show country flag. Default false.
	 *     @type bool   $show_phone        Show phone number. Default true.
	 *     @type bool   $show_email        Show email address. Default true.
	 *     @type bool   $show_address      Show address. Default true.
	 *     @type bool   $show_all_phones   Show all phones (main + additional). Default false.
	 *     @type string $phone_label       Label for phone (e.g., 'Tel'). Default empty.
	 *     @type bool   $show_phone_label  Show phone label. Default false.
	 *     @type string $email_label       Label for email (e.g., 'Email'). Default empty.
	 *     @type bool   $show_email_label  Show email label. Default false.
	 *     @type bool   $address_nl2br     Convert line breaks to <br> in address. Default false.
	 *     @type bool   $echo              Echo or return. Default true.
	 * }
	 * @return string|void
	 */
	function epaton_render_office_contact_info( $args = [] ) {
		$defaults = [
			'office_name'       => '',
			'container_class'   => 'office-contact-info',
			'title_class'       => 'office-title',
			'show_title'        => true,
			'use_full_name'     => false,
			'show_flag'         => false,
			'show_phone'        => true,
			'show_email'        => true,
			'show_address'      => true,
			'show_all_phones'   => false,
			'phone_label'       => '',
			'show_phone_label'  => false,
			'email_label'       => '',
			'show_email_label'  => false,
			'address_nl2br'     => false,
			'echo'              => true,
		];
		$args = wp_parse_args( $args, $defaults );

		if ( empty( $args['office_name'] ) ) {
			return;
		}

		$office = epaton_get_office_by_name( $args['office_name'] );

		if ( ! $office ) {
			return;
		}

		// Get title text
		$title_text = $args['office_name'];
		if ( $args['use_full_name'] ) {
			$full_name = epaton_get_office_full_name( $args['office_name'] );
			if ( $full_name ) {
				$title_text = $full_name;
			}
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( $args['container_class'] ); ?>">
			<?php if ( $args['show_flag'] ) : ?>
				<?php
				epaton_render_office_flag(
					[
						'office_name' => $args['office_name'],
						'echo'        => true,
					]
				);
				?>
			<?php endif; ?>

			<?php if ( $args['show_title'] ) : ?>
				<p class="<?php echo esc_attr( $args['title_class'] ); ?>"><?php echo esc_html( $title_text ); ?></p>
			<?php endif; ?>

			<?php if ( $args['show_address'] ) : ?>
				<?php
				epaton_render_office_address(
					[
						'office_name' => $args['office_name'],
						'nl2br'       => $args['address_nl2br'],
						'echo'        => true,
					]
				);
				?>
			<?php endif; ?>

			<?php if ( $args['show_phone'] ) : ?>
				<?php if ( $args['show_all_phones'] ) : ?>
					<?php
					epaton_render_office_phones(
						[
							'office_name' => $args['office_name'],
							'echo'        => true,
						]
					);
					?>
				<?php else : ?>
					<?php
					epaton_render_office_phone(
						[
							'office_name' => $args['office_name'],
							'show_label'  => $args['show_phone_label'],
							'label'       => $args['phone_label'],
							'echo'        => true,
						]
					);
					?>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( $args['show_email'] ) : ?>
				<?php
				epaton_render_office_email(
					[
						'office_name' => $args['office_name'],
						'show_label'  => $args['show_email_label'],
						'label'       => $args['email_label'],
						'echo'        => true,
					]
				);
				?>
			<?php endif; ?>
		</div>
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'epaton_render_all_offices' ) ) {
	/**
	 * Render contact information for all offices
	 *
	 * @param array $args {
	 *     Optional customization.
	 *
	 *     @type string $container_class   CSS class for main container. Default 'all-offices-contact'.
	 *     @type string $office_class      CSS class for each office. Default 'office-contact-info'.
	 *     @type bool   $show_title        Show office names as titles. Default true.
	 *     @type bool   $use_full_name     Use full office name for title. Default false.
	 *     @type bool   $show_flag         Show country flags. Default false.
	 *     @type bool   $show_phone        Show phone numbers. Default true.
	 *     @type bool   $show_email        Show email addresses. Default true.
	 *     @type bool   $show_address      Show addresses. Default true.
	 *     @type bool   $show_all_phones   Show all phones (main + additional). Default false.
	 *     @type bool   $echo              Echo or return. Default true.
	 * }
	 * @return string|void
	 */
	function epaton_render_all_offices( $args = [] ) {
		$defaults = [
			'container_class' => 'all-offices-contact',
			'office_class'    => 'office-contact-info',
			'show_title'      => true,
			'use_full_name'   => false,
			'show_flag'       => false,
			'show_phone'      => true,
			'show_email'      => true,
			'show_address'    => true,
			'show_all_phones' => false,
			'echo'            => true,
		];
		$args = wp_parse_args( $args, $defaults );

		$offices = epaton_get_office_locations();

		if ( ! $offices || ! is_array( $offices ) ) {
			return;
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( $args['container_class'] ); ?>">
			<?php foreach ( $offices as $office ) : ?>
				<?php
				$office_name = $office['office_name'] ?? '';
				if ( ! $office_name ) {
					continue;
				}

				epaton_render_office_contact_info(
					[
						'office_name'     => $office_name,
						'container_class' => $args['office_class'],
						'show_title'      => $args['show_title'],
						'use_full_name'   => $args['use_full_name'],
						'show_flag'       => $args['show_flag'],
						'show_phone'      => $args['show_phone'],
						'show_email'      => $args['show_email'],
						'show_address'    => $args['show_address'],
						'show_all_phones' => $args['show_all_phones'],
						'echo'            => true,
					]
				);
				?>
			<?php endforeach; ?>
		</div>
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

// Footer Options

if ( ! function_exists( 'epaton_get_footer_logo' ) ) {
	/**
	 * Get footer logo from settings
	 *
	 * @return array|false Returns logo array or false if ACF not available. Falls back to site logo if not set.
	 */
	function epaton_get_footer_logo() {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		$footer_logo = get_field( 'footer_logo', 'options' );

		// If no footer logo is set, use site logo as fallback
		if ( ! $footer_logo ) {
			$footer_logo = get_field( 'site_logo', 'options' );
		}

		return $footer_logo;
	}
}

if ( ! function_exists( 'epaton_render_footer_logo' ) ) {
	/**
	 * Render footer logo with responsive image and link to home
	 *
	 * @param array $args {
	 *     Optional customization.
	 *
	 *     @type string $class         CSS class for the image. Default 'footer-logo'.
	 *     @type string $alt           Alt text. Default site name.
	 *     @type string $link_class    CSS class for link. Default 'footer-logo-link'.
	 * }
	 * @return void
	 */
	function epaton_render_footer_logo( $args = [] ) {
		if ( ! function_exists( 'epaton_render_responsive_picture' ) ) {
			return;
		}

		$logo = epaton_get_footer_logo();

		if ( ! $logo ) {
			return;
		}

		$defaults = [
			'class'      => 'footer-logo',
			'alt'        => get_bloginfo( 'name' ),
			'link_class' => 'footer-logo-link',
		];
		$args = wp_parse_args( $args, $defaults );

		$home_url = home_url( '/' );
		?>
		<a href="<?php echo esc_url( $home_url ); ?>" class="<?php echo esc_attr( $args['link_class'] ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php
			epaton_render_responsive_picture(
				$logo,
				[
					'class' => $args['class'],
					'alt'   => $args['alt'],
					'sizes' => '(max-width: 768px) 100px, 160px',
				]
			);
			?>
		</a>
		<?php
	}
}

// Social Media Options

if ( ! function_exists( 'epaton_get_social_medias' ) ) {
	/**
	 * Get social media links from settings
	 *
	 * @return array|false
	 */
	function epaton_get_social_medias() {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		return get_field( 'social_medias', 'options' );
	}
}

if ( ! function_exists( 'epaton_get_footer_copyright' ) ) {
	/**
	 * Get footer copyright text from settings
	 *
	 * @return string|false
	 */
	function epaton_get_footer_copyright() {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		return get_field( 'footer_copyright', 'options' );
	}
}

if ( ! function_exists( 'epaton_render_footer_menu' ) ) {
	/**
	 * Render footer menu with title
	 *
	 * @param array $args {
	 *     Required and optional parameters.
	 *
	 *     @type string $location        Menu location (required). E.g., 'footerMenu1', 'footerMenu2', 'footerMenu3'.
	 *     @type string $container_class CSS class for container. Default 'footer-menu-column'.
	 *     @type string $title_class     CSS class for title. Default 'footer-menu-title'.
	 *     @type string $menu_class      CSS class for menu ul. Default 'footer-menu-list'.
	 *     @type bool   $show_title      Whether to show menu title. Default true.
	 *     @type bool   $echo            Whether to echo or return. Default true.
	 * }
	 * @return string|void
	 */
	function epaton_render_footer_menu( $args = [] ) {
		$defaults = [
			'location'        => '',
			'container_class' => 'footer-menu-column',
			'title_class'     => 'footer-menu-title',
			'menu_class'      => 'footer-menu-list',
			'show_title'      => true,
			'echo'            => true,
		];
		$args = wp_parse_args( $args, $defaults );

		// Check if location is provided
		if ( empty( $args['location'] ) ) {
			return;
		}

		// Check if menu location has a menu assigned
		if ( ! has_nav_menu( $args['location'] ) ) {
			return;
		}

		// Get menu name for title
		$locations = get_nav_menu_locations();
		$menu_id   = $locations[ $args['location'] ] ?? 0;
		$menu_obj  = wp_get_nav_menu_object( $menu_id );
		$menu_name = $menu_obj->name ?? '';

		ob_start();
		?>
		<div class="<?php echo esc_attr( $args['container_class'] ); ?>">
			<?php if ( $args['show_title'] && ! empty( $menu_name ) ) : ?>
				<p class="<?php echo esc_attr( $args['title_class'] ); ?>"><?php echo esc_html( $menu_name ); ?></p>
			<?php endif; ?>
			<?php
			wp_nav_menu(
				[
					'theme_location' => $args['location'],
					'container'      => false,
					'menu_class'     => $args['menu_class'],
					'depth'          => 1,
					'fallback_cb'    => false,
				]
			);
			?>
		</div>
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'epaton_render_social_medias' ) ) {
	/**
	 * Render social media links
	 *
	 * Supports both image files and inline SVG rendering for better styling control.
	 *
	 * @param array $args {
	 *     Optional customization.
	 *
	 *     @type string $list_class CSS class for ul element. Default 'social-media-list'.
	 *     @type string $item_class CSS class for li elements. Default 'social-media-item'.
	 *     @type string $link_class CSS class for anchor elements. Default 'social-media-link'.
	 *     @type string $icon_class CSS class for icon elements. Default 'social-media-icon'.
	 *     @type bool   $echo       Echo or return. Default true.
	 * }
	 * @return string|void
	 */
	function epaton_render_social_medias( $args = [] ) {
		$defaults = [
			'list_class' => 'social-media-list',
			'item_class' => 'social-media-item',
			'link_class' => 'social-media-link',
			'icon_class' => 'social-media-icon',
			'echo'       => true,
		];
		$args = wp_parse_args( $args, $defaults );

		$social_medias = epaton_get_social_medias();

		if ( ! $social_medias || ! is_array( $social_medias ) ) {
			return;
		}

		ob_start();
		?>
		<ul class="<?php echo esc_attr( $args['list_class'] ); ?>">
			<?php foreach ( $social_medias as $social ) : ?>
				<?php
				$icon = isset( $social['social_icon'] ) ? $social['social_icon'] : null;
				$link = isset( $social['social_link'] ) ? $social['social_link'] : '';

				if ( ! $icon || ! $link ) {
					continue;
				}

				$icon_url  = isset( $icon['url'] ) ? $icon['url'] : '';
				$icon_alt  = isset( $icon['alt'] ) ? $icon['alt'] : '';
				$icon_id   = isset( $icon['ID'] ) ? $icon['ID'] : 0;
				$icon_mime = $icon_id ? get_post_mime_type( $icon_id ) : '';

				// Check if SVG for inline rendering
				$is_svg = ( 'image/svg+xml' === $icon_mime ) || ( pathinfo( $icon_url, PATHINFO_EXTENSION ) === 'svg' );

				// Create descriptive aria-label
				// Priority: 1) Icon alt text, 2) URL detection, 3) Filename detection, 4) Generic fallback
				$aria_label = '';

				if ( ! empty( $icon_alt ) ) {
					// Use icon alt text if available
					$aria_label = $icon_alt;
				} else {
					// Try to detect platform from URL
					$url_lower = strtolower( $link );
					$filename_lower = strtolower( basename( $icon_url ) );

					if ( strpos( $url_lower, 'facebook' ) !== false || strpos( $filename_lower, 'facebook' ) !== false ) {
						$aria_label = __( 'Facebook', 'epaton' );
					} elseif ( strpos( $url_lower, 'twitter' ) !== false || strpos( $url_lower, 'x.com' ) !== false || strpos( $filename_lower, 'twitter' ) !== false ) {
						$aria_label = __( 'Twitter', 'epaton' );
					} elseif ( strpos( $url_lower, 'linkedin' ) !== false || strpos( $url_lower, 'linkdin' ) !== false || strpos( $filename_lower, 'linkedin' ) !== false ) {
						$aria_label = __( 'LinkedIn', 'epaton' );
					} elseif ( strpos( $url_lower, 'instagram' ) !== false || strpos( $filename_lower, 'instagram' ) !== false ) {
						$aria_label = __( 'Instagram', 'epaton' );
					} elseif ( strpos( $url_lower, 'youtube' ) !== false || strpos( $filename_lower, 'youtube' ) !== false ) {
						$aria_label = __( 'YouTube', 'epaton' );
					} else {
						$aria_label = __( 'Social Media', 'epaton' );
					}
				}
				?>

				<li class="<?php echo esc_attr( $args['item_class'] ); ?>">
					<a href="<?php echo esc_url( $link ); ?>" class="<?php echo esc_attr( $args['link_class'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $aria_label ); ?>">
						<?php if ( $is_svg ) : ?>
							<?php
							// Render SVG inline for styling control
							$svg_path = get_attached_file( $icon_id );
							if ( $svg_path && file_exists( $svg_path ) ) {
								$svg_content = file_get_contents( $svg_path );
								$svg_content = str_replace( '<svg', '<svg class="' . esc_attr( $args['icon_class'] ) . ' social-media-svg"', $svg_content );
								$svg_content = preg_replace( '/<\?xml.*?\?>/', '', $svg_content );
								echo $svg_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							} else {
								// Fallback to img if file not found
								?>
								<img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $icon_alt ); ?>" class="<?php echo esc_attr( $args['icon_class'] ); ?>" />
								<?php
							}
							?>
						<?php else : ?>
							<img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $icon_alt ); ?>" class="<?php echo esc_attr( $args['icon_class'] ); ?>" />
						<?php endif; ?>
					</a>
				</li>

			<?php endforeach; ?>
		</ul>
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'epaton_render_footer_copyright' ) ) {
	/**
	 * Render footer copyright text
	 *
	 * @param array $args {
	 *     Optional customization.
	 *
	 *     @type string $class CSS class for container. Default 'footer-copyright-text'.
	 *     @type bool   $echo  Echo or return. Default true.
	 * }
	 * @return string|void
	 */
	function epaton_render_footer_copyright( $args = [] ) {
		$defaults = [
			'class' => 'footer-copyright-text',
			'echo'  => true,
		];
		$args = wp_parse_args( $args, $defaults );

		$copyright = epaton_get_footer_copyright();

		if ( ! $copyright ) {
			return;
		}

		// Replace {year} placeholder with current year
		$copyright = str_replace( '{year}', gmdate( 'Y' ), $copyright );

		ob_start();
		?>

		<div class="<?php echo esc_attr( $args['class'] ); ?>">
			<p><?php echo esc_html( $copyright ); ?></p>
		</div>

		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}
