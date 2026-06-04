<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package epaton
 */

get_header();
?>

	<main id="primary" class="site-main blog-archive-page">

		<!-- Breadcrumb Section -->
		<section class="breadcrumb-section layout-padding">
			<?php
			if ( function_exists( 'epaton_breadcrumb' ) ) {
				epaton_breadcrumb();
			}
			?>
		</section>

		<!-- Archive Posts Grid Section -->
		<section class="blog-grid-section layout-padding pt-50">
			<?php if ( have_posts() ) : ?>

				<!-- Blog Posts Grid -->
				<div class="blog-grid card-grid columns-4">
					<?php
					while ( have_posts() ) :
						the_post();

						// Render blog card using the blog card component
						if ( function_exists( 'epaton_render_blog_card' ) ) {
							epaton_render_blog_card(
								get_post(),
								[
									'read_more_text' => __( 'Read more', 'epaton' ),
									'lazy'           => true,
								]
							);
						}

					endwhile;
					?>
				</div>

				<?php
				// Render pagination
				if ( function_exists( 'epaton_render_pagination' ) ) {
					epaton_render_pagination();
				}
				?>

			<?php else : ?>

				<!-- No Posts Found -->
				<?php get_template_part( 'template-parts/content', 'none' ); ?>

			<?php endif; ?>

		</section>

	</main><!-- #main -->

<?php
get_footer();
