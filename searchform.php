<?php
/**
 * Custom Search Form
 *
 * @package epaton
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="search-field" class="screen-reader-text"><?php esc_html_e( 'Search for:', 'epaton' ); ?></label>
	<input 
		type="search" 
		id="search-field" 
		class="search-field" 
		placeholder="<?php esc_attr_e( 'Search...', 'epaton' ); ?>" 
		value="<?php echo get_search_query(); ?>" 
		name="s" 
		required
	/>
	<button type="submit" class="search-submit">
		<?php esc_html_e( 'Search', 'epaton' ); ?>
	</button>
</form>

