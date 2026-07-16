<?php
/**
 * Technical SEO metadata and structured data.
 *
 * @package epaton
 */

if ( ! function_exists( 'epaton_seo_plugin_active' ) ) {
	/**
	 * Avoid duplicate metadata when a dedicated SEO plugin is active.
	 *
	 * @return bool
	 */
	function epaton_seo_plugin_active() {
		return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || class_exists( 'SEOPress' );
	}
}

if ( ! function_exists( 'epaton_get_meta_description' ) ) {
	/**
	 * Build a concise page description from native WordPress content.
	 *
	 * @return string
	 */
	function epaton_get_meta_description() {
		if ( is_singular() ) {
			$post = get_queried_object();
			$text = has_excerpt( $post ) ? $post->post_excerpt : $post->post_content;

			if ( ! trim( wp_strip_all_tags( (string) $text ) ) && function_exists( 'epaton_get_page_layouts' ) ) {
				$first = epaton_get_page_layouts()[0] ?? [];
				$text  = $first['hero_description'] ?? $first['inner_hero_description'] ?? '';
			}
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$text = term_description();
		} elseif ( is_post_type_archive() ) {
			$text = get_the_archive_description();
		} elseif ( is_home() && function_exists( 'get_field' ) ) {
			$text = get_field( 'blog_options_description', 'options' );
		} else {
			$text = get_bloginfo( 'description' );
		}

		$text = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( strip_shortcodes( (string) $text ) ) ) );

		if ( ! $text && ! is_404() ) {
			$text = sprintf(
				/* translators: 1: page title, 2: site name. */
				__( 'Learn more about %1$s at %2$s.', 'epaton' ),
				wp_get_document_title(),
				get_bloginfo( 'name' )
			);
		}

		return wp_html_excerpt( $text, 160, '…' );
	}
}

if ( ! function_exists( 'epaton_get_social_image' ) ) {
	/**
	 * Get the best available social preview image.
	 *
	 * @return string
	 */
	function epaton_get_social_image() {
		if ( is_singular() && has_post_thumbnail() ) {
			return (string) wp_get_attachment_image_url( get_post_thumbnail_id(), 'full' );
		}

		$preload = function_exists( 'epaton_get_hero_preload' ) ? epaton_get_hero_preload() : false;

		return $preload['src'] ?? '';
	}
}

/**
 * Output canonical and social metadata when no SEO plugin owns the head.
 */
function epaton_output_seo_meta() {
	if ( epaton_seo_plugin_active() || is_404() ) {
		return;
	}

	$title       = wp_get_document_title();
	$description = epaton_get_meta_description();
	global $wp;

	$url = is_singular()
		? wp_get_canonical_url()
		: home_url( user_trailingslashit( ltrim( (string) $wp->request, '/' ) ) );

	if ( is_search() ) {
		$url = home_url( '/' );
	}
	$image       = epaton_get_social_image();
	$type        = is_singular( 'post' ) ? 'article' : 'website';

	if ( $description ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	}

	if ( $url ) {
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
	}

	printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( get_locale() ) );
	printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $type ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );

	if ( $description ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
	}

	if ( $image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
	}

	printf( '<meta name="twitter:card" content="%s">' . "\n", $image ? 'summary_large_image' : 'summary' );
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );

	if ( $description ) {
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $description ) );
	}

	if ( $image ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );
	}
}
add_action( 'wp_head', 'epaton_output_seo_meta', 5 );

/**
 * Core outputs singular canonicals; the theme owns canonicals when no SEO plugin does.
 */
function epaton_manage_core_canonical() {
	if ( ! epaton_seo_plugin_active() ) {
		remove_action( 'wp_head', 'rel_canonical' );
	}
}
add_action( 'wp', 'epaton_manage_core_canonical' );

/**
 * Mark search and error responses as non-indexable.
 *
 * @param array $robots Robots directives.
 * @return array
 */
function epaton_robots_directives( $robots ) {
	if ( is_search() || is_404() ) {
		$robots['noindex'] = true;
	}

	return $robots;
}
add_filter( 'wp_robots', 'epaton_robots_directives' );

/**
 * Output Organization, WebSite, Article, and breadcrumb schema as one graph.
 */
function epaton_output_schema() {
	if ( epaton_seo_plugin_active() ) {
		return;
	}

	$home_url = home_url( '/' );
	$graph     = [
		[
			'@type' => 'Organization',
			'@id'   => $home_url . '#organization',
			'name'  => get_bloginfo( 'name' ),
			'url'   => $home_url,
			'email' => function_exists( 'epaton_get_footer_email' ) ? epaton_get_footer_email() : '',
			'telephone' => function_exists( 'epaton_get_footer_phone' ) ? epaton_get_footer_phone() : '',
		],
		[
			'@type' => 'WebSite',
			'@id'   => $home_url . '#website',
			'url'   => $home_url,
			'name'  => get_bloginfo( 'name' ),
			'publisher' => [ '@id' => $home_url . '#organization' ],
			'potentialAction' => [
				'@type'       => 'SearchAction',
				'target'      => home_url( '/?s={search_term_string}' ),
				'query-input' => 'required name=search_term_string',
			],
		],
	];

	$logo = function_exists( 'epaton_get_site_logo' ) ? epaton_get_site_logo() : false;
	if ( is_array( $logo ) && ! empty( $logo['url'] ) ) {
		$graph[0]['logo'] = [ '@type' => 'ImageObject', 'url' => $logo['url'] ];
	}

	$social_links = function_exists( 'epaton_get_social_links' ) ? epaton_get_social_links() : [];
	$same_as      = [];
	foreach ( $social_links as $social_link ) {
		$url = $social_link['link']['url'] ?? '';
		if ( $url && wp_http_validate_url( $url ) ) {
			$same_as[] = $url;
		}
	}
	if ( $same_as ) {
		$graph[0]['sameAs'] = $same_as;
	}

	if ( is_singular( 'post' ) ) {
		$graph[] = [
			'@type'         => 'Article',
			'@id'           => get_permalink() . '#article',
			'headline'      => get_the_title(),
			'description'   => epaton_get_meta_description(),
			'datePublished' => get_the_date( DATE_W3C ),
			'dateModified'  => get_the_modified_date( DATE_W3C ),
			'mainEntityOfPage' => get_permalink(),
			'publisher'      => [ '@id' => $home_url . '#organization' ],
			'image'          => epaton_get_social_image(),
		];
	}

	if ( is_singular() && ! is_front_page() ) {
		$items = [
			[
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => __( 'Home', 'epaton' ),
				'item'     => $home_url,
			],
		];
		$ancestors = array_reverse( get_post_ancestors( get_queried_object_id() ) );
		foreach ( $ancestors as $ancestor_id ) {
			$items[] = [
				'@type'    => 'ListItem',
				'position' => count( $items ) + 1,
				'name'     => get_the_title( $ancestor_id ),
				'item'     => get_permalink( $ancestor_id ),
			];
		}
		$items[] = [
			'@type'    => 'ListItem',
			'position' => count( $items ) + 1,
			'name'     => get_the_title(),
			'item'     => get_permalink(),
		];
		$graph[] = [
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $items,
		];
	}

	$schema = [
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	];

	printf( '<script type="application/ld+json">%s</script>' . "\n", wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
}
add_action( 'wp_head', 'epaton_output_schema', 20 );
