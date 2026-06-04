<?php
/**
 * Button Renderer Helper
 *
 * Renders ACF link field buttons with consistent styling across the site.
 *
 * @package epaton
 */

if ( ! function_exists( 'epaton_render_button' ) ) {
	/**
	 * Render a button from ACF link field
	 *
	 * @param array $button_link ACF link field array with 'url', 'title', 'target'.
	 * @param array $args {
	 *     Optional customization.
	 *
 * @type string $style Button style class. Default 'btn-accent'.
 *                         Options: 'btn-primary', 'btn-accent', 'btn-outline'.
	 *     @type bool   $show_icon Show arrow icon. Default true.
	 *     @type string $class Additional CSS classes. Default ''.
	 *     @type bool   $echo Echo or return. Default true.
	 * }
	 * @return string|void
	 */
	function epaton_render_button( $button_link, $args = [] ) {
		// Bail if no link data
		if ( empty( $button_link ) || ! is_array( $button_link ) || empty( $button_link['url'] ) ) {
			return;
		}

		// Default arguments
		$defaults = [
			'style'     => 'btn-accent',
			'show_icon' => true,
			'class'     => '',
			'echo'      => true,
		];
		$args = wp_parse_args( $args, $defaults );

		// Extract link data
		$link_url    = $button_link['url'] ?? '';
		$link_title  = $button_link['title'] ?? '';
		$link_target = $button_link['target'] ?? '_self';

		// Skip if no title/text
		if ( empty( $link_title ) ) {
			return;
		}

		// Build button classes
		$button_classes = 'site-btn ' . esc_attr( $args['style'] );
		if ( ! empty( $args['class'] ) ) {
			$button_classes .= ' ' . esc_attr( $args['class'] );
		}

		// Start output buffering
		ob_start();
		?>
		<a href="<?php echo esc_url( $link_url ); ?>"
			class="<?php echo esc_attr( $button_classes ); ?>"
			target="<?php echo esc_attr( $link_target ); ?>">
			<span class="btn-text"><?php echo esc_html( $link_title ); ?></span>
			<?php if ( $args['show_icon'] ) : ?>
				<span class="btn-icon">
					<?php get_template_part( 'assets/svgs/double-angle-right' ); ?>
				</span>
			<?php endif; ?>
		</a>
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'epaton_render_buttons' ) ) {
	/**
	 * Render multiple buttons from ACF repeater field
	 *
	 * @param array $buttons Array of button data from ACF repeater.
	 *                       Each item should have 'button_link' and optionally 'button_style'.
	 * @param array $args {
	 *     Optional customization.
	 *
	 *     @type string $wrapper_class CSS classes for wrapper div. Default 'btns'.
	 *     @type string $default_style Default button style if not specified. Default 'btn-accent'.
	 *     @type bool   $show_icon Show arrow icon on buttons. Default true.
	 *     @type bool   $echo Echo or return. Default true.
	 * }
	 * @return string|void
	 */
	function epaton_render_buttons( $buttons, $args = [] ) {
		// Bail if no buttons
		if ( empty( $buttons ) || ! is_array( $buttons ) ) {
			return;
		}

		// Default arguments
		$defaults = [
			'wrapper_class' => 'btns',
			'default_style' => 'btn-accent',
			'show_icon'     => true,
			'echo'          => true,
		];
		$args = wp_parse_args( $args, $defaults );

		// Start output buffering
		ob_start();
		?>
		<div class="<?php echo esc_attr( $args['wrapper_class'] ); ?>">
			<?php foreach ( $buttons as $button ) : ?>
				<?php
				$button_link  = $button['button_link'] ?? [];
				$button_style = $button['button_style'] ?? $args['default_style'];

				// Skip if no link data
				if ( empty( $button_link ) || empty( $button_link['url'] ) ) {
					continue;
				}

				// Render individual button
				if ( function_exists( 'epaton_render_button' ) ) {
					epaton_render_button(
						$button_link,
						[
							'style'     => $button_style,
							'show_icon' => $args['show_icon'],
							'echo'      => true,
						]
					);
				}
				?>
			<?php endforeach; ?>
		</div>
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

