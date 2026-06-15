<?php
/**
 * Post content template
 *
 * @package epaton
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'layout-padding mt-30 mt-md-50 mt-lg-80' ); ?>>
	<div class="epaton-container">

	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

		<div class="entry-meta">
			<span class="entry-author">
				<?php echo get_avatar( get_the_author_meta( 'ID' ), 24, '', '', [ 'class' => 'entry-author-avatar' ] ); ?>
				<?php the_author_posts_link(); ?>
			</span>
			<span class="entry-date">
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			</span>
			<?php
			$categories = get_the_category();
			if ( $categories ) :
				?>
				<span class="entry-categories">
					<?php
					foreach ( $categories as $category ) {
						echo '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a>';
					}
					?>
				</span>
			<?php endif; ?>
		</div>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="entry-thumbnail">
			<?php the_post_thumbnail( 'large', [ 'class' => 'entry-thumbnail-image' ] ); ?>
		</figure>
	<?php endif; ?>

	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages(
			[
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'epaton' ),
				'after'  => '</div>',
			]
		);
		?>
	</div>

	</div>

</article>
