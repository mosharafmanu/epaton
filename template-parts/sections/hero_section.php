<?php
/**
 * Hero Section
 *
 * Two-column hero section with content (left) and media (right), eyebrow text, title, CTA buttons, and USP items.
 *
 * @package epaton
 */

// Get ACF fields
$hero_eyebrow = get_sub_field( 'hero_eyebrow' );
$hero_title   = get_sub_field( 'hero_title' );
$hero_buttons = get_sub_field( 'hero_buttons' );
$hero_usp     = get_sub_field( 'hero_usp' );
$media_type   = get_sub_field( 'hero_media_type' );
$hero_image   = get_sub_field( 'hero_image' );
$hero_video   = get_sub_field( 'hero_video' );

// Build section classes
$section_classes = [ 'hero-section' ];
if ( 'video' === $media_type && $hero_video ) {
	$section_classes[] = 'has-video';
} elseif ( 'image' === $media_type && $hero_image ) {
	$section_classes[] = 'has-image';
}
?>

<div class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="hero-inner layout-padding">
		<div class="hero-grid">

			<!-- Hero Content Column -->
			<div class="hero-content">
				<div class="hero-content-inner">

					<?php if ( $hero_eyebrow ) : ?>
						<span class="hero-eyebrow"><?php echo esc_html( $hero_eyebrow ); ?></span>
					<?php endif; ?>

					<?php if ( $hero_title ) : ?>
						<h1 class="hero-title"><?php echo wp_kses_post( wpautop( $hero_title ) ); ?></h1>
					<?php endif; ?>

					<?php
					if ( $hero_buttons && function_exists( 'epaton_render_buttons' ) ) {
						epaton_render_buttons(
							$hero_buttons,
							[
								'wrapper_class' => 'hero-buttons btns',
								'default_style' => 'btn-accent',
								'show_icon'     => false,
							]
						);
					}
					?>

				</div>
			</div>

			<!-- Hero Media Column -->
			<?php if ( ( 'video' === $media_type && $hero_video ) || ( 'image' === $media_type && $hero_image ) ) : ?>
				<div class="hero-media media">
					<?php if ( 'video' === $media_type && $hero_video ) : ?>
						<!-- Video -->
						<?php
						if ( function_exists( 'epaton_render_video' ) ) {
							// Get video settings from ACF
							$video_behavior = ! empty( $hero_video['video_behavior'] ) ? $hero_video['video_behavior'] : 'autoplay';

							// Only use autoplay-specific settings when behavior is 'autoplay'
							$autoplay_on_scroll  = ( 'autoplay' === $video_behavior && ! empty( $hero_video['autoplay_on_scroll'] ) ) ? $hero_video['autoplay_on_scroll'] : false;
							$controls_visibility = ( 'autoplay' === $video_behavior && ! empty( $hero_video['controls_visibility'] ) ) ? $hero_video['controls_visibility'] : false;

							epaton_render_video(
								$hero_video,
								[
									'behavior'           => $video_behavior,
									'autoplay'           => true,
									'autoplay_on_scroll' => $autoplay_on_scroll,
									'muted'              => true,
									'loop'               => true,
									'controls'           => $controls_visibility,
									'class'              => 'hero-video',
									'container_class'    => 'hero-video-container',
									'width'              => '100%',
									'height'             => '100%',
								]
							);
						}
						?>
					<?php elseif ( 'image' === $media_type && $hero_image ) : ?>
						<!-- Image -->
						<?php
						if ( function_exists( 'epaton_render_responsive_picture' ) ) {
								epaton_render_responsive_picture(
									$hero_image,
									[
										'class'         => 'hero-image',
										'lazy'          => false,
										'fetchpriority' => 'high',
										'size_group'    => 'half-col-lg',
										'sizes'         => '(min-width: 768px) 50vw, 100vw',
									]
								);
						}
						?>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		</div>

		<?php if ( $hero_usp && is_array( $hero_usp ) ) : ?>
			<div class="hero-usp">
				<?php foreach ( $hero_usp as $usp ) : ?>
					<?php
					$usp_icon  = $usp['icon'] ?? '';
					$usp_title = $usp['title'] ?? '';

					// Skip if no title
					if ( empty( $usp_title ) ) {
						continue;
					}
					?>
					<div class="hero-usp-item">
						<?php if ( $usp_icon ) : ?>
							<div class="hero-usp-icon">
								<?php
								if ( function_exists( 'epaton_render_icon' ) ) {
									epaton_render_icon( $usp_icon, [ 'class' => 'icon' ] );
								}
								?>
							</div>
						<?php endif; ?>
						<?php if ( $usp_title ) : ?>
							<div class="hero-usp-title"><?php echo nl2br( esc_html( $usp_title ) ); ?></div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

	</div>
</div>
