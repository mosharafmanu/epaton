<?php
/**
 * Partners Listing Section
 *
 * @package epaton
 */

$heading  = get_sub_field( 'partners_listing_heading' );
$partners = get_sub_field( 'partners_listing_items' );

if ( empty( $heading ) && empty( $partners ) ) {
	return;
}
?>

<section class="partners-listing-section layout-padding">
	<div class="epaton-container">
		<?php if ( $heading ) : ?>
			<h2 class="partners-listing-heading"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( is_array( $partners ) && $partners ) : ?>
			<div class="partners-listing-grid">
				<?php foreach ( $partners as $partner ) : ?>
					<?php
					$name        = $partner['name'] ?? '';
					$description = $partner['description'] ?? '';
					$logo        = $partner['logo'] ?? [];
					$theme       = $partner['theme'] ?? 'blue';
					$theme       = in_array( $theme, [ 'blue', 'cyan' ], true ) ? $theme : 'blue';

					if ( empty( $name ) && empty( $description ) && empty( $logo ) ) {
						continue;
					}
					?>

					<article class="partners-listing-card theme-<?php echo esc_attr( $theme ); ?>">
						<?php if ( $logo && function_exists( 'epaton_render_icon' ) ) : ?>
							<div class="partners-listing-logo-wrap">
								<?php
								epaton_render_icon(
									$logo,
									[
										'class' => 'partners-listing-logo',
										'alt'   => $name,
									]
								);
								?>
							</div>
						<?php endif; ?>

						<?php if ( $name ) : ?>
							<h3 class="partners-listing-name"><?php echo esc_html( $name ); ?></h3>
						<?php endif; ?>

						<?php if ( $description ) : ?>
							<p class="partners-listing-description"><?php echo esc_html( $description ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
