<?php
/**
 * The template for displaying all pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package epaton
 */

get_header();
?>

	<main id="primary" class="site-main page-<?php echo esc_attr( get_post_field( 'post_name' ) ); ?>">

		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();

				if ( function_exists( 'have_rows' ) && have_rows( 'cms' ) ) :
					epaton_flexible_content( 'cms' );
				else :
					get_template_part( 'template-parts/content', 'page' );
				endif;

			endwhile;
		else :
			get_template_part( 'template-parts/content', 'none' );
		endif;
		?>

	</main>

<?php
get_footer();
