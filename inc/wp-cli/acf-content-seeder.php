<?php
/**
 * WP-CLI ACF content seeding commands.
 *
 * @package epaton
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Seed ACF flexible content for key pages.
 */
class Epaton_ACF_Content_Seeder {
	/**
	 * Seed global site settings used by the header and footer.
	 *
	 * ## EXAMPLES
	 *
	 *     wp epaton seed-site-settings
	 *
	 * @param array $args Positional args.
	 * @param array $assoc_args Associative args.
	 * @return void
	 */
	public function seed_site_settings( $args, $assoc_args ) {
		if ( ! function_exists( 'update_field' ) ) {
			WP_CLI::error( 'ACF is not active. Activate ACF Pro before running the seeder.' );
		}

		$logo_id = $this->get_or_import_seed_image(
			'/Users/mosharafmanu/Desktop/epaton-assets/Epaton Logo.svg',
			0,
			'site_logo_epaton',
			'Epaton Logo',
			'Epaton'
		);

		update_field( 'field_site_logo', $logo_id, 'options' );
		update_field(
			'field_header_button',
			[
				'title'  => 'Contact Epaton',
				'url'    => home_url( '/contact/' ),
				'target' => '',
			],
			'options'
		);
		update_field( 'field_footer_company_text', "Epaton Limited is a company\nregistered in England and Wales.\nCompany Number 08952280.\nVAT Number 185125409.", 'options' );
		update_field( 'field_footer_email', 'sales@epaton.co.uk', 'options' );
		update_field( 'field_footer_phone', '+44 (0)3333 111 001', 'options' );
		update_field( 'field_social_links', $this->get_seeded_social_links(), 'options' );
		update_field(
			'field_footer_offices',
			[
				[ 'city' => 'London', 'address' => '2-7 Clerkenwell Green, London, EC1R 0DE' ],
				[ 'city' => 'Leeds', 'address' => 'Fountain House 4, South Parade, LS1 5QX' ],
				[ 'city' => 'Edinburgh', 'address' => 'Third Floor, 3 Hill Street, EH2 3JP' ],
				[ 'city' => 'Birmingham', 'address' => 'Office 1, Izabella House, 24-26 Regent Place, B1 3NJ' ],
				[ 'city' => 'Manchester', 'address' => 'First Floor, Swan Buildings, 20 Swan Street, M4 5JW' ],
			],
			'options'
		);
		update_field( 'field_footer_copyright', '©Epaton - {year}. All rights reserved', 'options' );
		update_field( 'field_footer_credit_text', 'Website by', 'options' );

		$this->seed_footer_legal_menu();

		WP_CLI::success( 'Seeded site settings for header and footer.' );
	}

	/**
	 * Seed and assign the footer legal menu.
	 *
	 * @return void
	 */
	private function seed_footer_legal_menu() {
		$menu_name = 'Footer Legal Menu';
		$menu      = wp_get_nav_menu_object( $menu_name );

		if ( ! $menu ) {
			$menu_id = wp_create_nav_menu( $menu_name );
		} else {
			$menu_id = (int) $menu->term_id;
		}

		if ( is_wp_error( $menu_id ) || ! $menu_id ) {
			WP_CLI::warning( 'Could not create or find the Footer Legal Menu.' );
			return;
		}

		$existing_items = wp_get_nav_menu_items( $menu_id );

		if ( is_array( $existing_items ) ) {
			foreach ( $existing_items as $item ) {
				wp_delete_post( $item->ID, true );
			}
		}

		$links = [
			[
				'title' => 'Terms & Conditions',
				'url'   => home_url( '/terms-conditions/' ),
			],
			[
				'title' => 'Privacy Policy',
				'url'   => home_url( '/privacy-policy/' ),
			],
		];

		foreach ( $links as $link ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				[
					'menu-item-title'  => $link['title'],
					'menu-item-url'    => $link['url'],
					'menu-item-status' => 'publish',
					'menu-item-type'   => 'custom',
				]
			);
		}

		$locations                     = get_theme_mod( 'nav_menu_locations', [] );
		$locations['footerLegalMenu']  = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	/**
	 * Seed the Home page hero section.
	 *
	 * ## OPTIONS
	 *
	 * [--page_id=<id>]
	 * : Page ID to seed. Defaults to the page with slug "home".
	 *
	 * ## EXAMPLES
	 *
	 *     wp epaton seed-hero
	 *     wp epaton seed-hero --page_id=7
	 *
	 * @param array $args Positional args.
	 * @param array $assoc_args Associative args.
	 * @return void
	 */
	public function seed_hero( $args, $assoc_args ) {
		if ( ! function_exists( 'update_field' ) ) {
			WP_CLI::error( 'ACF is not active. Activate ACF Pro before running the seeder.' );
		}

		$page_id = isset( $assoc_args['page_id'] ) ? absint( $assoc_args['page_id'] ) : 0;

		if ( ! $page_id ) {
			$page = get_page_by_path( 'home', OBJECT, 'page' );
			if ( ! $page ) {
				WP_CLI::error( 'Could not find a page with slug "home". Pass --page_id=<id>.' );
			}

			$page_id = (int) $page->ID;
		}

		$image_id = $this->get_or_import_hero_image( $page_id );

		$hero_layout = [
			'acf_fc_layout'    => 'hero_section',
			'hero_title'       => "MORE. FOR\nLESS. BETTER.",
			'hero_description' => "Our focus is simple: deliver better outcomes for our customers,\nat a lower total cost of ownership.",
			'hero_buttons'     => [
				[
					'button_link'  => [
						'title'  => 'Our Services',
						'url'    => home_url( '/services/' ),
						'target' => '',
					],
					'button_style' => 'primary-gradient',
				],
				[
					'button_link'  => [
						'title'  => 'Contact Epaton',
						'url'    => home_url( '/contact/' ),
						'target' => '',
					],
					'button_style' => 'transparent-primary',
				],
			],
			'hero_media_type'  => 'image',
			'hero_image'       => $image_id,
			'hero_video'       => [],
		];

		update_field( 'field_cms', [ $hero_layout ], $page_id );

		WP_CLI::success( sprintf( 'Seeded Home hero content on page ID %d.', $page_id ) );
	}

	/**
	 * Seed service posts and featured images.
	 *
	 * ## OPTIONS
	 *
	 * [--assets_dir=<path>]
	 * : Directory containing service images. Defaults to /Users/mosharafmanu/Desktop/epaton-assets/services.
	 *
	 * ## EXAMPLES
	 *
	 *     wp epaton seed-services
	 *     wp epaton seed-services --assets_dir=/path/to/services
	 *
	 * @param array $args Positional args.
	 * @param array $assoc_args Associative args.
	 * @return void
	 */
	public function seed_services( $args, $assoc_args ) {
		if ( ! post_type_exists( 'service' ) ) {
			WP_CLI::error( 'The "service" post type is not registered. Make sure ACF Pro is active and the Services ACF post type JSON is loaded.' );
		}

		$assets_dir = isset( $assoc_args['assets_dir'] ) ? rtrim( (string) $assoc_args['assets_dir'], '/' ) : '/Users/mosharafmanu/Desktop/epaton-assets/services';

		if ( ! is_dir( $assets_dir ) ) {
			WP_CLI::error( sprintf( 'Service assets directory not found: %s', $assets_dir ) );
		}

		$services = $this->get_services_seed_data();
		$created  = 0;
		$updated  = 0;

		foreach ( $services as $index => $service ) {
			$slug        = sanitize_title( $service['title'] );
			$existing_id = $this->get_post_id_by_slug( $slug, 'service' );

			$post_data = [
				'post_type'    => 'service',
				'post_status'  => 'publish',
				'post_title'   => $service['title'],
				'post_name'    => $slug,
				'post_excerpt' => $service['excerpt'],
				'post_content' => $service['excerpt'],
				'menu_order'   => $index,
			];

			if ( $existing_id ) {
				$post_data['ID'] = $existing_id;
				$post_id         = wp_update_post( $post_data, true );
				$updated++;
			} else {
				$post_id = wp_insert_post( $post_data, true );
				$created++;
			}

			if ( is_wp_error( $post_id ) ) {
				WP_CLI::error( $post_id->get_error_message() );
			}

			$image_path    = $assets_dir . '/' . $service['image'];
			$attachment_id = $this->get_or_import_seed_image(
				$image_path,
				(int) $post_id,
				'service_' . $service['image_key'],
				$service['title'],
				$service['title']
			);

			set_post_thumbnail( (int) $post_id, $attachment_id );
		}

		WP_CLI::success( sprintf( 'Seeded services. Created: %d. Updated: %d.', $created, $updated ) );
	}

	/**
	 * Seed product posts and featured images.
	 *
	 * ## OPTIONS
	 *
	 * [--assets_dir=<path>]
	 * : Directory containing product images. Defaults to /Users/mosharafmanu/Desktop/epaton-assets/products.
	 *
	 * ## EXAMPLES
	 *
	 *     wp epaton seed-products
	 *     wp epaton seed-products --assets_dir=/path/to/products
	 *
	 * @param array $args Positional args.
	 * @param array $assoc_args Associative args.
	 * @return void
	 */
	public function seed_products( $args, $assoc_args ) {
		if ( ! post_type_exists( 'product' ) ) {
			WP_CLI::error( 'The "product" post type is not registered. Sync the Products ACF post type JSON, then rerun this command.' );
		}

		$assets_dir = isset( $assoc_args['assets_dir'] ) ? rtrim( (string) $assoc_args['assets_dir'], '/' ) : '/Users/mosharafmanu/Desktop/epaton-assets/products';

		if ( ! is_dir( $assets_dir ) ) {
			WP_CLI::error( sprintf( 'Product assets directory not found: %s', $assets_dir ) );
		}

		$products = $this->get_products_seed_data();
		$created  = 0;
		$updated  = 0;

		foreach ( $products as $index => $product ) {
			$slug        = sanitize_title( $product['title'] );
			$existing_id = $this->get_post_id_by_slug( $slug, 'product' );

			$post_data = [
				'post_type'    => 'product',
				'post_status'  => 'publish',
				'post_title'   => $product['title'],
				'post_name'    => $slug,
				'post_excerpt' => $product['excerpt'],
				'post_content' => $product['excerpt'],
				'menu_order'   => $index,
			];

			if ( $existing_id ) {
				$post_data['ID'] = $existing_id;
				$post_id         = wp_update_post( $post_data, true );
				$updated++;
			} else {
				$post_id = wp_insert_post( $post_data, true );
				$created++;
			}

			if ( is_wp_error( $post_id ) ) {
				WP_CLI::error( $post_id->get_error_message() );
			}

			$image_path    = $assets_dir . '/' . $product['image'];
			$attachment_id = $this->get_or_import_seed_image(
				$image_path,
				(int) $post_id,
				'product_' . $product['image_key'],
				$product['title'],
				$product['title']
			);

			set_post_thumbnail( (int) $post_id, $attachment_id );
		}

		WP_CLI::success( sprintf( 'Seeded products. Created: %d. Updated: %d.', $created, $updated ) );
	}

	/**
	 * Seed blog/news posts and featured images.
	 *
	 * ## OPTIONS
	 *
	 * [--image_path=<path>]
	 * : Featured image path. Defaults to /Users/mosharafmanu/Desktop/epaton-assets/blog-image.jpg.
	 *
	 * ## EXAMPLES
	 *
	 *     wp epaton seed-blog-posts
	 *     wp epaton seed-blog-posts --image_path=/path/to/blog-image.jpg
	 *
	 * @param array $args Positional args.
	 * @param array $assoc_args Associative args.
	 * @return void
	 */
	public function seed_blog_posts( $args, $assoc_args ) {
		$image_path = isset( $assoc_args['image_path'] ) ? (string) $assoc_args['image_path'] : '/Users/mosharafmanu/Desktop/epaton-assets/blog-image.jpg';

		$category = term_exists( 'Case Study', 'category' );
		if ( ! $category ) {
			$category = wp_insert_term( 'Case Study', 'category', [ 'slug' => 'case-study' ] );
		}

		if ( is_wp_error( $category ) ) {
			WP_CLI::error( $category->get_error_message() );
		}

		$category_id = is_array( $category ) ? (int) $category['term_id'] : (int) $category;
		$posts       = $this->get_blog_posts_seed_data();
		$created     = 0;
		$updated     = 0;

		foreach ( $posts as $index => $post ) {
			$slug        = $post['slug'];
			$existing_id = $this->get_post_id_by_slug( $slug, 'post' );

			$post_data = [
				'post_type'     => 'post',
				'post_status'   => 'publish',
				'post_title'    => $post['title'],
				'post_name'     => $slug,
				'post_excerpt'  => $post['excerpt'],
				'post_content'  => $post['content'],
				'post_category' => [ $category_id ],
				'post_date'     => gmdate( 'Y-m-d H:i:s', strtotime( sprintf( '2026-05-%02d 09:00:00', 24 - $index ) ) ),
			];

			if ( $existing_id ) {
				$post_data['ID'] = $existing_id;
				$post_id         = wp_update_post( $post_data, true );
				$updated++;
			} else {
				$post_id = wp_insert_post( $post_data, true );
				$created++;
			}

			if ( is_wp_error( $post_id ) ) {
				WP_CLI::error( $post_id->get_error_message() );
			}

			wp_set_post_terms( (int) $post_id, [ $category_id ], 'category', false );

			$attachment_id = $this->get_or_import_seed_image(
				$image_path,
				(int) $post_id,
				'blog_case_study_featured_image',
				'Case Study Featured Image',
				'Business team reviewing reports and charts'
			);

			set_post_thumbnail( (int) $post_id, $attachment_id );
		}

		update_option( 'posts_per_page', 12 );

		WP_CLI::success( sprintf( 'Seeded blog posts. Created: %d. Updated: %d.', $created, $updated ) );
	}

	/**
	 * Seed the Contact Form 7 enquiry form.
	 *
	 * ## OPTIONS
	 *
	 * [--form_id=<id>]
	 * : Contact Form 7 post ID to update. Defaults to 535.
	 *
	 * ## EXAMPLES
	 *
	 *     wp epaton seed-contact-form
	 *     wp epaton seed-contact-form --form_id=535
	 *
	 * @param array $args Positional args.
	 * @param array $assoc_args Associative args.
	 * @return void
	 */
	public function seed_contact_form( $args, $assoc_args ) {
		if ( ! post_type_exists( 'wpcf7_contact_form' ) ) {
			WP_CLI::error( 'Contact Form 7 is not active. Activate Contact Form 7 before running this seeder.' );
		}

		$form_id  = isset( $assoc_args['form_id'] ) ? absint( $assoc_args['form_id'] ) : 535;
		$form     = $form_id ? get_post( $form_id ) : null;
		$form_key = 'epaton-contact-enquiry';

		if ( ! $form || 'wpcf7_contact_form' !== $form->post_type ) {
			$existing_id = $this->get_post_id_by_slug( $form_key, 'wpcf7_contact_form' );
			if ( $existing_id ) {
				$form_id = $existing_id;
			}
		}

		$post_data = [
			'post_type'    => 'wpcf7_contact_form',
			'post_status'  => 'publish',
			'post_title'   => 'Epaton Contact Enquiry',
			'post_name'    => $form_key,
			'post_content' => $this->get_contact_form_7_markup(),
		];

		if ( $form_id && get_post( $form_id ) && 'wpcf7_contact_form' === get_post_type( $form_id ) ) {
			$post_data['ID'] = $form_id;
			$saved_id        = wp_update_post( $post_data, true );
		} else {
			$saved_id = wp_insert_post( $post_data, true );
		}

		if ( is_wp_error( $saved_id ) ) {
			WP_CLI::error( $saved_id->get_error_message() );
		}

		update_post_meta( (int) $saved_id, '_form', $this->get_contact_form_7_markup() );
		update_post_meta( (int) $saved_id, '_mail', $this->get_contact_form_7_mail_settings() );
		update_post_meta( (int) $saved_id, '_mail_2', [ 'active' => false ] );
		update_post_meta( (int) $saved_id, '_messages', [] );
		update_post_meta( (int) $saved_id, '_additional_settings', '' );
		update_post_meta( (int) $saved_id, '_locale', 'en_US' );

		WP_CLI::success( sprintf( 'Seeded Contact Form 7 form ID %d.', (int) $saved_id ) );
	}

	/**
	 * Seed the Contact page flexible content section.
	 *
	 * ## OPTIONS
	 *
	 * [--page_id=<id>]
	 * : Page ID to seed. Defaults to 536.
	 *
	 * [--form_id=<id>]
	 * : Contact Form 7 form ID. Defaults to 535.
	 *
	 * ## EXAMPLES
	 *
	 *     wp epaton seed-contact-page --page_id=536
	 *
	 * @param array $args Positional args.
	 * @param array $assoc_args Associative args.
	 * @return void
	 */
	public function seed_contact_page( $args, $assoc_args ) {
		if ( ! function_exists( 'update_field' ) ) {
			WP_CLI::error( 'ACF is not active. Activate ACF Pro before running the seeder.' );
		}

		$page_id = isset( $assoc_args['page_id'] ) ? absint( $assoc_args['page_id'] ) : 536;
		$form_id = isset( $assoc_args['form_id'] ) ? absint( $assoc_args['form_id'] ) : 535;

		if ( ! get_post( $page_id ) ) {
			WP_CLI::error( sprintf( 'Could not find page ID %d.', $page_id ) );
		}

		$layouts = get_field( 'cms', $page_id );

		if ( ! is_array( $layouts ) ) {
			$layouts = [];
		}

		$contact_layout = [
			'acf_fc_layout'                  => 'contact_panel',
			'contact_panel_section_title'    => 'CONTACT US',
			'contact_panel_title'            => 'Let’s Talk',
			'contact_panel_form_shortcode'   => sprintf( '[contact-form-7 id="%d"]', $form_id ),
		];

		$layouts = $this->upsert_flexible_layout( $layouts, 'contact_panel', $contact_layout );

		update_field( 'field_cms', $layouts, $page_id );

		WP_CLI::success( sprintf( 'Seeded Contact panel on page ID %d.', $page_id ) );
	}

	/**
	 * Seed the Core Areas flexible content section.
	 *
	 * ## OPTIONS
	 *
	 * [--page_id=<id>]
	 * : Page ID to seed. Defaults to the page with slug "home".
	 *
	 * ## EXAMPLES
	 *
	 *     wp epaton seed-core-areas
	 *     wp epaton seed-core-areas --page_id=7
	 *
	 * @param array $args Positional args.
	 * @param array $assoc_args Associative args.
	 * @return void
	 */
	public function seed_core_areas( $args, $assoc_args ) {
		if ( ! function_exists( 'update_field' ) ) {
			WP_CLI::error( 'ACF is not active. Activate ACF Pro before running the seeder.' );
		}

		$page_id = $this->get_seed_page_id( $assoc_args );
		$layouts = get_field( 'cms', $page_id );

		if ( ! is_array( $layouts ) ) {
			$layouts = [];
		}

		$core_areas_layout = [
			'acf_fc_layout'        => 'core_areas',
			'core_areas_eyebrow'   => 'What We Do',
			'core_areas_heading'   => 'We specialise in three core areas:',
			'core_areas_items'     => $this->get_core_areas_seed_data(),
		];

		$layouts = $this->upsert_flexible_layout( $layouts, 'core_areas', $core_areas_layout );

		update_field( 'field_cms', $layouts, $page_id );

		WP_CLI::success( sprintf( 'Seeded Core Areas section on page ID %d.', $page_id ) );
	}

	/**
	 * Seed the Approach Panels flexible content section.
	 *
	 * ## OPTIONS
	 *
	 * [--page_id=<id>]
	 * : Page ID to seed. Defaults to the page with slug "home".
	 *
	 * ## EXAMPLES
	 *
	 *     wp epaton seed-approach-panels
	 *     wp epaton seed-approach-panels --page_id=7
	 *
	 * @param array $args Positional args.
	 * @param array $assoc_args Associative args.
	 * @return void
	 */
	public function seed_approach_panels( $args, $assoc_args ) {
		if ( ! function_exists( 'update_field' ) ) {
			WP_CLI::error( 'ACF is not active. Activate ACF Pro before running the seeder.' );
		}

		$page_id = $this->get_seed_page_id( $assoc_args );
		$layouts = get_field( 'cms', $page_id );

		if ( ! is_array( $layouts ) ) {
			$layouts = [];
		}

		$approach_layout = [
			'acf_fc_layout'               => 'approach_panels',
			'approach_panels_eyebrow'     => 'OUR APPROACH',
			'approach_panels_title'       => 'We take time to assess your existing environment, future requirements, and operational challenges before recommending any solution.',
			'approach_panels_intro_label' => 'Our approach is:',
			'approach_panels_bullets'     => [
				[ 'text' => 'Consultative, not sales-driven' ],
				[ 'text' => 'Evidence-based, not assumption-led' ],
				[ 'text' => 'Outcome-focused, not product-focused' ],
			],
			'approach_panels_statement'   => 'We believe strong data infrastructure should enable your organisation - not restrict it.',
			'approach_panels_cards'       => $this->get_approach_panels_seed_data(),
		];

		$layouts = $this->upsert_flexible_layout( $layouts, 'approach_panels', $approach_layout );

		update_field( 'field_cms', $layouts, $page_id );

		WP_CLI::success( sprintf( 'Seeded Approach Panels section on page ID %d.', $page_id ) );
	}

	/**
	 * Seed the Commitment Panel flexible content section.
	 *
	 * ## OPTIONS
	 *
	 * [--page_id=<id>]
	 * : Page ID to seed. Defaults to the page with slug "home".
	 *
	 * ## EXAMPLES
	 *
	 *     wp epaton seed-commitment-panel
	 *     wp epaton seed-commitment-panel --page_id=7
	 *
	 * @param array $args Positional args.
	 * @param array $assoc_args Associative args.
	 * @return void
	 */
	public function seed_commitment_panel( $args, $assoc_args ) {
		if ( ! function_exists( 'update_field' ) ) {
			WP_CLI::error( 'ACF is not active. Activate ACF Pro before running the seeder.' );
		}

		$page_id = $this->get_seed_page_id( $assoc_args );
		$layouts = get_field( 'cms', $page_id );

		if ( ! is_array( $layouts ) ) {
			$layouts = [];
		}

		$commitment_layout = [
			'acf_fc_layout'                 => 'commitment_panel',
			'commitment_panel_eyebrow'      => 'OUR COMMITMENT',
			'commitment_panel_title'        => 'We are committed to delivering:',
			'commitment_panel_intro_label'  => 'We are committed to delivering:',
			'commitment_panel_bullets'      => [
				[ 'text' => 'Resilient, secure, immutable data platforms' ],
				[ 'text' => 'Transparent commercial value' ],
				[ 'text' => 'High-quality service and support' ],
				[ 'text' => 'Long-term customer relationships' ],
				[ 'text' => 'Continuous optimisation' ],
			],
			'commitment_panel_statement'    => 'We believe strong data infrastructure should enable your organisation - not restrict it.',
		];

		$layouts = $this->upsert_flexible_layout_after( $layouts, 'commitment_panel', $commitment_layout, 'approach_panels' );

		update_field( 'field_cms', $layouts, $page_id );

		WP_CLI::success( sprintf( 'Seeded Commitment Panel section on page ID %d.', $page_id ) );
	}

	/**
	 * Seed the Looking Forward flexible content section.
	 *
	 * ## OPTIONS
	 *
	 * [--page_id=<id>]
	 * : Page ID to seed. Defaults to the page with slug "home".
	 *
	 * ## EXAMPLES
	 *
	 *     wp epaton seed-looking-forward
	 *     wp epaton seed-looking-forward --page_id=7
	 *
	 * @param array $args Positional args.
	 * @param array $assoc_args Associative args.
	 * @return void
	 */
	public function seed_looking_forward( $args, $assoc_args ) {
		if ( ! function_exists( 'update_field' ) ) {
			WP_CLI::error( 'ACF is not active. Activate ACF Pro before running the seeder.' );
		}

		$page_id = $this->get_seed_page_id( $assoc_args );
		$layouts = get_field( 'cms', $page_id );

		if ( ! is_array( $layouts ) ) {
			$layouts = [];
		}

		$looking_forward_layout = [
			'acf_fc_layout'              => 'looking_forward',
			'looking_forward_eyebrow'    => 'LOOKING FORWARD',
			'looking_forward_statements' => [
				[
					'text' => "As data volumes grow and digital risk increases, the need for robust,\nwell-designed infrastructure has never been greater.",
				],
				[
					'text' => "Epaton continues to invest in skills, partnerships, and innovation to\nensure our clients remain protected, competitive, and cost-effective.",
				],
				[
					'text' => "We are focused on helping organisations prepare for what&rsquo;s next\n- without overpaying for it.",
				],
			],
		];

		$layouts = $this->upsert_flexible_layout_after( $layouts, 'looking_forward', $looking_forward_layout, 'commitment_panel' );

		update_field( 'field_cms', $layouts, $page_id );

		WP_CLI::success( sprintf( 'Seeded Looking Forward section on page ID %d.', $page_id ) );
	}

	/**
	 * Seed the Contact CTA flexible content section.
	 *
	 * ## OPTIONS
	 *
	 * [--page_id=<id>]
	 * : Page ID to seed. Defaults to the page with slug "home".
	 *
	 * ## EXAMPLES
	 *
	 *     wp epaton seed-contact-cta
	 *     wp epaton seed-contact-cta --page_id=7
	 *
	 * @param array $args Positional args.
	 * @param array $assoc_args Associative args.
	 * @return void
	 */
	public function seed_contact_cta( $args, $assoc_args ) {
		if ( ! function_exists( 'update_field' ) ) {
			WP_CLI::error( 'ACF is not active. Activate ACF Pro before running the seeder.' );
		}

		$page_id = $this->get_seed_page_id( $assoc_args );
		$layouts = get_field( 'cms', $page_id );

		if ( ! is_array( $layouts ) ) {
			$layouts = [];
		}

		$contact_cta_layout = [
			'acf_fc_layout'            => 'contact_cta',
			'contact_cta_use_global'   => 1,
			'contact_cta_title'        => 'Let\'s Talk',
			'contact_cta_body'         => "Whether you are reviewing your current platform, planning a major transformation,\nor looking to reduce cost and risk, we'd welcome the opportunity to help.",
			'contact_cta_button_style' => 'cyan',
			'contact_cta_button'       => [
				'title'  => 'Speak to our team today to start the conversation.',
				'url'    => home_url( '/contact/' ),
				'target' => '',
			],
		];

		$layouts = $this->upsert_flexible_layout_after( $layouts, 'contact_cta', $contact_cta_layout, 'looking_forward' );

		update_field( 'field_cms', $layouts, $page_id );

		WP_CLI::success( sprintf( 'Seeded Contact CTA section on page ID %d.', $page_id ) );
	}

	/**
	 * Seed the Clients Logos flexible content section.
	 *
	 * ## OPTIONS
	 *
	 * [--page_id=<id>]
	 * : Page ID to seed. Defaults to the page with slug "home".
	 *
	 * [--logos_dir=<path>]
	 * : Directory containing individually exported client logos.
	 *
	 * [--use_remote]
	 * : Import from fresh Figma export URLs in the seeder instead of a local directory.
	 *
	 * ## EXAMPLES
	 *
	 *     wp epaton seed-clients-section --page_id=7
	 *     wp epaton seed-clients-section --logos_dir=/path/to/client-logos
	 *
	 * @param array $args Positional args.
	 * @param array $assoc_args Associative args.
	 * @return void
	 */
	public function seed_clients_section( $args, $assoc_args ) {
		if ( ! function_exists( 'update_field' ) ) {
			WP_CLI::error( 'ACF is not active. Activate ACF Pro before running the seeder.' );
		}

		$page_id = $this->get_seed_page_id( $assoc_args );
		$layouts = get_field( 'cms', $page_id );

		if ( ! is_array( $layouts ) ) {
			$layouts = [];
		}

		$logos_dir  = isset( $assoc_args['logos_dir'] ) ? rtrim( (string) $assoc_args['logos_dir'], '/' ) : '/Users/mosharafmanu/Desktop/epaton-assets/client-logos';
		$use_remote = ! empty( $assoc_args['use_remote'] );
		$logos      = [];

		foreach ( $this->get_client_logos_seed_data() as $logo ) {
			if ( $use_remote ) {
				$logo_id = $this->get_or_import_remote_seed_image( $logo['url'], $page_id, 'client_logo_' . $logo['slug'], $logo['file'], $logo['name'], $logo['name'] );
			} else {
				$logo_path = $logos_dir . '/' . $logo['file'];
				$logo_id   = $this->get_or_import_seed_image( $logo_path, $page_id, 'client_logo_' . $logo['slug'], $logo['name'], $logo['name'] );
			}

			$logos[] = [
				'name' => $logo['name'],
				'logo' => $logo_id,
			];
		}

		$clients_layout = [
			'acf_fc_layout'              => 'clients_logos',
			'clients_logos_eyebrow'      => 'OUR CLIENTS',
			'clients_logos_logos_eyebrow' => 'TRUSTED BY INDUSTRY LEADERS',
			'clients_logos_title'        => "We work with a diverse range\nof organisations, including:",
			'clients_logos_bullets'      => [
				[ 'text' => 'Local and central government' ],
				[ 'text' => 'Education and healthcare providers' ],
				[ 'text' => 'Commercial and industrial enterprises' ],
				[ 'text' => 'Professional services firms' ],
			],
			'clients_logos_body'         => "Many of our clients have worked with us for\nyears, relying on Epaton as a long-term strategic\nbackup partner.",
			'clients_logos_button'       => [
				'title'  => 'Contact Us',
				'url'    => home_url( '/contact/' ),
				'target' => '',
			],
			'clients_logos_items'        => $logos,
		];

		$layouts = $this->upsert_flexible_layout( $layouts, 'clients_logos', $clients_layout );

		update_field( 'field_cms', $layouts, $page_id );

		WP_CLI::success( sprintf( 'Seeded Clients Logos section on page ID %d.', $page_id ) );
	}

	/**
	 * Seed the Partners Listing flexible content section.
	 *
	 * ## OPTIONS
	 *
	 * [--page_id=<id>]
	 * : Page ID to seed. Defaults to the page with slug "home".
	 *
	 * [--logos_dir=<path>]
	 * : Directory containing individually exported partner logos.
	 *
	 * ## EXAMPLES
	 *
	 *     wp epaton seed-partners-listing --page_id=192
	 *     wp epaton seed-partners-listing --page_id=192 --logos_dir=/path/to/partners
	 *
	 * @param array $args Positional args.
	 * @param array $assoc_args Associative args.
	 * @return void
	 */
	public function seed_partners_listing( $args, $assoc_args ) {
		if ( ! function_exists( 'update_field' ) ) {
			WP_CLI::error( 'ACF is not active. Activate ACF Pro before running the seeder.' );
		}

		$page_id = $this->get_seed_page_id( $assoc_args );
		$layouts = get_field( 'cms', $page_id );

		if ( ! is_array( $layouts ) ) {
			$layouts = [];
		}

		$logos_dir = isset( $assoc_args['logos_dir'] ) ? rtrim( (string) $assoc_args['logos_dir'], '/' ) : '/Users/mosharafmanu/Desktop/epaton-assets/partners';
		$partners  = [];

		foreach ( $this->get_partners_listing_seed_data() as $partner ) {
			$logo_id = $this->get_or_import_seed_image(
				$logos_dir . '/' . $partner['file'],
				$page_id,
				'partner_logo_' . $partner['slug'],
				$partner['name'],
				$partner['name']
			);

			$partners[] = [
				'name'        => $partner['name'],
				'description' => $partner['description'],
				'logo'        => $logo_id,
				'theme'       => $partner['theme'],
			];
		}

		$partners_layout = [
			'acf_fc_layout'                 => 'partners_listing',
			'partners_listing_heading'      => '',
			'partners_listing_items'        => $partners,
		];

		$layouts = $this->upsert_flexible_layout( $layouts, 'partners_listing', $partners_layout );

		update_field( 'field_cms', $layouts, $page_id );

		WP_CLI::success( sprintf( 'Seeded Partners Listing section on page ID %d.', $page_id ) );
	}

	/**
	 * Seed the Who We Are page with all flexible content sections.
	 *
	 * ## OPTIONS
	 *
	 * [--page_id=<id>]
	 * : Page ID to seed. Defaults to the page with slug "who-we-are".
	 *
	 * [--assets_dir=<path>]
	 * : Path to who-we-are assets directory.
	 *   Defaults to /Users/mosharafmanu/Desktop/epaton-assets/who-we-are.
	 *
	 * ## EXAMPLES
	 *
	 *     wp epaton seed-who-we-are --page_id=190
	 *
	 * @param array $args Positional args.
	 * @param array $assoc_args Associative args.
	 * @return void
	 */
	public function seed_who_we_are( $args, $assoc_args ) {
		if ( ! function_exists( 'update_field' ) ) {
			WP_CLI::error( 'ACF is not active. Activate ACF Pro before running the seeder.' );
		}

		$page_id = isset( $assoc_args['page_id'] ) ? absint( $assoc_args['page_id'] ) : 0;

		if ( ! $page_id ) {
			$page = get_page_by_path( 'who-we-are', OBJECT, 'page' );
			if ( ! $page ) {
				WP_CLI::error( 'Could not find a page with slug "who-we-are". Pass --page_id=<id>.' );
			}
			$page_id = (int) $page->ID;
		}

		$assets_dir  = isset( $assoc_args['assets_dir'] )
			? rtrim( (string) $assoc_args['assets_dir'], '/' )
			: '/Users/mosharafmanu/Desktop/epaton-assets/who-we-are';
		$media_dir   = $assets_dir . '/meda-content-5050';

		// Import images
		$hero_img = $this->get_or_import_seed_image(
			$assets_dir . '/inner-hero.jpg',
			$page_id, 'who_we_are_hero_bg',
			'Who We Are Hero Background', 'Vendor Independent Specialist in Next-Generation Storage'
		);

		$img1 = $this->get_or_import_seed_image(
			$media_dir . '/01.jpg', $page_id,
			'who_we_are_mc5050_01', 'Storage specialist at laptop', 'Specialist storage reseller'
		);
		$img2 = $this->get_or_import_seed_image(
			$media_dir . '/02.jpg', $page_id,
			'who_we_are_mc5050_02', 'Team bespoke solutions', 'Bespoke solutions team'
		);
		$img3 = $this->get_or_import_seed_image(
			$media_dir . '/03.jpg', $page_id,
			'who_we_are_mc5050_03', 'Measurable value delivery', 'Significant and measurable value'
		);
		$img4 = $this->get_or_import_seed_image(
			$media_dir . '/04.jpg', $page_id,
			'who_we_are_mc5050_04', 'Proven delivery methods', 'Powerful delivery methods'
		);

		$layouts = [

			// 1. Inner Hero
			[
				'acf_fc_layout'          => 'inner_hero',
				'inner_hero_eyebrow'     => 'WHO WE ARE',
				'inner_hero_title'       => 'WE ARE A VENDOR INDEPENDENT SPECIALIST IN NEXT-GENERATION STORAGE & BACKUP TECHNOLOGIES.',
				'inner_hero_description' => '',
				'inner_hero_buttons'     => [],
				'inner_hero_media_type'  => 'image',
				'inner_hero_image'       => $hero_img,
				'inner_hero_video'       => [],
			],

			// 2. Media 50/50 — content left, image right
			[
				'acf_fc_layout'       => 'media_content_5050',
				'mc5050_eyebrow'      => 'SPECIALIST STORAGE RESELLER',
				'mc5050_title'        => 'We are a Vendor Independent Specialist in Next-Generation Storage & Backup Technologies.',
				'mc5050_body'         => '<p>Eight-time winner of Specialist Storage Reseller of the Year at the Storage Awards, Epaton is the re-introduction of a fundamental missing component in the IT marketplace; a vendor independent specialist in Next Generation Storage, Backup, Hyper-Converged and Hybrid Cloud solutions encompassing all available strategies demonstrated by vendors today.</p>',
				'mc5050_button'       => [],
				'mc5050_media_position' => 'right',
				'mc5050_media_type'   => 'image',
				'mc5050_image'        => $img1,
				'mc5050_video'        => [],
			],

			// 3. Media 50/50 — image left, content right
			[
				'acf_fc_layout'       => 'media_content_5050',
				'mc5050_eyebrow'      => 'BESPOKE SOLUTIONS',
				'mc5050_title'        => 'We align our Bespoke Solutions with the People and Culture of Your Business.',
				'mc5050_body'         => '<p>As one of the industries few independent providers, Epaton is supported by an experienced, passionate, and dedicated team who believe in delivering bespoke, robust solutions to fit the individual requirements unique to your business.</p><p>Epaton focus on aligning solutions with the people and culture of your business as we believe this is critical to your and our success.</p>',
				'mc5050_button'       => [],
				'mc5050_media_position' => 'left',
				'mc5050_media_type'   => 'image',
				'mc5050_image'        => $img2,
				'mc5050_video'        => [],
			],

			// 4. Media 50/50 — content left, image right
			[
				'acf_fc_layout'       => 'media_content_5050',
				'mc5050_eyebrow'      => 'SIGNIFICANT AND MEASURABLE VALUE',
				'mc5050_title'        => 'We deliver Significant and Measurable Value with Every Element of work undertaken.',
				'mc5050_body'         => '<p>Delivery of solutions and strategies is often a multi-year program, involving organisational change, process re-engineering and numerous technology components.</p><p>Using our broad experience together with our unrivalled delivery capabilities, we\'ll help your business develop a strategic architectural blueprint, a business case, and a clear roadmap for all your solution requirements.</p>',
				'mc5050_button'       => [],
				'mc5050_media_position' => 'right',
				'mc5050_media_type'   => 'image',
				'mc5050_image'        => $img3,
				'mc5050_video'        => [],
			],

			// 5. Media 50/50 — image left, content right
			[
				'acf_fc_layout'       => 'media_content_5050',
				'mc5050_eyebrow'      => 'POWERFUL AND WELL PROVEN DELIVERY METHODS',
				'mc5050_title'        => 'We promise Powerful and Well-Proven delivery methods with Every Solution.',
				'mc5050_body'         => '<p>Epaton uses proven methodologies, tools, and frameworks to provide structure whilst tailoring a custom solution to meet the specific needs of each customer.</p><p>We provide a unique mix of comprehensive skills coupled with powerful and well-proven delivery methods to ensure on time, on budget and fully documented solutions.</p>',
				'mc5050_button'       => [],
				'mc5050_media_position' => 'left',
				'mc5050_media_type'   => 'image',
				'mc5050_image'        => $img4,
				'mc5050_video'        => [],
			],

			// 6. Contact CTA
			[
				'acf_fc_layout'            => 'contact_cta',
				'contact_cta_use_global'   => 1,
				'contact_cta_title'        => "Let's Talk",
				'contact_cta_body'         => "Whether you are reviewing your current platform, planning a major transformation,\nor looking to reduce cost and risk, we'd welcome the opportunity to help.",
				'contact_cta_button_style' => 'cyan',
				'contact_cta_button'       => [
					'title'  => 'Speak to our team today to start the conversation.',
					'url'    => home_url( '/contact/' ),
					'target' => '',
				],
			],
		];

		update_field( 'field_cms', $layouts, $page_id );

		WP_CLI::success( sprintf( 'Seeded Who We Are page (ID %d) with %d sections.', $page_id, count( $layouts ) ) );
	}

	/**
	 * Seed the Products page Inner Hero flexible content section.
	 *
	 * ## OPTIONS
	 *
	 * [--page_id=<id>]
	 * : Page ID to seed. Defaults to the page with slug "products".
	 *
	 * [--image_path=<path>]
	 * : Path to the hero background image.
	 *   Defaults to /Users/mosharafmanu/Desktop/epaton-assets/inner-hero.jpg.
	 *
	 * ## EXAMPLES
	 *
	 *     wp epaton seed-inner-hero-products
	 *     wp epaton seed-inner-hero-products --page_id=42
	 *
	 * @param array $args Positional args.
	 * @param array $assoc_args Associative args.
	 * @return void
	 */
	public function seed_inner_hero_products( $args, $assoc_args ) {
		if ( ! function_exists( 'update_field' ) ) {
			WP_CLI::error( 'ACF is not active. Activate ACF Pro before running the seeder.' );
		}

		$page_id = isset( $assoc_args['page_id'] ) ? absint( $assoc_args['page_id'] ) : 0;

		if ( ! $page_id ) {
			$page = get_page_by_path( 'products', OBJECT, 'page' );
			if ( ! $page ) {
				WP_CLI::error( 'Could not find a page with slug "products". Pass --page_id=<id>.' );
			}
			$page_id = (int) $page->ID;
		}

		$image_path = isset( $assoc_args['image_path'] )
			? (string) $assoc_args['image_path']
			: '/Users/mosharafmanu/Desktop/epaton-assets/inner-hero.jpg';

		$image_id = $this->get_or_import_seed_image(
			$image_path,
			$page_id,
			'inner_hero_products_bg',
			'Products Hero Background',
			'Storage, Backup, Recovery and Software Defined Solutions'
		);

		$inner_hero_layout = [
			'acf_fc_layout'          => 'inner_hero',
			'inner_hero_eyebrow'     => 'OUR PRODUCTS',
			'inner_hero_title'       => 'STORAGE, BACKUP, RECOVERY AND SOFTWARE DEFINED SOLUTIONS',
			'inner_hero_description' => 'A vendor independent specialist in Next Generation Storage, Backup, Recovery and Software Defined Solutions',
			'inner_hero_buttons'     => [
				[
					'button_link'  => [
						'title'  => 'Contact Epaton',
						'url'    => home_url( '/contact/' ),
						'target' => '',
					],
					'button_style' => 'transparent-primary',
				],
			],
			'inner_hero_media_type'  => 'image',
			'inner_hero_image'       => $image_id,
			'inner_hero_video'       => [],
		];

		$layouts = get_field( 'cms', $page_id );

		if ( ! is_array( $layouts ) ) {
			$layouts = [];
		}

		$layouts = $this->upsert_flexible_layout_at_start( $layouts, 'inner_hero', $inner_hero_layout );

		update_field( 'field_cms', $layouts, $page_id );

		WP_CLI::success( sprintf( 'Seeded Products inner hero on page ID %d.', $page_id ) );
	}

	/**
	 * Seed the Partners page Inner Hero flexible content section.
	 *
	 * ## OPTIONS
	 *
	 * [--page_id=<id>]
	 * : Page ID to seed. Defaults to the page with slug "partners".
	 *
	 * [--image_path=<path>]
	 * : Path to the hero background image.
	 *   Defaults to /Users/mosharafmanu/Desktop/epaton-assets/inner-hero.jpg.
	 *
	 * ## EXAMPLES
	 *
	 *     wp epaton seed-inner-hero-partners
	 *     wp epaton seed-inner-hero-partners --page_id=192
	 *
	 * @param array $args Positional args.
	 * @param array $assoc_args Associative args.
	 * @return void
	 */
	public function seed_inner_hero_partners( $args, $assoc_args ) {
		if ( ! function_exists( 'update_field' ) ) {
			WP_CLI::error( 'ACF is not active. Activate ACF Pro before running the seeder.' );
		}

		$page_id = isset( $assoc_args['page_id'] ) ? absint( $assoc_args['page_id'] ) : 0;

		if ( ! $page_id ) {
			$page = get_page_by_path( 'partners', OBJECT, 'page' );
			if ( ! $page ) {
				WP_CLI::error( 'Could not find a page with slug "partners". Pass --page_id=<id>.' );
			}
			$page_id = (int) $page->ID;
		}

		$image_path = isset( $assoc_args['image_path'] )
			? (string) $assoc_args['image_path']
			: '/Users/mosharafmanu/Desktop/epaton-assets/inner-hero.jpg';

		$image_id = $this->get_or_import_seed_image(
			$image_path,
			$page_id,
			'inner_hero_partners_bg',
			'Partners Hero Background',
			'Cloud infrastructure partner hero background'
		);

		$inner_hero_layout = [
			'acf_fc_layout'          => 'inner_hero',
			'inner_hero_eyebrow'     => 'OUR PARTNERS',
			'inner_hero_title'       => 'OUR PARTNERS',
			'inner_hero_description' => 'We build strong, long-lasting relationships with our partners.',
			'inner_hero_buttons'     => [],
			'inner_hero_media_type'  => 'image',
			'inner_hero_image'       => $image_id,
			'inner_hero_video'       => [],
		];

		$layouts = get_field( 'cms', $page_id );

		if ( ! is_array( $layouts ) ) {
			$layouts = [];
		}

		$layouts = $this->upsert_flexible_layout_at_start( $layouts, 'inner_hero', $inner_hero_layout );

		update_field( 'field_cms', $layouts, $page_id );

		WP_CLI::success( sprintf( 'Seeded Partners inner hero on page ID %d.', $page_id ) );
	}

	/**
	 * Get an existing seeded hero image or import it from the theme assets.
	 *
	 * @param int $page_id Page ID to attach media to.
	 * @return int Attachment ID.
	 */
	private function get_or_import_hero_image( $page_id ) {
		$existing = get_posts(
			[
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => 1,
				'meta_key'       => '_epaton_seed_asset',
				'meta_value'     => 'home_hero',
				'fields'         => 'ids',
			]
		);

		if ( ! empty( $existing ) ) {
			return (int) $existing[0];
		}

		$source = get_template_directory() . '/assets/images/hero.jpg';

		if ( ! file_exists( $source ) ) {
			WP_CLI::error( 'Hero image asset not found at assets/images/hero.jpg.' );
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$tmp = wp_tempnam( basename( $source ) );
		if ( ! $tmp || ! copy( $source, $tmp ) ) {
			WP_CLI::error( 'Could not prepare temporary hero image for import.' );
		}

		$file = [
			'name'     => 'epaton-home-hero.jpg',
			'type'     => 'image/jpeg',
			'tmp_name' => $tmp,
			'error'    => 0,
			'size'     => filesize( $tmp ),
		];

		$attachment_id = media_handle_sideload( $file, $page_id, 'Epaton Home Hero' );

		if ( is_wp_error( $attachment_id ) ) {
			@unlink( $tmp );
			WP_CLI::error( $attachment_id->get_error_message() );
		}

		update_post_meta( $attachment_id, '_epaton_seed_asset', 'home_hero' );
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', 'Epaton and Notape cloud hero graphic' );

		return (int) $attachment_id;
	}

	/**
	 * Get the target page ID for page-builder seeders.
	 *
	 * @param array $assoc_args WP-CLI associative arguments.
	 * @return int
	 */
	private function get_seed_page_id( $assoc_args ) {
		$page_id = isset( $assoc_args['page_id'] ) ? absint( $assoc_args['page_id'] ) : 0;

		if ( $page_id ) {
			return $page_id;
		}

		$page = get_page_by_path( 'home', OBJECT, 'page' );
		if ( ! $page ) {
			WP_CLI::error( 'Could not find a page with slug "home". Pass --page_id=<id>.' );
		}

		return (int) $page->ID;
	}

	/**
	 * Get a post ID by slug and post type.
	 *
	 * @param string $slug Post slug.
	 * @param string $post_type Post type.
	 * @return int
	 */
	private function get_post_id_by_slug( $slug, $post_type ) {
		$posts = get_posts(
			[
				'name'           => $slug,
				'post_type'      => $post_type,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			]
		);

		return ! empty( $posts ) ? (int) $posts[0] : 0;
	}

	/**
	 * Add or replace a flexible content layout, ensuring it is always position 0.
	 *
	 * @param array  $layouts Existing flexible content layouts.
	 * @param string $layout_name Layout name to replace.
	 * @param array  $new_layout New layout values.
	 * @return array
	 */
	private function upsert_flexible_layout_at_start( $layouts, $layout_name, $new_layout ) {
		// Remove any existing occurrence so we can cleanly prepend.
		foreach ( $layouts as $index => $layout ) {
			if ( ! empty( $layout['acf_fc_layout'] ) && $layout_name === $layout['acf_fc_layout'] ) {
				array_splice( $layouts, $index, 1 );
				break;
			}
		}

		array_unshift( $layouts, $new_layout );

		return $layouts;
	}

	/**
	 * Add or replace a flexible content layout by layout name.
	 *
	 * @param array  $layouts Existing flexible content layouts.
	 * @param string $layout_name Layout name to replace.
	 * @param array  $new_layout New layout values.
	 * @return array
	 */
	private function upsert_flexible_layout( $layouts, $layout_name, $new_layout ) {
		foreach ( $layouts as $index => $layout ) {
			if ( ! empty( $layout['acf_fc_layout'] ) && $layout_name === $layout['acf_fc_layout'] ) {
				$layouts[ $index ] = $new_layout;
				return $layouts;
			}
		}

		$layouts[] = $new_layout;

		return $layouts;
	}

	/**
	 * Add or replace a flexible content layout, inserting after another layout when new.
	 *
	 * @param array  $layouts Existing flexible content layouts.
	 * @param string $layout_name Layout name to replace.
	 * @param array  $new_layout New layout values.
	 * @param string $after_layout Layout name to insert after when appending.
	 * @return array
	 */
	private function upsert_flexible_layout_after( $layouts, $layout_name, $new_layout, $after_layout ) {
		foreach ( $layouts as $index => $layout ) {
			if ( ! empty( $layout['acf_fc_layout'] ) && $layout_name === $layout['acf_fc_layout'] ) {
				$layouts[ $index ] = $new_layout;
				return $layouts;
			}
		}

		foreach ( $layouts as $index => $layout ) {
			if ( ! empty( $layout['acf_fc_layout'] ) && $after_layout === $layout['acf_fc_layout'] ) {
				array_splice( $layouts, $index + 1, 0, [ $new_layout ] );
				return $layouts;
			}
		}

		$layouts[] = $new_layout;

		return $layouts;
	}

	/**
	 * Get Core Areas seed content.
	 *
	 * @return array[]
	 */
	private function get_core_areas_seed_data() {
		return [
			[
				'title' => 'Backup & Disaster Recovery',
				'theme' => 'blue',
				'text'  => "We design and implement secure, resilient backup and recovery platforms that protect organisations from data loss, cyber threats, and operational disruption.\n\nFrom SaaS and cloud protection to on-premise and hybrid environments, our solutions are built for reliability and rapid recovery.",
			],
			[
				'title' => 'Primary Storage & Infrastructure',
				'theme' => 'cyan',
				'text'  => "We architect modern storage and infrastructure platforms that balance performance, scalability, and cost.\n\nOur expertise spans software-defined storage, hyper-converged infrastructure, and hybrid cloud environments, ensuring systems are built for both current and future needs.",
			],
			[
				'title' => 'Value & TCO Optimisation',
				'theme' => 'blue',
				'text'  => "We help organisations take control of technology spend. By consolidating platforms, rationalising licensing, and optimising utilisation, we reduce unnecessary cost and improve return on investment.\n\nEvery solution we design is assessed through a commercial as well as technical lens.",
			],
		];
	}

	/**
	 * Get Approach Panels seed content.
	 *
	 * @return array[]
	 */
	private function get_approach_panels_seed_data() {
		return [
			[
				'title' => 'Vendor Independence',
				'theme' => 'blue',
				'body'  => '<p>Independence is at the core of Epaton.<br>We are not aligned to a single manufacturer or platform. This allows us to recommend technologies based solely on suitability, performance, and value.</p><p><strong>Our customers trust us because:</strong></p><ul><li>We are transparent in our recommendations</li><li>We avoid unnecessary complexity</li><li>We prioritise long-term sustainability</li><li>We protect your commercial interests</li></ul><p><strong>You get solutions that fit your business - not someone else&rsquo;s sales targets.</strong></p>',
			],
			[
				'title' => 'Proven Expertise',
				'theme' => 'navy',
				'body'  => '<p>Epaton is an ten-time winner of Specialist Storage Reseller of the Year at the Storage Awards, reflecting our consistent commitment to technical excellence and customer success.</p><p>Our team brings deep experience across enterprise,</p><p>IT, data management, and infrastructure</p><p>architecture supported by strong relationships with</p><p>leading technology providers.</p><p><strong>We combine specialist knowledge with practical delivery expertise.</strong></p>',
			],
		];
	}

	/**
	 * Get Product seed content.
	 *
	 * @return array[]
	 */
	private function get_products_seed_data() {
		return [
			[
				'title'     => 'SaaS Application Backup and Recovery',
				'excerpt'   => 'The latest generation of backup applications allow an on-premise backup appliance to make secure copies of this data or the ability to perform Cloud to Cloud backup with Backup as a Service (BaaS).',
				'image'     => '01.jpg',
				'image_key' => 'saas-application-backup-and-recovery',
			],
			[
				'title'     => 'Data Management and Disaster Recovery',
				'excerpt'   => 'As the various formats, sources and deployments of data grow exponentially, enterprises need advanced data management to optimise this wealth of new data while remaining compatible with existing systems.',
				'image'     => '02.jpg',
				'image_key' => 'data-management-and-disaster-recovery',
			],
			[
				'title'     => 'Storage and Hyper-Converged Infrastructure',
				'excerpt'   => 'Digital data is growing faster than any other commodity and its value for businesses cannot be underestimated. Enterprise class infrastructure needs to store this data reliably and securely, while maintaining flexibility in the most cost-efficient way possible.',
				'image'     => '03.jpg',
				'image_key' => 'storage-and-hyper-converged-infrastructure',
			],
		];
	}

	/**
	 * Get Blog Posts seed content.
	 *
	 * @return array[]
	 */
	private function get_blog_posts_seed_data() {
		$posts = [];

		for ( $i = 1; $i <= 12; $i++ ) {
			$posts[] = [
				'title'   => 'Case Study: UK Council Gets Back To Work With Nutanix',
				'slug'    => sprintf( 'case-study-uk-council-gets-back-to-work-with-nutanix-%02d', $i ),
				'excerpt' => 'Nutanix is a global leader in cloud software and a pioneer in hyperconverged infrastructure solutions.',
				'content' => '<p>Nutanix is a global leader in cloud software and a pioneer in hyperconverged infrastructure solutions.</p>',
			];
		}

		return $posts;
	}

	/**
	 * Import social icons and return Site Settings repeater rows.
	 *
	 * @return array[]
	 */
	private function get_seeded_social_links() {
		$social_links = [];

		foreach ( $this->get_social_links_seed_data() as $social ) {
			$icon_id = $this->get_or_import_seed_image(
				'/Users/mosharafmanu/Desktop/epaton-assets/socials/' . $social['file'],
				0,
				'social_icon_' . $social['slug'],
				$social['label'],
				$social['label']
			);

			$social_links[] = [
				'label' => $social['label'],
				'icon'  => $icon_id,
				'link'  => [
					'title'  => $social['label'],
					'url'    => $social['url'],
					'target' => '_blank',
				],
			];
		}

		return $social_links;
	}

	/**
	 * Get social links seed data.
	 *
	 * @return array[]
	 */
	private function get_social_links_seed_data() {
		return [
			[
				'label' => 'LinkedIn',
				'slug'  => 'linkedin',
				'file'  => 'linkedin.svg',
				'url'   => '#',
			],
			[
				'label' => 'Facebook',
				'slug'  => 'facebook',
				'file'  => 'facebook-square.svg',
				'url'   => '#',
			],
			[
				'label' => 'X',
				'slug'  => 'x-twitter',
				'file'  => 'x-twitter.svg',
				'url'   => '#',
			],
		];
	}

	/**
	 * Get Contact Form 7 form markup.
	 *
	 * @return string
	 */
	private function get_contact_form_7_markup() {
		return <<<'FORM'
<div class="epaton-contact-form">
	<div class="epaton-contact-field">
		<label>First Name*</label>
		[text* first-name autocomplete:given-name]
	</div>

	<div class="epaton-contact-field">
		<label>Last Name*</label>
		[text* last-name autocomplete:family-name]
	</div>

	<div class="epaton-contact-field">
		<label>What can we help you with?*</label>
		[select* enquiry-topic "Managed Services" "Storage, Backup and Recovery" "Products" "Partnerships" "General Enquiry"]
	</div>

	<div class="epaton-contact-field">
		<label>Where did you hear about us?*</label>
		[select* referral-source "Referred" "Search Engine" "LinkedIn" "Event" "Existing Customer" "Other"]
	</div>

	<div class="epaton-contact-field">
		<label>Message</label>
		[textarea message placeholder "Write your message here"]
	</div>

	<div class="epaton-contact-privacy">
		[acceptance privacy-consent]
		<span>By submitting I am agreeing to the<br>privacy policy of this website</span>
	</div>

	[submit class:epaton-contact-submit "SUBMIT"]
</div>
FORM;
	}

	/**
	 * Get Contact Form 7 mail settings.
	 *
	 * @return array
	 */
	private function get_contact_form_7_mail_settings() {
		return [
			'active'             => true,
			'subject'            => 'Epaton contact enquiry from [first-name] [last-name]',
			'sender'             => '[_site_title] <wordpress@[_site_domain]>',
			'recipient'          => 'sales@epaton.co.uk',
			'body'               => "First Name: [first-name]\nLast Name: [last-name]\nWhat can we help you with?: [enquiry-topic]\nWhere did you hear about us?: [referral-source]\n\nMessage:\n[message]\n\n--\nThis enquiry was sent from [_site_title] ([_site_url]).",
			'additional_headers' => '',
			'attachments'        => '',
			'use_html'           => false,
			'exclude_blank'      => false,
		];
	}

	/**
	 * Get Client Logos seed content.
	 *
	 * @return array[]
	 */
	private function get_client_logos_seed_data() {
		return [
			[
				'name'  => 'DELT Shared Services',
				'slug'  => 'delt',
				'file'  => 'DELT Logo.svg',
				'url'   => 'https://www.figma.com/api/mcp/asset/7e6ec1c9-7062-4fe9-abcf-942432237b0f',
			],
			[
				'name'  => 'Carole Nash',
				'slug'  => 'carole-nash',
				'file'  => 'C Nash Logo.svg',
				'url'   => 'https://www.figma.com/api/mcp/asset/520971f2-228b-4640-b773-4c7a138b3fa7',
			],
			[
				'name'  => 'NHS East and North Hertfordshire Teaching NHS Trust',
				'slug'  => 'nhs-east-north-hertfordshire',
				'file'  => 'NHS Logo.svg',
				'url'   => 'https://www.figma.com/api/mcp/asset/511508de-d6a4-42b3-bd27-e81ecefb266e',
			],
			[
				'name'  => 'West Midlands Fire Service',
				'slug'  => 'west-midlands-fire-service',
				'file'  => 'WMFS.svg',
				'url'   => 'https://www.figma.com/api/mcp/asset/66bb20d0-ef21-4400-9798-4c49618f3493',
			],
			[
				'name'  => 'Prada',
				'slug'  => 'prada',
				'file'  => 'prada.svg',
				'url'   => 'https://www.figma.com/api/mcp/asset/e127cde4-44d6-4b8a-b0a8-4b44ee3a66f4',
			],
			[
				'name'  => 'Inchcape',
				'slug'  => 'inchcape',
				'file'  => 'inchcape.svg',
				'url'   => 'https://www.figma.com/api/mcp/asset/79fe5f81-151d-4115-b7b5-b8c61bfd2515',
			],
			[
				'name'  => 'Birkbeck University of London',
				'slug'  => 'birkbeck',
				'file'  => 'Birkbeck.svg',
				'url'   => 'https://www.figma.com/api/mcp/asset/fd6918a0-42e9-4095-830a-44cef1a48f95',
			],
			[
				'name'  => 'Yodel',
				'slug'  => 'yodel',
				'file'  => 'Yodel Logo.svg',
				'url'   => 'https://www.figma.com/api/mcp/asset/7f374c6f-99f7-417d-b7ab-7271ec9d3d4d',
			],
			[
				'name'  => 'NHS Mid Cheshire Hospitals NHS Foundation Trust',
				'slug'  => 'nhs-mid-cheshire',
				'file'  => 'Mid Cheshire.svg',
				'url'   => 'https://www.figma.com/api/mcp/asset/07856587-2791-4219-89a9-e9590663a710',
			],
			[
				'name'  => 'Barrett Steel',
				'slug'  => 'barrett-steel',
				'file'  => 'barrett logo.svg',
				'url'   => 'https://www.figma.com/api/mcp/asset/7772048e-60e4-4653-a1c8-e1f0a06e9629',
			],
			[
				'name'  => 'West Herts College',
				'slug'  => 'west-herts-college',
				'file'  => 'westherts.svg',
				'url'   => 'https://www.figma.com/api/mcp/asset/09f28a41-5685-4b99-ad8f-ae286ed94a1e',
			],
			[
				'name'  => 'Driver and Vehicle Licensing Agency',
				'slug'  => 'dvla',
				'file'  => 'DVLA.svg',
				'url'   => 'https://www.figma.com/api/mcp/asset/5c3f9d7d-65cf-4662-8460-8645fb2b1e2f',
			],
			[
				'name'  => 'De Montfort University Leicester',
				'slug'  => 'de-montfort-university',
				'file'  => 'de-montfort.svg',
				'url'   => 'https://www.figma.com/api/mcp/asset/8c14c3fb-7735-4d90-874b-8f13cb0868b8',
			],
			[
				'name'  => 'Studiocanal',
				'slug'  => 'studiocanal',
				'file'  => 'Studiocanal.svg',
				'url'   => 'https://www.figma.com/api/mcp/asset/85e9e4a7-dd81-45aa-8998-5dcda41bdf3c',
			],
			[
				'name'  => 'Reliance Bank',
				'slug'  => 'reliance-bank',
				'file'  => 'Reliance Bank.svg',
				'url'   => 'https://www.figma.com/api/mcp/asset/2e25dd2c-25cc-4b1a-b82a-4e2cd46b58d6',
			],
			[
				'name'  => 'arvato Bertelsmann',
				'slug'  => 'arvato',
				'file'  => 'arvato.svg',
				'url'   => 'https://www.figma.com/api/mcp/asset/40d737c1-788c-4d40-9df0-cc41a2bd97fa',
			],
			[
				'name'  => 'European Tour',
				'slug'  => 'european-tour',
				'file'  => 'European Tour.svg',
				'url'   => 'https://www.figma.com/api/mcp/asset/74c90f65-c93d-4bd8-b02b-e6c8f1849171',
			],
			[
				'name'  => 'Church\'s',
				'slug'  => 'churchs',
				'file'  => 'Churchs.svg',
				'url'   => 'https://www.figma.com/api/mcp/asset/0335db6d-518f-4e7b-b908-a9d9b38f90ad',
			],
		];
	}

	/**
	 * Get Partners Listing seed content.
	 *
	 * @return array[]
	 */
	private function get_partners_listing_seed_data() {
		return [
			[
				'name'        => 'Nutanix',
				'slug'        => 'nutanix',
				'file'        => 'nutanix.svg',
				'theme'       => 'blue',
				'description' => 'Nutanix is a global leader in cloud software and a pioneer in hyperconverged infrastructure solutions.',
			],
			[
				'name'        => 'Barracuda',
				'slug'        => 'barracuda',
				'file'        => 'Barracuda.svg',
				'theme'       => 'cyan',
				'description' => 'Bringing access to cloud-enabled enterprise-grade security solutions that are easy to buy, deploy, and use.',
			],
			[
				'name'        => 'Assured Data Protection',
				'slug'        => 'assured-data-protection',
				'file'        => 'Assured Data Protection.svg',
				'theme'       => 'blue',
				'description' => 'Global data backup, business continuity, disaster recovery, and threat detection managed service provider.',
			],
			[
				'name'        => 'Juniper',
				'slug'        => 'juniper',
				'file'        => 'Juniper.svg',
				'theme'       => 'cyan',
				'description' => 'A new approach to the network - one that is intelligent, agile, secure and open to any vendor and any network environment.',
			],
			[
				'name'        => 'Rubrik',
				'slug'        => 'rubrik',
				'file'        => 'Rubrik.svg',
				'theme'       => 'blue',
				'description' => 'Rubrik, the Zero Trust Data Security Company, delivers data security and operational resilience for enterprises.',
			],
			[
				'name'        => 'HYCU',
				'slug'        => 'hycu',
				'file'        => 'HYCU.svg',
				'theme'       => 'cyan',
				'description' => 'HYCU is the fastest-growing leader in the multi-cloud and SaaS data protection as a service industry.',
			],
			[
				'name'        => 'Arcserve',
				'slug'        => 'arcserve',
				'file'        => 'Arcserve.svg',
				'theme'       => 'blue',
				'description' => 'A global top 5 data protection vendor with best-in-class solutions that manage, protect, and recover all data workloads.',
			],
			[
				'name'        => 'VM Ware',
				'slug'        => 'vm-ware',
				'file'        => 'VM Ware.svg',
				'theme'       => 'cyan',
				'description' => 'Beyond the barriers of compromise to engineer new ways to make technologies work together seamlessly.',
			],
			[
				'name'        => 'PacketFabric',
				'slug'        => 'packetfabric',
				'file'        => 'PacketFabric.svg',
				'theme'       => 'blue',
				'description' => 'Achieving cloud data agility with Space cloud storage, Transporter data-mobility-as-a-service, and global NaaS.',
			],
			[
				'name'        => 'HP',
				'slug'        => 'hp',
				'file'        => 'HP.svg',
				'theme'       => 'cyan',
				'description' => 'Helps customers use technology to turn ideas into value, and empower them to transform industries, markets, and lives.',
			],
			[
				'name'        => 'Tintri',
				'slug'        => 'tintri',
				'file'        => 'Tintri.svg',
				'theme'       => 'blue',
				'description' => 'Tintri uses storage to speed development cycles, simplify management and predict your every need as you scale.',
			],
			[
				'name'        => 'Nexsan',
				'slug'        => 'nexsan',
				'file'        => 'Nexsan.svg',
				'theme'       => 'cyan',
				'description' => 'Nexsan offers reliable, scalable, and energy-efficient enterprise storage, including immutable solutions, with 25 years of industry excellence.',
			],
			[
				'name'        => 'Dell EMC',
				'slug'        => 'dell-emc',
				'file'        => 'Dell EMC.svg',
				'theme'       => 'blue',
				'description' => 'Provides the essential infrastructure for organizations to build their digital future, transform IT and protect their information.',
			],
			[
				'name'        => 'Pure Storage',
				'slug'        => 'pure-storage',
				'file'        => 'Pure Storage.svg',
				'theme'       => 'cyan',
				'description' => 'Pure Storage enables customers to quickly adopt next-generation technologies, including artificial intelligence and machine learning.',
			],
			[
				'name'        => 'TrueNAS',
				'slug'        => 'truenas',
				'file'        => 'TrueNAS.svg',
				'theme'       => 'blue',
				'description' => 'TrueNAS delivers high-performance file, block, and object storage built on ZFS, trusted by over 60% of the Fortune 500.',
			],
			[
				'name'        => 'Immutably',
				'slug'        => 'immutably',
				'file'        => 'Immutably.svg',
				'theme'       => 'blue',
				'description' => 'Immutably is the smarter, simpler way to protect your data - wherever it lives.',
			],
		];
	}

	/**
	 * Get an existing seeded image or import it from disk.
	 *
	 * @param string $source Source image path.
	 * @param int    $parent_id Parent post ID.
	 * @param string $asset_key Stable seed asset key.
	 * @param string $title Attachment title.
	 * @param string $alt Attachment alt text.
	 * @return int Attachment ID.
	 */
	private function get_or_import_seed_image( $source, $parent_id, $asset_key, $title, $alt ) {
		$existing = get_posts(
			[
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => 1,
				'meta_key'       => '_epaton_seed_asset',
				'meta_value'     => $asset_key,
				'fields'         => 'ids',
			]
		);

		if ( ! empty( $existing ) ) {
			return (int) $existing[0];
		}

		if ( ! file_exists( $source ) ) {
			WP_CLI::error( sprintf( 'Seed image not found: %s', $source ) );
		}

		if ( 'svg' === strtolower( pathinfo( $source, PATHINFO_EXTENSION ) ) ) {
			return $this->import_seed_svg_attachment( $source, $parent_id, $asset_key, $title, $alt );
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$tmp = wp_tempnam( basename( $source ) );
		if ( ! $tmp || ! copy( $source, $tmp ) ) {
			WP_CLI::error( sprintf( 'Could not prepare temporary image for import: %s', $source ) );
		}

		$filetype = wp_check_filetype( basename( $source ) );
		$file_mime = $filetype['type'];

		if ( ! $file_mime && 'svg' === strtolower( pathinfo( $source, PATHINFO_EXTENSION ) ) ) {
			$file_mime = 'image/svg+xml';
		}

		$file     = [
			'name'     => basename( $source ),
			'type'     => $file_mime ?: 'image/jpeg',
			'tmp_name' => $tmp,
			'error'    => 0,
			'size'     => filesize( $tmp ),
		];

		$attachment_id = media_handle_sideload( $file, $parent_id, $title );

		if ( is_wp_error( $attachment_id ) ) {
			@unlink( $tmp );
			WP_CLI::error( $attachment_id->get_error_message() );
		}

		update_post_meta( $attachment_id, '_epaton_seed_asset', $asset_key );
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );

		return (int) $attachment_id;
	}

	/**
	 * Import an SVG attachment without running sideload prefilters.
	 *
	 * The SVG Support plugin can fail in WP-CLI because its sanitizer service is
	 * not initialized for sideload prefilters. These logo exports are trusted
	 * local assets, so copy them directly into uploads and register the
	 * attachment.
	 *
	 * @param string $source Source SVG path.
	 * @param int    $parent_id Parent post ID.
	 * @param string $asset_key Stable seed asset key.
	 * @param string $title Attachment title.
	 * @param string $alt Attachment alt text.
	 * @return int Attachment ID.
	 */
	private function import_seed_svg_attachment( $source, $parent_id, $asset_key, $title, $alt ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$uploads = wp_upload_dir();

		if ( ! empty( $uploads['error'] ) ) {
			WP_CLI::error( $uploads['error'] );
		}

		$filename = wp_unique_filename( $uploads['path'], sanitize_file_name( basename( $source ) ) );
		$target   = trailingslashit( $uploads['path'] ) . $filename;

		if ( ! copy( $source, $target ) ) {
			WP_CLI::error( sprintf( 'Could not copy SVG into uploads: %s', $source ) );
		}

		$attachment_id = wp_insert_attachment(
			[
				'guid'           => trailingslashit( $uploads['url'] ) . $filename,
				'post_mime_type' => 'image/svg+xml',
				'post_title'     => $title,
				'post_content'   => '',
				'post_status'    => 'inherit',
			],
			$target,
			$parent_id,
			true
		);

		if ( is_wp_error( $attachment_id ) ) {
			@unlink( $target );
			WP_CLI::error( $attachment_id->get_error_message() );
		}

		$metadata = wp_generate_attachment_metadata( $attachment_id, $target );

		if ( ! is_wp_error( $metadata ) && ! empty( $metadata ) ) {
			wp_update_attachment_metadata( $attachment_id, $metadata );
		}

		update_post_meta( $attachment_id, '_epaton_seed_asset', $asset_key );
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );

		return (int) $attachment_id;
	}

	/**
	 * Get an existing seeded remote image or import it from a URL.
	 *
	 * @param string $url Remote image URL.
	 * @param int    $parent_id Parent post ID.
	 * @param string $asset_key Stable seed asset key.
	 * @param string $filename Filename to use in the Media Library.
	 * @param string $title Attachment title.
	 * @param string $alt Attachment alt text.
	 * @return int Attachment ID.
	 */
	private function get_or_import_remote_seed_image( $url, $parent_id, $asset_key, $filename, $title, $alt ) {
		$existing = get_posts(
			[
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => 1,
				'meta_key'       => '_epaton_seed_asset',
				'meta_value'     => $asset_key,
				'fields'         => 'ids',
			]
		);

		if ( ! empty( $existing ) ) {
			return (int) $existing[0];
		}

		if ( empty( $url ) ) {
			WP_CLI::error( 'No remote image URL was provided.' );
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$response = wp_remote_get(
			$url,
			[
				'timeout'     => 30,
				'redirection' => 5,
				'headers'     => [
					'Accept' => 'image/png,image/*;q=0.9,*/*;q=0.8',
				],
			]
		);

		if ( is_wp_error( $response ) ) {
			WP_CLI::error( $response->get_error_message() );
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		$body          = wp_remote_retrieve_body( $response );

		if ( 200 !== (int) $response_code || empty( $body ) ) {
			WP_CLI::error( sprintf( 'Could not download client logos image. HTTP status: %d.', (int) $response_code ) );
		}

		$uploads = wp_upload_dir();

		if ( ! empty( $uploads['error'] ) ) {
			WP_CLI::error( $uploads['error'] );
		}

		if ( ! wp_mkdir_p( $uploads['path'] ) ) {
			WP_CLI::error( sprintf( 'Could not create uploads directory: %s', $uploads['path'] ) );
		}

		$filename  = wp_unique_filename( $uploads['path'], sanitize_file_name( $filename ) );
		$file_path = trailingslashit( $uploads['path'] ) . $filename;

		if ( false === file_put_contents( $file_path, $body ) ) {
			WP_CLI::error( sprintf( 'Could not write client logos image to uploads directory: %s', $file_path ) );
		}

		$mime_type = wp_get_image_mime( $file_path );

		if ( ! $mime_type || ! str_starts_with( $mime_type, 'image/' ) ) {
			@unlink( $file_path );
			WP_CLI::error( 'Downloaded client logos file is not a valid image. The Figma asset URL may have expired; pass a fresh --logo_url or use --logo_path.' );
		}

		$attachment_id = wp_insert_attachment(
			[
				'guid'           => trailingslashit( $uploads['url'] ) . $filename,
				'post_mime_type' => $mime_type,
				'post_title'     => $title,
				'post_content'   => '',
				'post_status'    => 'inherit',
			],
			$file_path,
			$parent_id
		);

		if ( is_wp_error( $attachment_id ) ) {
			@unlink( $file_path );
			WP_CLI::error( $attachment_id->get_error_message() );
		}

		$metadata = wp_generate_attachment_metadata( $attachment_id, $file_path );
		wp_update_attachment_metadata( $attachment_id, $metadata );

		update_post_meta( $attachment_id, '_epaton_seed_asset', $asset_key );
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );

		return (int) $attachment_id;
	}
}

WP_CLI::add_command( 'epaton seed-site-settings', [ new Epaton_ACF_Content_Seeder(), 'seed_site_settings' ] );
WP_CLI::add_command( 'epaton seed-hero', [ new Epaton_ACF_Content_Seeder(), 'seed_hero' ] );
WP_CLI::add_command( 'epaton seed-services', [ new Epaton_ACF_Content_Seeder(), 'seed_services' ] );
WP_CLI::add_command( 'epaton seed-products', [ new Epaton_ACF_Content_Seeder(), 'seed_products' ] );
WP_CLI::add_command( 'epaton seed-blog-posts', [ new Epaton_ACF_Content_Seeder(), 'seed_blog_posts' ] );
WP_CLI::add_command( 'epaton seed-contact-form', [ new Epaton_ACF_Content_Seeder(), 'seed_contact_form' ] );
WP_CLI::add_command( 'epaton seed-contact-page', [ new Epaton_ACF_Content_Seeder(), 'seed_contact_page' ] );
WP_CLI::add_command( 'epaton seed-core-areas', [ new Epaton_ACF_Content_Seeder(), 'seed_core_areas' ] );
WP_CLI::add_command( 'epaton seed-approach-panels', [ new Epaton_ACF_Content_Seeder(), 'seed_approach_panels' ] );
WP_CLI::add_command( 'epaton seed-commitment-panel', [ new Epaton_ACF_Content_Seeder(), 'seed_commitment_panel' ] );
WP_CLI::add_command( 'epaton seed-looking-forward', [ new Epaton_ACF_Content_Seeder(), 'seed_looking_forward' ] );
WP_CLI::add_command( 'epaton seed-contact-cta', [ new Epaton_ACF_Content_Seeder(), 'seed_contact_cta' ] );
WP_CLI::add_command( 'epaton seed-clients-section', [ new Epaton_ACF_Content_Seeder(), 'seed_clients_section' ] );
WP_CLI::add_command( 'epaton seed-partners-listing', [ new Epaton_ACF_Content_Seeder(), 'seed_partners_listing' ] );
WP_CLI::add_command( 'epaton seed-inner-hero-products', [ new Epaton_ACF_Content_Seeder(), 'seed_inner_hero_products' ] );
WP_CLI::add_command( 'epaton seed-inner-hero-partners', [ new Epaton_ACF_Content_Seeder(), 'seed_inner_hero_partners' ] );
WP_CLI::add_command( 'epaton seed-who-we-are', [ new Epaton_ACF_Content_Seeder(), 'seed_who_we_are' ] );
