<?php
/**
 * Hamburger Menu Component
 *
 * Renders the mobile hamburger menu
 *
 * @package epaton
 */

if ( ! function_exists( 'epaton_render_hamburger_menu' ) ) {
	/**
	 * Render hamburger menu for mobile navigation
	 *
	 * @return void
	 */
	function epaton_render_hamburger_menu() {
		?>

		<!-- Hamburger Menu Overlay -->
		<div class="hamburger-overlay"></div>

		<!-- Hamburger Menu Panel -->
		<div class="hamburger-wrapper" id="epaton-mobile-menu">

			<!-- Close Button -->
			<button class="hamburger-close menu-trigger" type="button" aria-label="<?php esc_attr_e( 'Close menu', 'epaton' ); ?>">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M1 1L19 19M19 1L1 19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
				</svg>
			</button>

			<div class="hamburger-inner">

				<!-- Mobile Menu Navigation -->
				<nav class="hamburger-navigation" role="navigation" aria-label="Mobile navigation">
					<h2 class="sr-only">Mobile navigation</h2>
					<?php
					wp_nav_menu(
						array(
							'theme_location'  => 'mainMenu',
							'container'       => 'div',
							'container_class' => 'mobile-menu',
						)
					);
					?>
				</nav>

				<!-- Contact Button -->
				<div class="hamburger-button">
					<?php
					epaton_render_header_button(
						array(
							'class'              => 'site-btn btn-outline',
							'desktop_class'      => 'hamburger-contact-button',
							'show_text'          => true,
							'show_icon'          => false,
							'show_mobile_button' => false,
						)
					);
					?>
				</div>

			</div>
		</div>

		<?php
	}
}
