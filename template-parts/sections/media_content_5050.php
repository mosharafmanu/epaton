<?php
/**
 * Media Content 50/50 Section
 *
 * Single 50/50 row with content on one side and image or video on the other.
 * Add multiple instances of this section to the page builder for stacked rows.
 *
 * @package epaton
 */

$eyebrow        = get_sub_field( 'mc5050_eyebrow' );
$title          = get_sub_field( 'mc5050_title' );
$body           = get_sub_field( 'mc5050_body' );
$button         = get_sub_field( 'mc5050_button' );
$media_position = get_sub_field( 'mc5050_media_position' ) ?: 'right';
$media_type     = get_sub_field( 'mc5050_media_type' ) ?: 'image';
$image          = get_sub_field( 'mc5050_image' );
$video          = get_sub_field( 'mc5050_video' );

$has_image = ( 'image' === $media_type && ! empty( $image ) );
$has_video = ( 'video' === $media_type && ! empty( $video ) );
$has_media = $has_image || $has_video;

$row_classes = [ 'media-content-5050-row mt-50 mt-lg-90 ', 'media-' . $media_position ];
?>

<div class="media-content-5050-section">
	<div class="media-content-5050-inner layout-padding">
		<div class="epaton-container">

			<div class="<?php echo esc_attr( implode( ' ', $row_classes ) ); ?>">

				<!-- Content column -->
				<div class="mc5050-content">

					<?php if ( $eyebrow ) : ?>
						<p class="mc5050-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
					<?php endif; ?>

					<?php if ( $title ) : ?>
						<h2 class="mc5050-title"><?php echo wp_kses( $title, [ 'br' => [] ] ); ?></h2>
					<?php endif; ?>

					<?php if ( $body ) : ?>
						<div class="mc5050-body wysiwyg-content"><?php echo wp_kses_post( $body ); ?></div>
					<?php endif; ?>

					<?php if ( ! empty( $button['url'] ) ) : ?>
						<a href="<?php echo esc_url( $button['url'] ); ?>"
						   class="site-btn mc5050-btn primary-gradient"
						   target="<?php echo esc_attr( $button['target'] ?: '_self' ); ?>">
							<span class="btn-text"><?php echo esc_html( $button['title'] ); ?></span>
						</a>
					<?php endif; ?>

				</div>

				<!-- Media column -->
				<?php if ( $has_media ) : ?>
					<div class="mc5050-media media">

						<?php if ( $has_video && function_exists( 'epaton_render_video' ) ) : ?>
							<?php
							$video_behavior = ! empty( $video['video_behavior'] ) ? $video['video_behavior'] : 'autoplay';
							epaton_render_video(
								$video,
								[
									'behavior'           => $video_behavior,
									'autoplay'           => true,
									'autoplay_on_scroll' => ! empty( $video['autoplay_on_scroll'] ),
									'muted'              => true,
									'loop'               => true,
									'controls'           => ! empty( $video['controls_visibility'] ),
									'class'              => 'mc5050-video',
									'container_class'    => 'mc5050-video-container',
								]
							);
							?>

						<?php elseif ( $has_image && function_exists( 'epaton_render_responsive_picture' ) ) : ?>
							<?php
							epaton_render_responsive_picture(
								$image,
								[
									'class' => 'mc5050-img',
									'lazy'  => true,
									'sizes' => '(max-width: 767px) 100vw, 50vw',
								]
							);
							?>
						<?php endif; ?>

					</div>
				<?php endif; ?>

			</div>

		</div>
	</div>
</div>
