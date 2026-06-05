<?php
/**
 * Services Listing Section
 *
 * Queries all published service posts and renders them as horizontal cards.
 * Cards alternate between a light and blue theme.
 *
 * @package epaton
 */

$heading     = get_sub_field( 'services_listing_heading' );
$button_text = get_sub_field( 'services_listing_button_text' ) ?: 'Find Out More';

$services = new WP_Query(
	[
		'post_type'      => 'service',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	]
);

if ( ! $services->have_posts() ) {
	return;
}
?>

<div class="services-listing-section">
	<div class="services-listing-inner layout-padding">
		<div class="epaton-container">

			<?php if ( $heading ) : ?>
				<h2 class="services-listing-heading"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>

			<div class="services-list">
				<?php
				$index = 0;
				while ( $services->have_posts() ) :
					$services->the_post();

					$service_id    = get_the_ID();
					$service_title = get_the_title();
					$service_url   = get_permalink();
					$excerpt       = get_the_excerpt();
					$thumbnail_id  = get_post_thumbnail_id( $service_id );
					$thumbnail     = $thumbnail_id ? [
						'ID'  => $thumbnail_id,
						'url' => wp_get_attachment_image_url( $thumbnail_id, 'full' ),
					] : null;

					$theme = ( 0 === $index % 2 ) ? 'light' : 'blue';
					$index++;
					?>

					<article class="service-card theme-<?php echo esc_attr( $theme ); ?>">

						<div class="service-card-content">
							<h3 class="service-card-title">
								<a href="<?php echo esc_url( $service_url ); ?>"><?php echo esc_html( $service_title ); ?></a>
							</h3>

							<?php if ( $excerpt ) : ?>
								<p class="service-card-description"><?php echo esc_html( $excerpt ); ?></p>
							<?php endif; ?>

							<a href="<?php echo esc_url( $service_url ); ?>" class="site-btn service-card-btn">
								<span class="btn-text"><?php echo esc_html( $button_text ); ?></span>
							</a>
						</div>

						<?php if ( $thumbnail ) : ?>
							<div class="service-card-image">
								<?php
								if ( function_exists( 'epaton_render_responsive_picture' ) ) {
									epaton_render_responsive_picture(
										$thumbnail,
										[
											'class' => 'service-card-img',
											'lazy'  => true,
											'sizes' => '(max-width: 767px) 100vw, 45vw',
										]
									);
								} else {
									echo wp_get_attachment_image( $thumbnail_id, 'large', false, [ 'class' => 'service-card-img', 'loading' => 'lazy' ] );
								}
								?>
							</div>
						<?php endif; ?>

					</article>

				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			</div>

		</div>
	</div>
</div>
