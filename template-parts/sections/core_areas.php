<?php
/**
 * Core Areas section.
 *
 * @package epaton
 */

$heading = get_sub_field('core_areas_heading');
$items = get_sub_field('core_areas_items');

if (empty($heading) && empty($items)) {
    return;
}
?>

<section class="core-areas-section layout-padding">
		<div class="epaton-container">
			<?php if ($heading): ?>
				<h2 class="core-areas-heading"><?php echo esc_html($heading); ?></h2>
			<?php endif; ?>

			<?php if (is_array($items) && $items): ?>
				<div class="core-areas-grid">
					<?php foreach ($items as $item): ?>
						<?php
$item_title = $item['title'] ?? '';
$item_text = $item['text'] ?? '';
$theme = !empty($item['theme']) ? $item['theme'] : 'blue';

if (empty($item_title) && empty($item_text)) {
    continue;
}
?>
						<article class="core-areas-card core-areas-card-<?php echo esc_attr($theme); ?>">
							<?php if ($item_title): ?>
								<h3 class="core-areas-card-title"><?php echo esc_html($item_title); ?></h3>
							<?php endif; ?>

							<?php if ($item_text): ?>
								<div class="core-areas-card-text"><?php echo wp_kses($item_text, ['br' => []]); ?></div>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
</section>
