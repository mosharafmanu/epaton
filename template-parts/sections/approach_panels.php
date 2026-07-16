<?php
/**
 * Approach Panels section.
 *
 * @package epaton
 */

$eyebrow = get_sub_field('approach_panels_eyebrow');
$title = get_sub_field('approach_panels_title');
$intro_label = get_sub_field('approach_panels_intro_label');
$bullets = get_sub_field('approach_panels_bullets');
$statement = get_sub_field('approach_panels_statement');
$cards = get_sub_field('approach_panels_cards');

if (empty($eyebrow) && empty($title) && empty($bullets) && empty($statement) && empty($cards)) {
    return;
}
?>

<section class="approach-panels-section layout-padding pt-45">
		<div class="epaton-container">
			<div class="approach-panels-content">
				<?php if ($eyebrow): ?>
					<div class="approach-panels-eyebrow"><?php echo esc_html($eyebrow); ?></div>
				<?php endif; ?>

				<?php if ($title): ?>
					<h2 class="approach-panels-title"><?php echo wp_kses($title, ['br' => []]); ?></h2>
				<?php endif; ?>

				<?php if ($intro_label): ?>
					<div class="approach-panels-intro-label"><?php echo esc_html($intro_label); ?></div>
				<?php endif; ?>

				<?php if (is_array($bullets) && $bullets): ?>
					<ul class="epaton-list approach-panels-bullets">
						<?php foreach ($bullets as $bullet): ?>
							<?php if (!empty($bullet['text'])): ?>
								<li><?php echo esc_html($bullet['text']); ?></li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ($statement): ?>
					<div class="approach-panels-statement"><?php echo wp_kses($statement, ['br' => []]); ?></div>
				<?php endif; ?>

				<?php if (is_array($cards) && $cards): ?>
					<div class="approach-panels-grid">
						<?php foreach ($cards as $card): ?>
							<?php
$card_title = $card['title'] ?? '';
$card_body = $card['body'] ?? '';
$theme = !empty($card['theme']) ? $card['theme'] : 'blue';

if (empty($card_title) && empty($card_body)) {
    continue;
}
?>
							<article class="approach-panels-card approach-panels-card-<?php echo esc_attr($theme); ?>">
								<?php if ($card_title): ?>
									<h3 class="approach-panels-card-title"><?php echo esc_html($card_title); ?></h3>
								<?php endif; ?>

								<?php if ($card_body): ?>
									<div class="approach-panels-card-body"><?php echo wp_kses_post($card_body); ?></div>
								<?php endif; ?>
							</article>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
</section>
