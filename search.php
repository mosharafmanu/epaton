<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package epaton
 */

get_header();

$search_query = get_search_query();
?>

<main id="primary" class="site-main">

	<!-- Search Results Header -->
	<section class="search-results-header layout-padding pt-50 pb-30 pt-md-70">
		<div class="search-results-header-inner">
			<h1 class="search-results-title">
				<?php
				/* translators: %s: search query. */
				printf( esc_html__( 'Search Results for: %s', 'epaton' ), '<span class="search-query">"' . esc_html( $search_query ) . '"</span>' );
				?>
			</h1>
			<?php if ( have_posts() ) : ?>
				<p class="search-results-count">
					<?php
					global $wp_query;
					/* translators: %d: number of results. */
					printf( _n( 'Found %d result', 'Found %d results', $wp_query->found_posts, 'epaton' ), $wp_query->found_posts );
					?>
				</p>
			<?php endif; ?>
		</div>
	</section>

	<?php if ( have_posts() ) : ?>

		<!-- Posts Section -->
		<section class="search-posts-section layout-padding pt-30 pb-50 pt-md-50 pb-md-70">
			<div class="search-section-header">
				<h2 class="search-section-title">
					<?php
					global $wp_query;
					/* translators: %d: number of posts. */
					printf( _n( 'Posts (%d)', 'Posts (%d)', $wp_query->found_posts, 'epaton' ), $wp_query->found_posts );
					?>
				</h2>
			</div>

			<div class="blog-grid card-grid columns-3">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', 'search' );
				endwhile;
				?>
			</div>
		</section>

		<!-- Pagination -->
		<?php
		if ( function_exists( 'epaton_render_pagination' ) ) {
			epaton_render_pagination();
		}
		?>

	<?php else : ?>

		<!-- No Results -->
		<section class="search-no-results layout-padding pt-70 pt-lg-100">
			<div class="no-results-content">
				<h2 class="no-results-title"><?php esc_html_e( 'No Results Found', 'epaton' ); ?></h2>
				<p class="no-results-text">
					<?php
					/* translators: %s: search query. */
					printf( esc_html__( 'Sorry, we couldn\'t find any results for "%s". Please try a different search term.', 'epaton' ), esc_html( $search_query ) );
					?>
				</p>

				<!-- Search Form -->
				<div class="no-results-search-form">
					<?php get_search_form(); ?>
				</div>
			</div>
		</section>

	<?php endif; ?>

</main><!-- #main -->

<?php
get_footer();
