<?php
/**
 * Contact CTA section.
 *
 * Supports both global (Site Settings) and per-page content.
 * The "Content Source" toggle determines which content is used.
 *
 * @package epaton
 */

$use_global = get_sub_field('contact_cta_use_global');

if (null === $use_global || $use_global) {
	$title        = get_field('global_contact_cta_title', 'options');
	$body         = get_field('global_contact_cta_body', 'options');
	$button_style = get_field('global_contact_cta_button_style', 'options');
	$button       = get_field('global_contact_cta_button', 'options');
} else {
	$title        = get_sub_field('contact_cta_title');
	$body         = get_sub_field('contact_cta_body');
	$button_style = get_sub_field('contact_cta_button_style');
	$button       = get_sub_field('contact_cta_button');
}

if (!in_array($button_style, ['cyan', 'blue'], true)) {
	$button_style = 'cyan';
}

if (empty($title) && empty($body) && empty($button)) {
	return;
}
?>

<section class="contact-cta-section layout-padding pt-60 pt-lg-200">
		<div class="epaton-container">
			<div class="contact-cta-card">
				<?php if ($title): ?>
					<h2 class="contact-cta-title"><?php echo esc_html($title); ?></h2>
				<?php endif; ?>

				<?php if ($body): ?>
					<div class="contact-cta-body"><?php echo wp_kses($body, ['br' => []]); ?></div>
				<?php endif; ?>

				<?php
if ($button && function_exists('epaton_render_button')) {
	epaton_render_button(
		$button,
		[
			'style'     => 'btn-primary',
			'show_icon' => false,
			'class'     => 'contact-cta-button contact-cta-button-' . $button_style,
		]
	);
}
?>
			</div>
		</div>
</section>
