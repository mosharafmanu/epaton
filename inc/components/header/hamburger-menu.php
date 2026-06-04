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
		<div class="hamburger-wrapper">
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
