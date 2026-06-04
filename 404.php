<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package epaton
 */

get_header();

// SEO: Set proper HTTP status code
status_header( 404 );
nocache_headers();
?>

	<main id="primary" class="site-main">

		<section class="error-404 not-found layout-padding" itemscope itemtype="https://schema.org/WebPage">
			<div class="error-404-wrapper">

					<!-- 404 Number with Animation -->
					<div class="error-404-number">
						<span class="digit" data-digit="4">4</span>
						<span class="digit digit-zero" data-digit="0">0</span>
						<span class="digit" data-digit="4">4</span>
					</div>

					<!-- Error Message -->
					<h1 class="error-404-title"><?php esc_html_e( 'Page Not Found', 'epaton' ); ?></h1>

					<p class="error-404-text"><?php esc_html_e( 'The page you\'re looking for doesn\'t exist or has been moved. Let\'s get you back on track.', 'epaton' ); ?></p>

					<!-- Action Buttons -->
					<div class="error-404-actions btns">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-btn btn-accent">
							<span class="btn-text"><?php esc_html_e( 'Back to Home', 'epaton' ); ?></span>
							<span class="btn-icon">
								<?php get_template_part( 'assets/svgs/double-angle-right' ); ?>
							</span>
						</a>
					</div>

				</div><!-- .error-404-wrapper -->
		</section><!-- .error-404 -->

	</main><!-- #main -->

<?php
get_footer();
