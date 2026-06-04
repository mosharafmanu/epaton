<?php
/**
 * Single post template
 *
 * @package epaton
 */

get_header();
?>

	<main id="primary" class="site-main">

		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();

				if ( function_exists( 'have_rows' ) && have_rows( 'cms' ) ) :
					epaton_flexible_content( 'cms' );
				else :
					get_template_part( 'template-parts/content', get_post_type() );
				endif;

			endwhile;
		else :
			get_template_part( 'template-parts/content', 'none' );
		endif;
		?>

	</main>

<?php
get_footer();
