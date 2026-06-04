<?php
/**
 * Breadcrumb Navigation
 *
 * @package epaton
 */

if ( ! function_exists( 'epaton_breadcrumb' ) ) {
	/**
	 * Display breadcrumb navigation
	 *
	 * @param bool   $layout_padding Add layout-padding class. Default false.
	 * @param string $margin_top     Margin top utility class. Default 'mt-30'.
	 * @param string $margin_bottom  Margin bottom utility class. Default ''.
	 */
	function epaton_breadcrumb( $layout_padding = false, $margin_top = 'mt-30', $margin_bottom = '' ) {
		// Don't show breadcrumbs on homepage
		if ( is_front_page() ) {
			return;
		}

		// Build CSS classes
		$classes = [ 'epaton-breadcrumb' ];

		if ( $layout_padding ) {
			$classes[] = 'layout-padding';
		}
		if ( ! empty( $margin_top ) ) {
			$classes[] = $margin_top;
		}
		if ( ! empty( $margin_bottom ) ) {
			$classes[] = $margin_bottom;
		}

		echo '<nav class="' . esc_attr( implode( ' ', $classes ) ) . '" role="navigation" aria-label="Breadcrumb navigation">';
		echo '<h2 class="sr-only">Breadcrumb navigation</h2>';
		echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'epaton' ) . '</a>';
		echo ' <span class="breadcrumb-separator">/</span> ';

		// Blog archive
		if ( is_home() ) {
			$posts_page = get_option( 'page_for_posts' );
			if ( $posts_page ) {
				echo '<span class="current">' . esc_html( get_the_title( $posts_page ) ) . '</span>';
			} else {
				echo '<span class="current">' . esc_html__( 'Blog', 'epaton' ) . '</span>';
			}

		// Single post
		} elseif ( is_singular( 'post' ) ) {
			$posts_page = get_option( 'page_for_posts' );
			if ( $posts_page ) {
				echo '<a href="' . esc_url( get_permalink( $posts_page ) ) . '">' . esc_html( get_the_title( $posts_page ) ) . '</a>';
			} else {
				echo '<a href="' . esc_url( home_url( '/blog/' ) ) . '">' . esc_html__( 'Blog', 'epaton' ) . '</a>';
			}
			echo ' <span class="breadcrumb-separator">/</span> ';
			echo '<span class="current">' . esc_html( get_the_title() ) . '</span>';

		// Pages (including parents)
		} elseif ( is_page() ) {
			$parents   = [];
			$parent_id = wp_get_post_parent_id( get_the_ID() );

			// Walk up the page hierarchy
			while ( $parent_id ) {
				$parent = get_post( $parent_id );
				if ( ! $parent ) {
					break;
				}
				$parents[]  = '<a href="' . esc_url( get_permalink( $parent->ID ) ) . '">' . esc_html( get_the_title( $parent->ID ) ) . '</a>';
				$parent_id = $parent->post_parent;
			}

			if ( $parents ) {
				$parents = array_reverse( $parents );
				foreach ( $parents as $parent_link ) {
					echo $parent_link;
					echo ' <span class="breadcrumb-separator">/</span> ';
				}
			}

			echo '<span class="current">' . esc_html( get_the_title() ) . '</span>';

		// Category archive
		} elseif ( is_category() ) {
			echo '<span class="current">' . single_cat_title( '', false ) . '</span>';

		// Tag archive
		} elseif ( is_tag() ) {
			echo '<span class="current">' . single_tag_title( '', false ) . '</span>';

		// Custom taxonomy archive
		} elseif ( is_tax() ) {
			echo '<span class="current">' . single_term_title( '', false ) . '</span>';

		// Post type archive
		} elseif ( is_post_type_archive() ) {
			echo '<span class="current">' . post_type_archive_title( '', false ) . '</span>';

		// Search results
		} elseif ( is_search() ) {
			echo '<span class="current">' . sprintf( esc_html__( 'Search results for: %s', 'epaton' ), get_search_query() ) . '</span>';

		// 404 page
		} elseif ( is_404() ) {
			echo '<span class="current">' . esc_html__( '404 Not Found', 'epaton' ) . '</span>';

		// Fallback
		} else {
			$title = get_the_title();
			if ( $title ) {
				echo '<span class="current">' . esc_html( $title ) . '</span>';
			}
		}

		echo '</nav>';
	}
}
