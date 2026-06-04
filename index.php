<?php
/**
 * The main template file (Blog Page)
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package epaton
 */

get_header();
?>

	<main id="primary" class="site-main blog-page">

		<?php
		// Include Inner Hero Section from Blog Options with breadcrumb inside
		get_template_part(
			'template-parts/sections/inner_hero',
			null,
			[
				'context'         => 'options',
				'field_prefix'    => 'blog_hero_',
				'default_title'   => 'Blog',
				'show_breadcrumb' => true,
			]
		);
		?>

		<!-- Blog Posts Grid Section -->
		<section class="blog-grid-section layout-padding pt-70 pt-lg-100">
			<?php if ( have_posts() ) : ?>

				<!-- Blog Posts Grid -->
				<div class="blog-grid card-grid columns-4">
					<?php
					while ( have_posts() ) :
						the_post();

						// Render blog card using the new blog card component
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
