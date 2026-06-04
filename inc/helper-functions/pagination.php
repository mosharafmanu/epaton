<?php
/**
 * Pagination Helper
 *
 * Renders blog pagination with custom SVG arrows.
 *
 * @package epaton
 */

/**
 * Render pagination with SVG navigation arrows
 *
 * @return void
 */
function epaton_render_pagination() {
	// Grab SVG icons for arrows
	ob_start();
	get_template_part( 'assets/svgs/angle-left-pagination' );
	$prev_arrow = ob_get_clean();

	ob_start();
	get_template_part( 'assets/svgs/angle-right-pagination' );
	$next_arrow = ob_get_clean();

	$args = [
		'mid_size'  => 1, // Reduced from 2 to 1 for better mobile display
		'end_size'  => 1, // Show first and last page
		'prev_text' => '<span class="pagination-arrow">' . $prev_arrow . '</span>',
		'next_text' => '<span class="pagination-arrow">' . $next_arrow . '</span>',
		'type'      => 'list',
	];

	$pagination = paginate_links( $args );

	if ( ! $pagination ) {
		return;
	}

	// Allow SVG and pagination markup through kses
	$allowed_tags = [
		'nav'  => [
			'class'      => [],
			'aria-label' => [],
		],
		'ul'   => [ 'class' => [] ],
		'li'   => [ 'class' => [] ],
		'a'    => [
			'class' => [],
			'href'  => [],
		],
		'span' => [
			'class'        => [],
			'aria-current' => [],
		],
		'svg'  => [
			'width'   => [],
			'height'  => [],
			'viewbox' => [],
			'fill'    => [],
			'xmlns'   => [],
		],
		'path' => [
			'd'            => [],
			'stroke'       => [],
			'stroke-width' => [],
			'fill'         => [],
		],
	];
	?>
	<nav class="blog-pagination pagination" aria-label="<?php esc_attr_e( 'Blog pagination', 'epaton' ); ?>">
		<?php echo wp_kses( $pagination, $allowed_tags ); ?>
	</nav>
	<?php
}


