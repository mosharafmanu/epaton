<?php
/**
 * epaton  functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package epaton
 */

if (!defined('_S_VERSION')) {
    define('_S_VERSION', '1.0.0');
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function epaton_setup() {
    /*
     * Make theme available for translation.
     * Translations can be filed in the /languages/ directory.
     * If you're building a theme based on epaton , use a find and replace
     * to change 'epaton' to the name of your theme in all the template files.
     */
    load_theme_textdomain('epaton', get_template_directory() . '/languages');

    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    /*
     * Let WordPress manage the document title.
     * By adding theme support, we declare that this theme does not use a
     * hard-coded <title> tag in the document head, and expect WordPress to
     * provide it for us.
     */
    add_theme_support('title-tag');

    /*
     * Enable support for Post Thumbnails on posts and pages.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    // This theme uses wp_nav_menu() in multiple locations.
    register_nav_menus(
        array(
            'mainMenu'   => esc_html__('Main Menu', 'epaton'),
            'footerMenu' => esc_html__('Footer Menu', 'epaton'),
        )
    );

    /*
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    // Set up the WordPress core custom background feature.
    add_theme_support(
        'custom-background',
        apply_filters(
            'epaton_custom_background_args',
            array(
                'default-color' => 'ffffff',
                'default-image' => '',
            )
        )
    );

    // Add theme support for selective refresh for widgets.
    add_theme_support('customize-selective-refresh-widgets');

    /**
     * Add support for core custom logo.
     *
     * @link https://codex.wordpress.org/Theme_Logo
     */
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 250,
            'width'       => 250,
            'flex-width'  => true,
            'flex-height' => true,
        )
    );
}
add_action('after_setup_theme', 'epaton_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function epaton_content_width() {
    $GLOBALS['content_width'] = apply_filters('epaton_content_width', 640);
}
add_action('after_setup_theme', 'epaton_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function epaton_widgets_init() {

    register_sidebar(
        array(
            'name'          => esc_html__('Sidebar', 'epaton'),
            'id'            => 'sidebar-1',
            'description'   => esc_html__('Add widgets here.', 'epaton'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<div class="widget-title h6-style">',
            'after_title'   => '</div>',
        )
    );
}
add_action('widgets_init', 'epaton_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function epaton_scripts() {
    // Only load frontend assets (not in admin)
    if (is_admin()) {
        return;
    }

    // Enqueue CSS files - Fonts first for critical loading
	wp_enqueue_style('epaton-fonts', 'https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap', array(), NULL, 'all');

    wp_style_add_data('epaton-fonts', 'priority', 'high');

    wp_enqueue_style('epaton-spacer', get_template_directory_uri() . '/assets/css/spacer.css', array(), _S_VERSION);

    wp_enqueue_style('epaton-utilities', get_template_directory_uri() . '/assets/css/utilities.css', array(), _S_VERSION);

    wp_enqueue_style('slick-carousel', get_template_directory_uri() . '/assets/css/slick.css', array(), _S_VERSION);

    wp_enqueue_style('epaton-slick-carousel', get_template_directory_uri() . '/assets/css/epaton-slick-custom.css', array(), _S_VERSION);

    wp_enqueue_style('epaton-form', get_template_directory_uri() . '/assets/css/epaton-form.css', array(), _S_VERSION);

    wp_enqueue_style('epaton-video-behaviors', get_template_directory_uri() . '/assets/css/video-behaviors.css', array(), _S_VERSION);

    wp_enqueue_style('epaton-video-popup', get_template_directory_uri() . '/assets/css/video-popup.css', array(), _S_VERSION);

	wp_enqueue_style('epaton-theme-style', get_template_directory_uri() . '/assets/css/epaton-theme-style.css', array(), _S_VERSION);

    // Main stylesheet
    wp_enqueue_style('epaton-style', get_stylesheet_uri(), array(), _S_VERSION);
    wp_style_add_data('epaton-style', 'rtl', 'replace');

    // Enqueue JavaScript files
    wp_enqueue_script('slick-carousel', get_template_directory_uri() . '/assets/js/slick.js', array('jquery'), _S_VERSION, true);

    wp_enqueue_script('epaton-carousels', get_template_directory_uri() . '/assets/js/epaton-carousels.js', array('jquery', 'slick-carousel'), _S_VERSION, true);

    // Localize carousel arrows SVG
    ob_start();
    get_template_part('assets/svgs/carousel-arrow-left');
    $arrow_left = ob_get_clean();

    ob_start();
    get_template_part('assets/svgs/carousel-arrow-right');
    $arrow_right = ob_get_clean();

    wp_localize_script(
        'epaton-carousels',
        'epatonCarousel',
        array(
            'arrowLeft'  => $arrow_left,
            'arrowRight' => $arrow_right,
        )
    );

    wp_enqueue_script('jquery-vimeo-player', get_template_directory_uri() . '/assets/js/jquery.mb.vimeo_player.min.js', array('jquery'), _S_VERSION, true);

    wp_enqueue_script('epaton-video-behaviors', get_template_directory_uri() . '/assets/js/video-behaviors.js', array('jquery'), _S_VERSION, true);

    wp_enqueue_script('epaton-video-popup', get_template_directory_uri() . '/assets/js/video-popup.js', array('jquery'), _S_VERSION, true);

    wp_enqueue_script('epaton-hamburger-menu', get_template_directory_uri() . '/assets/js/hamburger-menu.js', array('jquery'), _S_VERSION, true);

	wp_enqueue_script('epaton-scripts', get_template_directory_uri() . '/assets/js/scripts.js', array('jquery'), _S_VERSION, true);

}
add_action('wp_enqueue_scripts', 'epaton_scripts');

/**
 * Disable Gutenberg Editor Completely
 * Use Classic Editor for all post types
 */
function epaton_disable_gutenberg() {
    // Disable Gutenberg for all post types
    add_filter('use_block_editor_for_post_type', '__return_false');

    // Disable Gutenberg for posts
    add_filter('use_block_editor_for_post', '__return_false');
}
add_action('wp_loaded', 'epaton_disable_gutenberg');

/**
 * Disable the block widget editor and restore classic widgets
 */
function epaton_disable_block_widgets() {
    remove_theme_support('widgets-block-editor');
}
add_action('after_setup_theme', 'epaton_disable_block_widgets');

/**
 * Disable Gutenberg block library CSS
 * Removes unnecessary CSS from frontend for better performance
 */
function epaton_get_blocked_style_handles() {
    return array(
        'wp-block-library',
        'wp-block-library-theme',
        'global-styles',
        'classic-theme-styles',
    );
}

function epaton_disable_block_library_css() {
    foreach (epaton_get_blocked_style_handles() as $handle) {
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
    }
}
add_action('wp_enqueue_scripts', 'epaton_disable_block_library_css', 100);
add_action('wp_print_styles', 'epaton_disable_block_library_css', 100);

/**
 * Disable Gutenberg block library CSS in admin
 * Further performance optimization
 */
function epaton_disable_block_library_css_admin() {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
}
add_action('admin_enqueue_scripts', 'epaton_disable_block_library_css_admin', 100);

/**
 * ACF JSON Sync Configuration
 * Save and load ACF field groups from JSON files
 */
add_filter('acf/settings/save_json', 'epaton_acf_json_save_point');
function epaton_acf_json_save_point($path) {
    return get_stylesheet_directory() . '/acf-json';
}

add_filter('acf/settings/load_json', 'epaton_acf_json_load_point');
function epaton_acf_json_load_point($paths) {
    unset($paths[0]);
    $paths[] = get_stylesheet_directory() . '/acf-json';
    return $paths;
}

/**
 * Load theme core files
 */

// Image sizes registration
require get_template_directory() . '/inc/image-sizes.php';

// Helper functions
require get_template_directory() . '/inc/helper-functions/breadcrumb.php';
require get_template_directory() . '/inc/helper-functions/button-renderer.php';
require get_template_directory() . '/inc/helper-functions/flexible-content.php';
require get_template_directory() . '/inc/helper-functions/icon-renderer.php';
require get_template_directory() . '/inc/helper-functions/pagination.php';
require get_template_directory() . '/inc/helper-functions/responsive-picture.php';
require get_template_directory() . '/inc/helper-functions/site-settings.php';
require get_template_directory() . '/inc/helper-functions/video-renderer.php';

// Components - Only load on frontend (not in admin)
if (!is_admin()) {
    require get_template_directory() . '/inc/components/video/video-autoplay-controls.php';
    require get_template_directory() . '/inc/components/header/hamburger-menu.php';
}

/**
 * Remove all inline style="" attributes from post content
 * Only runs on frontend single posts
 */
add_filter('the_content', function ($content) {
    // Only run on frontend
    if (is_admin()) {
        return $content;
    }

    // Only apply cleanup on single posts
    if (is_singular('post')) {
        $content = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $content);
    }
    return $content;
}, 20);
