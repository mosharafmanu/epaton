<?php
/**
 * Blog card component.
 *
 * @package epaton
 */

if ( ! function_exists( 'epaton_render_blog_card' ) ) {
	/**
	 * Render a blog/news card.
	 *
	 * @param WP_Post|int|null $post Post object or ID.
	 * @param array            $args Optional args.
	 * @return void
	 */
	function epaton_render_blog_card( $post = null, $args = [] ) {
		$post = get_post( $post );

		if ( ! $post ) {
			return;
		}

		$args = wp_parse_args(
			$args,
			[
				'lazy' => true,
			]
		);

		$post_id      = (int) $post->ID;
		$title        = get_the_title( $post_id );
		$url          = get_permalink( $post_id );
		$excerpt      = get_the_excerpt( $post_id );
		$thumbnail_id = get_post_thumbnail_id( $post_id );
		$categories   = get_the_category( $post_id );
		$label        = ! empty( $categories ) ? $categories[0]->name : __( 'News', 'epaton' );
		?>

		<article <?php post_class( 'blog-card', $post_id ); ?>>
			<a class="blog-card-link" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
				<div class="blog-card-image-wrap">
					<?php if ( $thumbnail_id ) : ?>
						<?php
						echo wp_get_attachment_image(
							$thumbnail_id,
							'epaton-600',
							false,
							[
								'class'   => 'blog-card-image',
								'loading' => $args['lazy'] ? 'lazy' : 'eager',
								'sizes'   => '(max-width: 767px) 100vw, 33vw',
							]
						);
						?>
					<?php endif; ?>
					<span class="blog-card-label"><?php echo esc_html( $label ); ?></span>
				</div>

				<div class="blog-card-content">
					<h4 class="blog-card-title"><?php echo esc_html( $title ); ?></h4>

					<?php if ( $excerpt ) : ?>
						<p class="blog-card-excerpt"><?php echo esc_html( $excerpt ); ?></p>
					<?php endif; ?>
				</div>
			</a>
		</article>
		<?php
	}
}
