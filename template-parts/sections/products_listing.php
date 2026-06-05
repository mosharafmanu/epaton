<?php
/**
 * Products Listing Section
 *
 * Queries all published product posts and renders them as horizontal cards.
 *
 * @package epaton
 */

$heading     = get_sub_field( 'products_listing_heading' );
$button_text = get_sub_field( 'products_listing_button_text' ) ?: 'Find Out More';

$products = new WP_Query(
	[
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	]
);

if ( ! $products->have_posts() ) {
	return;
}
?>

<div class="products-listing-section">
	<div class="products-listing-inner layout-padding">
		<div class="epaton-container">

			<?php if ( $heading ) : ?>
				<h2 class="products-listing-heading"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>

			<div class="products-list">
				<?php
				while ( $products->have_posts() ) :
					$products->the_post();

					$product_id    = get_the_ID();
					$product_title = get_the_title();
					$product_url   = get_permalink();
					$excerpt       = get_the_excerpt();
					$thumbnail_id  = get_post_thumbnail_id( $product_id );
					$thumbnail     = $thumbnail_id ? [
						'ID'  => $thumbnail_id,
						'url' => wp_get_attachment_image_url( $thumbnail_id, 'full' ),
					] : null;
					?>

					<article class="product-card">

						<div class="product-card-content">
							<h3 class="product-card-title">
								<a href="<?php echo esc_url( $product_url ); ?>"><?php echo esc_html( $product_title ); ?></a>
							</h3>

							<?php if ( $excerpt ) : ?>
								<p class="product-card-description"><?php echo esc_html( $excerpt ); ?></p>
							<?php endif; ?>

							<a href="<?php echo esc_url( $product_url ); ?>" class="site-btn product-card-btn">
								<span class="btn-text"><?php echo esc_html( $button_text ); ?></span>
							</a>
						</div>

						<?php if ( $thumbnail ) : ?>
							<div class="product-card-image">
								<?php
								if ( function_exists( 'epaton_render_responsive_picture' ) ) {
									epaton_render_responsive_picture(
										$thumbnail,
										[
											'class' => 'product-card-img',
											'lazy'  => true,
											'sizes' => '(max-width: 767px) 100vw, 45vw',
										]
									);
								} else {
									echo wp_get_attachment_image( $thumbnail_id, 'large', false, [ 'class' => 'product-card-img', 'loading' => 'lazy' ] );
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
