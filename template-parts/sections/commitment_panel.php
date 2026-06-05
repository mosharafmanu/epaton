<?php
/**
 * Commitment Panel section.
 *
 * @package epaton
 */

$eyebrow = get_sub_field('commitment_panel_eyebrow');
$title = get_sub_field('commitment_panel_title');
$intro_label = get_sub_field('commitment_panel_intro_label');
$bullets = get_sub_field('commitment_panel_bullets');
$statement = get_sub_field('commitment_panel_statement');

if (empty($eyebrow) && empty($title) && empty($intro_label) && empty($bullets) && empty($statement)) {
    return;
}
?>

<section class="commitment-panel-section layout-padding">
		<div class="epaton-container">
			<div class="commitment-panel-card">
				<div class="commitment-panel-content">
					<?php if ($eyebrow): ?>
						<div class="commitment-panel-eyebrow"><?php echo esc_html($eyebrow); ?></div>
					<?php endif; ?>

					<?php if ($title): ?>
						<h2 class="commitment-panel-title"><?php echo wp_kses($title, ['br' => []]); ?></h2>
					<?php endif; ?>

					<?php if ($intro_label): ?>
						<div class="commitment-panel-intro-label"><?php echo esc_html($intro_label); ?></div>
					<?php endif; ?>

					<?php if (is_array($bullets) && $bullets): ?>
						<ul class="epaton-list commitment-panel-bullets">
							<?php foreach ($bullets as $bullet): ?>
								<?php if (!empty($bullet['text'])): ?>
									<li><?php echo esc_html($bullet['text']); ?></li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ($statement): ?>
						<div class="commitment-panel-statement"><?php echo wp_kses($statement, ['br' => []]); ?></div>
					<?php endif; ?>
				</div>
			</div>
		</div>
</section>
