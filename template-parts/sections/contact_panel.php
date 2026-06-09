<?php
/**
 * Contact Panel section.
 *
 * @package epaton
 */

$section_title  = get_sub_field( 'contact_panel_section_title' ) ?: 'CONTACT US';
$title          = get_sub_field( 'contact_panel_title' ) ?: 'Let’s Talk';
$form_shortcode = get_sub_field( 'contact_panel_form_shortcode' ) ?: '[contact-form-7 id="535"]';

$email = function_exists( 'epaton_get_footer_email' ) ? epaton_get_footer_email() : 'sales@epaton.co.uk';
$phone = function_exists( 'epaton_get_footer_phone' ) ? epaton_get_footer_phone() : '+44 (0)3333 111 001';
$phone_href = preg_replace( '/[^0-9+]/', '', $phone );
$social_links = function_exists( 'epaton_get_social_links' ) ? epaton_get_social_links() : [];

if ( empty( $section_title ) && empty( $title ) && empty( $form_shortcode ) && empty( $email ) && empty( $phone ) && empty( $social_links ) ) {
	return;
}
?>

<section class="contact-panel-section layout-padding mt-50 mb-70 mb-lg-200">
	<div class="epaton-container">
		<?php if ( $section_title ) : ?>
			<h2 class="contact-panel-section-title"><?php echo esc_html( $section_title ); ?></h2>
		<?php endif; ?>

		<div class="contact-panel mt-40 mt-lg-90">
			<?php if ( $title ) : ?>
				<h2 class="contact-panel-title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>

			<div class="contact-panel-grid">
				<?php if ( $form_shortcode ) : ?>
					<div class="contact-panel-form">
						<?php echo do_shortcode( $form_shortcode ); ?>
					</div>
				<?php endif; ?>

				<div class="contact-panel-info">
					<?php if ( $email ) : ?>
						<a class="contact-panel-info-link" href="mailto:<?php echo esc_attr( $email ); ?>">
							<?php echo esc_html( strtoupper( $email ) ); ?>
						</a>
					<?php endif; ?>

					<?php if ( $phone ) : ?>
						<a class="contact-panel-info-link" href="tel:<?php echo esc_attr( $phone_href ); ?>">
							<?php echo esc_html( $phone ); ?>
						</a>
					<?php endif; ?>

					<?php if ( is_array( $social_links ) && $social_links ) : ?>
						<div class="contact-panel-socials" aria-label="<?php esc_attr_e( 'Social links', 'epaton' ); ?>">
							<?php foreach ( $social_links as $social ) : ?>
								<?php
								$icon  = $social['icon'] ?? [];
								$link  = $social['link'] ?? [];
								$label = $social['label'] ?? '';
								$url   = is_array( $link ) ? ( $link['url'] ?? '' ) : '';
								$target = is_array( $link ) ? ( $link['target'] ?? '' ) : '';

								if ( empty( $icon ) || empty( $url ) || ! function_exists( 'epaton_render_icon' ) ) {
									continue;
								}
								?>
								<a class="contact-panel-social-link" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $label ); ?>"<?php echo '_blank' === $target ? ' target="_blank" rel="noopener noreferrer"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
									<?php
									epaton_render_icon(
										$icon,
										[
											'class' => 'contact-panel-social-icon',
											'alt'   => $label,
										]
									);
									?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
