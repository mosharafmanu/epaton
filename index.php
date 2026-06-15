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

$blog_title       = function_exists( 'get_field' ) ? get_field( 'blog_options_title', 'options' ) : '';
$blog_description = function_exists( 'get_field' ) ? get_field( 'blog_options_description', 'options' ) : '';

$blog_title       = $blog_title ?: __( 'NEWS', 'epaton' );
$blog_description = $blog_description ?: __( 'Want to keep learning? Explore our news, and the latest industry insights.', 'epaton' );
?>

	<main id="primary" class="site-main blog-page">

		<!-- Blog Posts Grid Section -->
		<section class="blog-grid-section layout-padding">
			<div class="epaton-container">
				<header class="blog-index-header">
				<h1 class="blog-index-title"><?php echo esc_html( $blog_title ); ?></h1>
				<?php if ( $blog_description ) : ?>
					<p class="blog-index-description"><?php echo esc_html( $blog_description ); ?></p>
				<?php endif; ?>
			</header>

				<?php if ( have_posts() ) : ?>

					<!-- Blog Posts Grid -->
					<div class="blog-grid card-grid mt-50 mt-lg-100 columns-3">
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
