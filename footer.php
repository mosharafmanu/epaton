<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package epaton
 */

?>

		<footer class="site-footer layout-padding pt-50 pb-50 pt-lg-75">
            <div class="epaton-container">
				<div class="site-footer-inner">
					<div class="site-footer-main">
						<div class="site-footer-brand">
							<?php
						epaton_render_site_logo(
							[
								'class'      => 'footer-logo',
								'link_class' => 'footer-logo-link',
							]
						);
						?>

							<div class="footer-company-text">
								<?php echo wp_kses(epaton_get_footer_company_text(), ['br' => []]); ?>
							</div>
						</div>

						<div class="site-footer-contact">
							<h2 class="footer-heading"><?php esc_html_e('Contact Us', 'epaton'); ?></h2>

							<div class="footer-contact-row">
								<?php
								$footer_email = epaton_get_footer_email();
								$footer_phone = epaton_get_footer_phone();
								$phone_href = preg_replace('/[^0-9+]/', '', $footer_phone);
								?>

								<?php if ($footer_email): ?>
								<a class="footer-contact-link footer-email" href="mailto:<?php echo esc_attr($footer_email); ?>"><?php echo esc_html(strtoupper($footer_email)); ?></a>
								<?php endif; ?>

								<?php if ($footer_phone): ?>
									<a class="footer-contact-link footer-phone" href="tel:<?php echo esc_attr($phone_href); ?>"><?php echo esc_html($footer_phone); ?></a>
								<?php endif; ?>
							</div>

							<h2 class="footer-heading footer-offices-heading"><?php esc_html_e('Our Offices', 'epaton'); ?></h2>

							<?php $footer_offices = epaton_get_footer_offices(); ?>
							<?php if ($footer_offices): ?>
								<div class="footer-offices">
									<?php foreach ($footer_offices as $office): ?>
										<?php
											$city = $office['city'] ?? '';
											$address = $office['address'] ?? '';

											if (empty($city) && empty($address)) {
												continue;
											}
											?>
										<div class="footer-office-row">
											<div class="footer-office-city"><?php echo esc_html(strtoupper($city)); ?></div>
											<div class="footer-office-address"><?php echo esc_html(strtoupper($address)); ?></div>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>

					<div class="site-footer-bottom">
						<div class="footer-legal">
							<span><?php echo esc_html(epaton_get_footer_copyright()); ?></span>

							<?php if (has_nav_menu('footerLegalMenu')): ?>
								<nav class="footer-legal-nav" aria-label="<?php esc_attr_e('Footer legal links', 'epaton'); ?>">
									<?php
										wp_nav_menu(
											[
												'theme_location' => 'footerLegalMenu',
												'container'      => false,
												'menu_class'     => 'footer-legal-menu',
												'depth'          => 1,
												'fallback_cb'    => false,
											]
										);
										?>
								</nav>
							<?php endif; ?>
						</div>

						<div class="footer-credit">
							<span><?php echo esc_html(epaton_get_footer_credit_text()); ?></span>
							<?php get_template_part('assets/svgs/so-marketing-white'); ?>
						</div>
					</div>
				</div>
			</div>
		</footer>

	<?php
// Hamburger Menu
// epaton_render_hamburger_menu();
?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
