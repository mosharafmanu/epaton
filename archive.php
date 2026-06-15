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

		<!-- Archive Posts Grid Section -->
		<section class="blog-grid-section layout-padding">
			<div class="epaton-container">
				<header class="blog-index-header">
					<h1 class="blog-index-title"><?php the_archive_title(); ?></h1>
					<?php
					$description = get_the_archive_description();
					if ( $description ) :
						?>
						<p class="blog-index-description"><?php echo wp_kses_post( $description ); ?></p>
					<?php endif; ?>
				</header>

				<?php if ( have_posts() ) : ?>

					<!-- Blog Posts Grid -->
					<div class="blog-grid card-grid mt-50 mt-lg-100 columns-3">
						<?php
						while ( have_posts() ) :
							the_post();

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
					if ( function_exists( 'epaton_render_pagination' ) ) {
						epaton_render_pagination();
					}
					?>

				<?php else : ?>

					<!-- No Posts Found -->
					<?php get_template_part( 'template-parts/content', 'none' ); ?>

				<?php endif; ?>
			</div>

		</section>

		<?php
		if ( function_exists( 'epaton_render_global_contact_cta' ) ) {
			epaton_render_global_contact_cta();
		}
		?>

	</main><!-- #main -->

<?php
get_footer();
