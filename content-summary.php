<?php
/**
 * Plugin Name:       Content Summary
 * Description:       e-labo block written with ESNext standard and JSX support.
 * Requires at least: 5.7
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            e-labo
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       content-summary
 *
 * @package           create-block
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/writing-your-first-block-type/
 */
function create_block_content_summary_block_init() {
	register_block_type_from_metadata( __DIR__ );
}
add_action( 'init', 'create_block_content_summary_block_init' );

/**
 * Render block and generate summary
 *
 * @param string $block_content Content of the Gutenberg block.
 * @param string $block Gutenberg block infos and parameters.
 */
function elabo_content_summary_render( $block_content, $block ) {
	if ( 'create-block/content-summary' === $block['blockName'] ) {
		$summary_elements = array();
		$summary_html     = '';
		foreach ( $block['innerBlocks'] as $block_in ) {
			if ( 'core/heading' == $block_in['blockName'] && ! isset( $block_in['attrs']['level'] ) ) {
				$inner_content = wp_strip_all_tags( $block_in['innerContent'][0] );
				$anchor        = sanitize_title( $inner_content );

				$summary_elements[] = '<li><a href="#' . $anchor . '">' . esc_html( $inner_content ) . '</a></li>';
			}
		}
		if ( ! empty( $summary_elements ) && 2 < count( $summary_elements ) ) {
			$summary_html = sprintf(
				'<div class="summary-menu">
					<div class="summary-title">%1$s</div>
					<ul class="summary-items">
					%2$s
					</ul>
					<button class="summary-open-button hidden"><span class="icon-arrow-right"></span>%3$s<span class="icon-arrow-right"></span></button>
				</div>',
				esc_html( 'Au sommaire', 'luberon-sud' ),
				implode( '', $summary_elements ),
				esc_html( 'Sommaire', 'luberon-sud' )
			);
		}
		return sprintf(
			'<div class="page-aside">
				<div class="col-aside">
					<div class="page-summary">
						%1$s
						<div class="summary-socials">
						%2$s
						</div>
					</div>
				</div>
				<div class="col-page">
					%3$s
				</div>
			</div>',
			$summary_html,
			do_shortcode( '[addtoany buttons="facebook,twitter,pinterest"]' ),
			$block_content
		);
	} else {
		return $block_content;
	}
}

add_filter( 'render_block', 'elabo_content_summary_render', 10, 3 );
