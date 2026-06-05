<?php
/**
 * Our Clients section.
 *
 * @package epaton
 */

$eyebrow = get_sub_field('clients_logos_eyebrow');
$logos_eyebrow = get_sub_field('clients_logos_logos_eyebrow');
$title = get_sub_field('clients_logos_title');
$bullets = get_sub_field('clients_logos_bullets');
$body = get_sub_field('clients_logos_body');
$button = get_sub_field('clients_logos_button');
$logos = get_sub_field('clients_logos_items');

if (empty($eyebrow) && empty($title) && empty($bullets) && empty($body) && empty($logos)) {
    return;
}
?>

<section class="clients-logos-section layout-padding">
		<div class="epaton-container">
			<div class="clients-logos-grid">
				<div class="clients-logos-content">
					<?php if ($eyebrow): ?>
						<div class="clients-logos-eyebrow"><?php echo esc_html($eyebrow); ?></div>
					<?php endif; ?>

					<?php if ($title): ?>
						<h2 class="clients-logos-title"><?php echo wp_kses($title, ['br' => []]); ?></h2>
					<?php endif; ?>

					<?php if (is_array($bullets) && $bullets): ?>
						<ul class="epaton-list clients-logos-bullets">
							<?php foreach ($bullets as $bullet): ?>
								<?php if (!empty($bullet['text'])): ?>
									<li><?php echo esc_html($bullet['text']); ?></li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ($body): ?>
						<div class="clients-logos-body"><?php echo wp_kses($body, ['br' => []]); ?></div>
					<?php endif; ?>

					<?php
if ($button && function_exists('epaton_render_button')) {
    epaton_render_button(
        $button,
        [
            'style'     => 'accent-gradient',
            'show_icon' => false,
            'class'     => 'clients-logos-button',
        ]
    );
}
?>
				</div>

				<div class="clients-logos-list-wrap">
					<?php if ($logos_eyebrow): ?>
						<div class="clients-logos-list-eyebrow"><?php echo esc_html($logos_eyebrow); ?></div>
					<?php endif; ?>

					<?php if (is_array($logos) && $logos): ?>
						<div class="clients-logos-list">
							<?php foreach ($logos as $logo): ?>
								<?php
$logo_image = $logo['logo'] ?? [];
$logo_name = $logo['name'] ?? '';

if (empty($logo_image) || !function_exists('epaton_render_icon')) {
    continue;
}
?>
								<div class="clients-logos-item">
									<?php
epaton_render_icon(
    $logo_image,
    [
        'class' => 'clients-logos-logo',
        'alt'   => $logo_name,
    ]
);
?>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
</section>
