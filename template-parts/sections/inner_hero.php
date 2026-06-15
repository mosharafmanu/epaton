<?php
/**
 * Inner Hero Section
 *
 * Compact page hero with title, description, and CTA buttons.
 * Supports image or video background. Used on inner pages and the blog index.
 *
 * @package epaton
 */

// Allow override via $args when called outside flexible content (e.g. blog page).
$args            = $args ?? [];
$is_args_mode    = ! empty( $args );
$show_breadcrumb = ! empty( $args['show_breadcrumb'] );

if ( $is_args_mode ) {
	$context       = $args['context'] ?? 'post';
	$prefix        = $args['field_prefix'] ?? 'inner_hero_';
	$default_title = $args['default_title'] ?? '';
	$source_id     = 'options' === $context ? 'options' : get_the_ID();

	$eyebrow     = get_field( $prefix . 'eyebrow', $source_id );
	$title       = get_field( $prefix . 'title', $source_id ) ?: $default_title;
	$description = get_field( $prefix . 'description', $source_id );
	$buttons     = get_field( $prefix . 'buttons', $source_id );
	$media_type  = get_field( $prefix . 'media_type', $source_id ) ?: 'image';
	$bg_image    = get_field( $prefix . 'image', $source_id );
	$bg_video    = get_field( $prefix . 'video', $source_id );
} else {
	$eyebrow     = get_sub_field( 'inner_hero_eyebrow' );
	$title       = get_sub_field( 'inner_hero_title' );
	$description = get_sub_field( 'inner_hero_description' );
	$buttons     = get_sub_field( 'inner_hero_buttons' );
	$media_type  = get_sub_field( 'inner_hero_media_type' ) ?: 'image';
	$bg_image    = get_sub_field( 'inner_hero_image' );
	$bg_video    = get_sub_field( 'inner_hero_video' );
}

$has_video = ( 'video' === $media_type && $bg_video );
$has_image = ( 'image' === $media_type && $bg_image );

$section_classes = [ 'inner-hero-section' ];
if ( $has_video ) {
	$section_classes[] = 'has-video';
} elseif ( $has_image ) {
	$section_classes[] = 'has-image';
}
?>

<div class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">

	<?php if ( $has_video || $has_image ) : ?>
		<div class="inner-hero-background media" aria-hidden="true">
			<?php if ( $has_video ) : ?>
				<?php
				if ( function_exists( 'epaton_render_video' ) ) {
					$video_behavior = ! empty( $bg_video['video_behavior'] ) ? $bg_video['video_behavior'] : 'autoplay';

					epaton_render_video(
						$bg_video,
						[
							'behavior'           => $video_behavior,
							'autoplay'           => true,
							'autoplay_on_scroll' => false,
							'muted'              => true,
							'loop'               => true,
							'controls'           => false,
							'class'              => 'inner-hero-video',
							'container_class'    => 'inner-hero-video-container',
							'width'              => '100%',
							'height'             => '100%',
						]
					);
				}
				?>
			<?php elseif ( $has_image ) : ?>
				<?php
				if ( function_exists( 'epaton_render_responsive_picture' ) ) {
					epaton_render_responsive_picture(
						$bg_image,
						[
							'class'         => 'inner-hero-bg-image',
							'lazy'          => false,
							'fetchpriority' => 'high',
							'sizes'         => '100vw',
						]
					);
				}
				?>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="inner-hero-inner">
		<div class="epaton-container layout-padding">

			<?php if ( $show_breadcrumb && function_exists( 'epaton_render_breadcrumb' ) ) : ?>
				<?php epaton_render_breadcrumb(); ?>
			<?php endif; ?>

			<div class="inner-hero-content">

				<?php if ( $eyebrow ) : ?>
					<p class="inner-hero-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>

				<?php if ( $title ) : ?>
					<h1 class="inner-hero-title"><?php echo wp_kses( $title, [ 'br' => [] ] ); ?></h1>
				<?php endif; ?>

				<?php if ( $description ) : ?>
					<p class="inner-hero-description"><?php echo wp_kses( $description, [ 'br' => [] ] ); ?></p>
				<?php endif; ?>

				<?php
				if ( $buttons && function_exists( 'epaton_render_buttons' ) ) {
					epaton_render_buttons(
						$buttons,
						[
							'wrapper_class' => 'inner-hero-buttons btns',
							'default_style' => 'transparent-primary',
							'show_icon'     => false,
						]
					);
				}
				?>

			</div>

		</div>
	</div>

</div>
