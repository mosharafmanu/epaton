<?php
/**
 * Hero Section
 *
 * Background media hero with title, description, and CTA buttons.
 *
 * @package epaton
 */

$hero_description = get_sub_field('hero_description');
$hero_title = get_sub_field('hero_title');
$hero_buttons = get_sub_field('hero_buttons');
$media_type = get_sub_field('hero_media_type') ?: 'image';
$hero_image = get_sub_field('hero_image');
$hero_video = get_sub_field('hero_video');

// Build section classes
$section_classes = ['hero-section'];
if ('video' === $media_type && $hero_video) {
    $section_classes[] = 'has-video';
} elseif ($hero_image) {
    $section_classes[] = 'has-image';
}
?>

<div class="<?php echo esc_attr(implode(' ', $section_classes)); ?>">
	<?php if (('video' === $media_type && $hero_video) || $hero_image): ?>
		<div class="hero-background media" aria-hidden="true">
			<?php if ('video' === $media_type && $hero_video): ?>
				<?php
if (function_exists('epaton_render_video')) {
    $video_behavior = !empty($hero_video['video_behavior']) ? $hero_video['video_behavior'] : 'autoplay';

    epaton_render_video(
        $hero_video,
        [
            'behavior'           => $video_behavior,
            'autoplay'           => true,
            'autoplay_on_scroll' => false,
            'muted'              => true,
            'loop'               => true,
            'controls'           => false,
            'class'              => 'hero-video',
            'container_class'    => 'hero-video-container',
            'width'              => '100%',
            'height'             => '100%',
        ]
    );
}
?>
			<?php elseif ($hero_image): ?>
				<?php
if (function_exists('epaton_render_responsive_picture')) {
    epaton_render_responsive_picture(
        $hero_image,
        [
            'class'         => 'hero-image',
            'lazy'          => false,
            'fetchpriority' => 'high',
            'size_group'    => 'hero',
            'sizes'         => '100vw',
        ]
    );
}
?>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="hero-inner layout-padding">
		<div class="epaton-container">
			<div class="hero-grid">

				<!-- Hero Content Column -->
				<div class="hero-content">
					<div class="hero-content-inner">

						<?php if ($hero_title): ?>
							<h1 class="hero-title"><?php echo wp_kses($hero_title, ['br' => []]); ?></h1>
						<?php endif; ?>

						<?php if ($hero_description): ?>
							<p class="hero-description"><?php echo wp_kses($hero_description, ['br' => []]); ?></p>
						<?php endif; ?>

						<?php
                        if ($hero_buttons && function_exists('epaton_render_buttons')) {
                            epaton_render_buttons(
                                $hero_buttons,
                                [
                                    'wrapper_class' => 'hero-buttons btns',
                                    'default_style' => 'primary-btn',
                                    'show_icon'     => false,
                                ]
                            );
                        }
                        ?>

					</div>
				</div>

			</div>
		</div>
	</div>
</div>
