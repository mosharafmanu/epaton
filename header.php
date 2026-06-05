<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package epaton
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">


<header class="site-header">
	<div class="site-header-inner layout-padding">
		<div class="site-header-bar">
			<?php epaton_render_site_logo(); ?>

			<nav class="site-navigation" aria-label="<?php esc_attr_e('Primary navigation', 'epaton'); ?>">
				<?php
wp_nav_menu(
    [
        'theme_location' => 'mainMenu',
        'container'      => false,
        'menu_class'     => 'primary-menu',
        'fallback_cb'    => false,
        'depth'          => 2,
        'walker'         => new Epaton_Primary_Menu_Walker(),
    ]
);
?>
			</nav>

			<div class="site-header-actions">
				<?php epaton_render_header_button(); ?>
				<!-- <button class="menu-trigger hamburger-menu-toggle" type="button" aria-label="<?php esc_attr_e('Open menu', 'epaton'); ?>">
					<span></span>
					<span></span>
					<span></span>
				</button> -->
			</div>
		</div>
	</div>
</header>
