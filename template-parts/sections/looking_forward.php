<?php
/**
 * Looking Forward section.
 *
 * @package epaton
 */

$eyebrow = get_sub_field('looking_forward_eyebrow');
$statements = get_sub_field('looking_forward_statements');

if (empty($eyebrow) && empty($statements)) {
    return;
}
?>

<section class="looking-forward-section layout-padding">
	<div class="epaton-container">
		<div class="looking-forward-content">
			<?php if ($eyebrow): ?>
				<div class="looking-forward-eyebrow"><?php echo esc_html($eyebrow); ?></div>
			<?php endif; ?>

			<?php if (is_array($statements) && $statements): ?>
				<div class="looking-forward-statements">
					<?php foreach ($statements as $statement): ?>
						<?php if (!empty($statement['text'])): ?>
							<p><?php echo wp_kses($statement['text'], ['br' => []]); ?></p>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
