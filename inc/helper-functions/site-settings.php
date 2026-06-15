<?php
/**
 * Site settings helpers.
 *
 * @package epaton
 */

if ( ! function_exists( 'epaton_get_option_field' ) ) {
	/**
	 * Read an ACF options field with a fallback value.
	 *
	 * @param string $field Field name.
	 * @param mixed  $fallback Fallback value.
	 * @return mixed
	 */
	function epaton_get_option_field( $field, $fallback = '' ) {
		if ( ! function_exists( 'get_field' ) ) {
			return $fallback;
		}

		$value = get_field( $field, 'options' );

		return ( null !== $value && false !== $value && '' !== $value ) ? $value : $fallback;
	}
}

if ( ! function_exists( 'epaton_get_site_logo' ) ) {
	/**
	 * Get the primary site logo.
	 *
	 * @return array|false
	 */
	function epaton_get_site_logo() {
		return epaton_get_option_field( 'site_logo', false );
	}
}

if ( ! function_exists( 'epaton_render_site_logo' ) ) {
	/**
	 * Render the Epaton logo.
	 *
	 * @param array $args Render args.
	 * @return void
	 */
	function epaton_render_site_logo( $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'class'      => 'site-logo',
				'link_class' => 'site-logo-link',
			]
		);

		$logo = epaton_get_site_logo();
		?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="<?php echo esc_attr( $args['link_class'] ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php if ( $logo && function_exists( 'epaton_render_icon' ) ) : ?>
				<?php
				epaton_render_icon(
					$logo,
					[
						'class' => $args['class'],
						'alt'   => get_bloginfo( 'name' ),
					]
				);
				?>
			<?php else : ?>
				<span class="<?php echo esc_attr( $args['class'] ); ?> site-logo-fallback">epaton</span>
			<?php endif; ?>
		</a>
		<?php
	}
}

if ( ! function_exists( 'epaton_get_header_button' ) ) {
	/**
	 * Get the header CTA link.
	 *
	 * @return array|false
	 */
	function epaton_get_header_button() {
		return epaton_get_option_field(
			'header_button',
			[
				'title'  => 'Contact Epaton',
				'url'    => home_url( '/contact/' ),
				'target' => '',
			]
		);
	}
}

if ( ! function_exists( 'epaton_render_header_button' ) ) {
	/**
	 * Render the header CTA button.
	 *
	 * @param array $args Render args.
	 * @return string|void
	 */
	function epaton_render_header_button( $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'class'              => 'btn-primary site-btn header-btn',
				'desktop_class'      => 'header-btn-desktop',
				'mobile_class'       => 'header-btn-mobile',
				'text_class'         => 'header-btn-text',
				'show_text'          => true,
				'show_mobile_button' => false,
				'echo'               => true,
			]
		);

		$button = epaton_get_header_button();

		if ( empty( $button['url'] ) ) {
			return;
		}

		$text   = $button['title'] ?? __( 'Contact Epaton', 'epaton' );
		$target = $button['target'] ?? '';

		ob_start();
		?>
		<a href="<?php echo esc_url( $button['url'] ); ?>" class="<?php echo esc_attr( trim( $args['class'] . ' ' . $args['desktop_class'] ) ); ?>"<?php echo '_blank' === $target ? ' target="_blank" rel="noopener noreferrer"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php if ( $args['show_text'] ) : ?>
				<span class="<?php echo esc_attr( $args['text_class'] ); ?>"><?php echo esc_html( $text ); ?></span>
			<?php endif; ?>
		</a>
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		return $output;
	}
}

if ( ! function_exists( 'epaton_get_footer_company_text' ) ) {
	/**
	 * Get footer company registration text.
	 *
	 * @return string
	 */
	function epaton_get_footer_company_text() {
		return epaton_get_option_field( 'footer_company_text', "Epaton Limited is a company\nregistered in England and Wales.\nCompany Number 08952280.\nVAT Number 185125409." );
	}
}

if ( ! function_exists( 'epaton_get_footer_email' ) ) {
	/**
	 * Get footer email address.
	 *
	 * @return string
	 */
	function epaton_get_footer_email() {
		return epaton_get_option_field( 'footer_email', 'sales@epaton.co.uk' );
	}
}

if ( ! function_exists( 'epaton_get_footer_phone' ) ) {
	/**
	 * Get footer phone number.
	 *
	 * @return string
	 */
	function epaton_get_footer_phone() {
		return epaton_get_option_field( 'footer_phone', '+44 (0)3333 111 001' );
	}
}

if ( ! function_exists( 'epaton_get_social_links' ) ) {
	/**
	 * Get site social links.
	 *
	 * @return array
	 */
	function epaton_get_social_links() {
		$social_links = epaton_get_option_field( 'social_links', [] );

		return is_array( $social_links ) ? $social_links : [];
	}
}

if ( ! function_exists( 'epaton_get_footer_offices' ) ) {
	/**
	 * Get footer office rows.
	 *
	 * @return array
	 */
	function epaton_get_footer_offices() {
		$offices = epaton_get_option_field( 'footer_offices', [] );

		if ( is_array( $offices ) && $offices ) {
			return $offices;
		}

		return [
			[ 'city' => 'London', 'address' => '2-7 Clerkenwell Green, London, EC1R 0DE' ],
			[ 'city' => 'Leeds', 'address' => 'Fountain House 4, South Parade, LS1 5QX' ],
			[ 'city' => 'Edinburgh', 'address' => 'Third Floor, 3 Hill Street, EH2 3JP' ],
			[ 'city' => 'Birmingham', 'address' => 'Office 1, Izabella House, 24-26 Regent Place, B1 3NJ' ],
			[ 'city' => 'Manchester', 'address' => 'First Floor, Swan Buildings, 20 Swan Street, M4 5JW' ],
		];
	}
}

if ( ! function_exists( 'epaton_get_footer_copyright' ) ) {
	/**
	 * Get footer copyright text.
	 *
	 * @return string
	 */
	function epaton_get_footer_copyright() {
		$copyright = epaton_get_option_field( 'footer_copyright', '©Epaton - {year}. All rights reserved' );

		return str_replace( '{year}', gmdate( 'Y' ), $copyright );
	}
}

if ( ! function_exists( 'epaton_get_footer_credit_text' ) ) {
	/**
	 * Get footer credit text.
	 *
	 * @return string
	 */
	function epaton_get_footer_credit_text() {
		return epaton_get_option_field( 'footer_credit_text', 'Website by' );
	}
}

if ( ! function_exists( 'epaton_get_global_contact_cta' ) ) {
	/**
	 * Get the global Contact CTA content from Site Settings.
	 *
	 * Returns an associative array with title, body, button_style, and button keys.
	 * All values fall back to sensible defaults when nothing is configured.
	 *
	 * @return array{title: string, body: string, button_style: string, button: array|false}
	 */
	function epaton_get_global_contact_cta() {
		return [
			'title'        => epaton_get_option_field( 'global_contact_cta_title', "Let's Talk" ),
			'body'         => epaton_get_option_field( 'global_contact_cta_body', "Whether you are reviewing your current platform, planning a major transformation,\nor looking to reduce cost and risk, we'd welcome the opportunity to help." ),
			'button_style' => epaton_get_option_field( 'global_contact_cta_button_style', 'cyan' ),
			'button'       => epaton_get_option_field(
				'global_contact_cta_button',
				[
					'title'  => 'Speak to our team today to start the conversation.',
					'url'    => home_url( '/contact/' ),
					'target' => '',
				]
			),
		];
	}
}

if ( ! function_exists( 'epaton_render_global_contact_cta' ) ) {
	/**
	 * Render the global Contact CTA section.
	 *
	 * Outputs the full contact CTA HTML using data from Site Settings.
	 * Returns early if no title or body is configured.
	 *
	 * @return void
	 */
	function epaton_render_global_contact_cta() {
		$cta = epaton_get_global_contact_cta();

		if ( empty( $cta['title'] ) && empty( $cta['body'] ) ) {
			return;
		}
		?>
		<section class="contact-cta-section layout-padding pt-60 pt-lg-200">
			<div class="epaton-container">
				<div class="contact-cta-card">
					<?php if ( $cta['title'] ) : ?>
						<h2 class="contact-cta-title"><?php echo esc_html( $cta['title'] ); ?></h2>
					<?php endif; ?>

					<?php if ( $cta['body'] ) : ?>
						<div class="contact-cta-body"><?php echo wp_kses( $cta['body'], [ 'br' => [] ] ); ?></div>
					<?php endif; ?>

					<?php
					if ( ! empty( $cta['button'] ) && function_exists( 'epaton_render_button' ) ) :
						epaton_render_button(
							$cta['button'],
							[
								'style'     => 'btn-primary',
								'show_icon' => false,
								'class'     => 'contact-cta-button contact-cta-button-' . $cta['button_style'],
							]
						);
					endif;
					?>
				</div>
			</div>
		</section>
		<?php
	}
}
