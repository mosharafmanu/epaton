<?php
/**
 * Intro with Featured Services section.
 *
 * @package epaton
 */

$eyebrow = get_sub_field('featured_services_intro_eyebrow');
$services_eyebrow = get_sub_field('featured_services_intro_services_eyebrow');
$title = get_sub_field('featured_services_intro_title');
$body = get_sub_field('featured_services_intro_body');
$button = get_sub_field('featured_services_intro_button');
$source = get_sub_field('featured_services_intro_source') ?: 'dynamic';
$service_cards = [];

if ('manual' === $source) {
    $manual_services = get_sub_field('featured_services_intro_manual_services');

    if (is_array($manual_services)) {
        foreach ($manual_services as $manual_service) {
            if (empty($manual_service['title'])) {
                continue;
            }

            $link = $manual_service['link'] ?? [];

            $service_cards[] = [
                'title'  => $manual_service['title'],
                'url'    => !empty($link['url']) ? $link['url'] : '',
                'target' => !empty($link['target']) ? $link['target'] : '',
                'image'  => $manual_service['image'] ?? [],
            ];
        }
    }
} else {
    $selected_services = get_sub_field('featured_services_intro_services');

    if (is_array($selected_services)) {
        foreach ($selected_services as $service) {
            $service_id = is_object($service) ? (int) $service->ID : (int) $service;

            if (!$service_id) {
                continue;
            }

            $image = function_exists('get_field') ? get_field('service_secondary_thumbnail', $service_id) : [];

            if (empty($image)) {
                $thumbnail_id = get_post_thumbnail_id($service_id);

                if ($thumbnail_id) {
                    $image = [
                        'ID'  => $thumbnail_id,
                        'url' => wp_get_attachment_image_url($thumbnail_id, 'full'),
                        'alt' => get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true),
                    ];
                }
            }

            $service_cards[] = [
                'title'  => get_the_title($service_id),
                'url'    => get_permalink($service_id),
                'target' => '',
                'image'  => $image,
            ];
        }
    }
}
?>

<section class="featured-services-intro-section layout-padding">
		<div class="epaton-container">
			<div class="featured-services-intro-grid">
				<div class="featured-services-intro-content">
					<?php if ($eyebrow): ?>
						<div class="featured-services-intro-eyebrow"><?php echo esc_html($eyebrow); ?></div>
					<?php endif; ?>

					<?php if ($title): ?>
						<h2 class="featured-services-intro-title"><?php echo wp_kses($title, ['br' => []]); ?></h2>
					<?php endif; ?>

					<?php if ($body): ?>
						<div class="featured-services-intro-body">
							<?php echo wp_kses_post($body); ?>
						</div>
					<?php endif; ?>

					<?php
if ($button && function_exists('epaton_render_button')) {
    epaton_render_button(
        $button,
        [
            'style'     => 'primary-gradient',
            'show_icon' => false,
            'class'     => 'featured-services-intro-button',
        ]
    );
}
?>
				</div>

				<?php if ($services_eyebrow || $service_cards): ?>
					<div class="featured-services-intro-services">
						<?php if ($services_eyebrow): ?>
							<div class="featured-services-intro-eyebrow"><?php echo esc_html($services_eyebrow); ?></div>
						<?php endif; ?>

						<?php if ($service_cards): ?>
							<div class="featured-services-intro-card-list">
								<?php foreach ($service_cards as $service_card): ?>
									<?php
$card_url = $service_card['url'] ?? '';
$card_target = $service_card['target'] ?? '';
?>
									<?php if ($card_url): ?>
										<a
											class="featured-services-intro-card"
											href="<?php echo esc_url($card_url); ?>"
											<?php if ($card_target): ?>
												target="<?php echo esc_attr($card_target); ?>"
												<?php if ('_blank' === $card_target): ?>
													rel="noopener"
												<?php endif; ?>
											<?php endif; ?>
										>
									<?php else: ?>
										<div class="featured-services-intro-card">
									<?php endif; ?>
										<?php if (!empty($service_card['image']) && function_exists('epaton_render_responsive_picture')): ?>
											<?php
epaton_render_responsive_picture(
    $service_card['image'],
    [
        'class'      => 'featured-services-intro-card-image',
        'size_group' => 'card',
        'sizes'      => '(min-width: 992px) 18rem, 100vw',
    ]
);
?>
										<?php endif; ?>
										<span class="featured-services-intro-card-overlay"></span>
										<span class="featured-services-intro-card-content">
											<span class="featured-services-intro-card-title"><?php echo esc_html($service_card['title']); ?></span>
											<span class="featured-services-intro-card-link"><?php esc_html_e('Find out more', 'epaton'); ?></span>
										</span>
									<?php if ($card_url): ?>
										</a>
									<?php else: ?>
										</div>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
</section>
