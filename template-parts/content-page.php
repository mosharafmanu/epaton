<?php
/**
 * Page content template
 *
 * @package epaton
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'layout-padding pt-30 pt-md-50 pt-lg-80' ); ?>>

	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title h2-style mb-15 mb-md-30">', '</h1>' ); ?>
	</header>

	<section class="entry-content">
		<?php
		the_content();

		wp_link_pages(
			[
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'epaton' ),
				'after'  => '</div>',
			]
		);
		?>
	</section>

</article>
