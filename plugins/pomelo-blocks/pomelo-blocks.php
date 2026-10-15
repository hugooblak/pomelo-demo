<?php
/**
 * Plugin Name: Pomelo Coffee Roasters Blocks
 * Description: The moving parts of the Pomelo Coffee Roasters shop: hero tasting-note tags, the tasting-notes ticker, the coffee cards (real WooCommerce products with add to cart), the subscription loop, the brew-method tiles and the demo newsletter field. Text is edited in each block's sidebar.
 * Version: 1.0.0
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * Author: Hugo Oblak
 * License: GPL-2.0-or-later
 * Text Domain: pomelo-blocks
 *
 * Why a plugin and not theme code: the tasting notes, tags and tiles are content the team edits,
 * and the coffee cards read the shop's own products. They must survive a redesign.
 *
 * @package PomeloBlocks
 */

defined( 'ABSPATH' ) || exit;

define( 'PMB_URL', plugin_dir_url( __FILE__ ) );
define( 'PMB_DIR', __DIR__ );
define( 'PMB_VER', '1.0.0' );

add_action(
	'init',
	function () {
		foreach ( array( 'hero-chips', 'notes-ticker', 'origin-cards', 'sub-loop', 'brew-tiles', 'signup' ) as $block ) {
			register_block_type( __DIR__ . '/blocks/' . $block );
		}
	}
);

// One stylesheet for every block, in the editor and on the site.
add_action(
	'enqueue_block_assets',
	function () {
		wp_enqueue_style( 'pomelo-blocks', PMB_URL . 'blocks.css', array(), PMB_VER );
	}
);

// One script for the whole page, front end only. It runs a single animation loop.
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_script( 'pomelo-blocks', PMB_URL . 'blocks.js', array(), PMB_VER, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	}
);

add_action(
	'enqueue_block_editor_assets',
	function () {
		wp_enqueue_script(
			'pomelo-blocks-editor',
			PMB_URL . 'editor.js',
			array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n' ),
			PMB_VER,
			true
		);
	}
);

/**
 * Split a textarea into trimmed, non-empty lines.
 *
 * @param string $text Text from a block attribute.
 * @return string[]
 */
function pmb_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', explode( "\n", (string) $text ) ), 'strlen' ) );
}

/**
 * Split a line "a | b | c" into at least $n trimmed cells.
 *
 * @param string $line Line.
 * @param int    $n    Minimum number of cells.
 * @return string[]
 */
function pmb_cells( $line, $n = 3 ) {
	return array_pad( array_map( 'trim', explode( '|', (string) $line ) ), $n, '' );
}

/**
 * The origin tints. The products store one of these slugs (meta _pm_tint).
 *
 * @return array slug => hex.
 */
function pmb_tints() {
	return array(
		'sage'   => '#B5B690',
		'butter' => '#EECF8B',
		'sky'    => '#AFC2C8',
		'sand'   => '#E3CBAE',
		'peach'  => '#E6BC9F',
		'lilac'  => '#CFC5DA',
	);
}

/**
 * A tint slug as a colour, falling back to sand.
 *
 * @param string $slug Tint slug.
 * @return string Hex colour.
 */
function pmb_tint( $slug ) {
	$t = pmb_tints();
	return $t[ $slug ] ?? $t['sand'];
}

/**
 * "Card colour" box on each WooCommerce product: the tint behind its photo on the coffee cards.
 */
add_action(
	'add_meta_boxes_product',
	function () {
		add_meta_box(
			'pmb_tint',
			__( 'Card colour', 'pomelo-blocks' ),
			function ( $post ) {
				$current = get_post_meta( $post->ID, '_pm_tint', true );
				wp_nonce_field( 'pmb_tint', 'pmb_tint_nonce' );
				echo '<label for="pmb-tint" class="screen-reader-text">' . esc_html__( 'Card colour', 'pomelo-blocks' ) . '</label><select id="pmb-tint" name="pmb_tint" style="width:100%">';
				foreach ( pmb_tints() as $slug => $hex ) {
					printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $slug ), selected( $current, $slug, false ), esc_html( ucfirst( $slug ) . ' ' . $hex ) );
				}
				echo '</select><p class="description">' . esc_html__( 'The colour behind this coffee\'s photo on the home page cards.', 'pomelo-blocks' ) . '</p>';
			},
			'product',
			'side'
		);
	}
);

add_action(
	'save_post_product',
	function ( $post_id ) {
		if ( ! isset( $_POST['pmb_tint_nonce'], $_POST['pmb_tint'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pmb_tint_nonce'] ) ), 'pmb_tint' ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$tint = sanitize_key( wp_unslash( $_POST['pmb_tint'] ) );
		if ( isset( pmb_tints()[ $tint ] ) ) {
			update_post_meta( $post_id, '_pm_tint', $tint );
		}
	}
);

/**
 * Line icon for the tasting-notes ticker, same set as the static site.
 *
 * @param string $name pistachio, jasmine, lemon, apple, chocolate, cane.
 * @return string SVG markup.
 */
function pmb_note_icon( $name ) {
	$paths = array(
		'pistachio' => '<path pathLength="1" d="M10 27c-4-7-1-17 9-19 10 2 13 12 9 19"/><path pathLength="1" d="M13 25c2-6 10-6 12 0"/><path pathLength="1" d="M19 8v6"/>',
		'jasmine'   => '<circle pathLength="1" cx="19" cy="19" r="3"/><path pathLength="1" d="M19 16c-3-4-3-9 0-11 3 2 3 7 0 11"/><path pathLength="1" d="M21.8 18c3.6-3.3 8.6-4 10.4-1.3-1.2 3-6.2 3.6-10.4 1.3"/><path pathLength="1" d="M20.8 21.7c4.4 2 6.4 6.6 4.4 9-3.2-.4-5-4.8-4.4-9"/><path pathLength="1" d="M17.2 21.7c.6 4.2-1.2 8.6-4.4 9-2-2.4 0-7 4.4-9"/><path pathLength="1" d="M16.2 18c-4.2 2.3-9.2 1.7-10.4-1.3 1.8-2.7 6.8-2 10.4 1.3"/>',
		'lemon'     => '<path pathLength="1" d="M6 21c0-7 6-12 13-12s13 5 13 12-6 10-13 10S6 28 6 21z"/><path pathLength="1" d="M19 9c1-3 4-5 8-5-1 4-4 5-8 5"/>',
		'apple'     => '<path pathLength="1" d="M19 12c-3-3-12-2-12 8 0 8 5 13 8 13 2 0 2-1 4-1s2 1 4 1c3 0 8-5 8-13 0-10-9-11-12-8z"/><path pathLength="1" d="M19 12c0-3 1-6 4-7"/>',
		'chocolate' => '<rect pathLength="1" x="7" y="8" width="24" height="22" rx="2"/><path pathLength="1" d="M15 8v22M23 8v22M7 15.3h24M7 22.6h24"/>',
		'cane'      => '<path pathLength="1" d="M9 32 27 6"/><path pathLength="1" d="M15 34 31 11"/><path pathLength="1" d="M13 22l4 3M19 13l4 3M21 26l4 3M26 18l3 2"/><path pathLength="1" d="M18 18c-5-1-9-4-10-9 5 0 9 3 10 9"/>',
	);
	return '<svg viewBox="0 0 38 38" aria-hidden="true" focusable="false">' . ( $paths[ $name ] ?? $paths['jasmine'] ) . '</svg>';
}

/**
 * Line drawing of a brewer, with the coffee shape that fills it.
 *
 * @param string $name pourover, aeropress, chemex, frenchpress, espresso.
 * @param string $uid  Unique id for the clip path.
 * @return string SVG markup.
 */
function pmb_brewer( $name, $uid ) {
	$b = array(
		'pourover'    => array( '<path d="M38 66h44c6 0 8 6 6 14l-6 40c-1 6-4 8-10 8H48c-6 0-9-2-10-8l-6-40c-2-8 0-14 6-14z"/>', '<rect class="liq" x="20" y="92" width="80" height="40"/>', array( 60, 62 ), '<path d="M28 20h64L70 56H50z"/><path d="M24 20h72"/><path d="M38 66h44c6 0 8 6 6 14l-6 40c-1 6-4 8-10 8H48c-6 0-9-2-10-8l-6-40c-2-8 0-14 6-14z"/><path d="M86 78c14 0 16 24 0 26"/>' ),
		'aeropress'   => array( '<path d="M36 100h48v20c0 6-4 9-10 9H46c-6 0-10-3-10-9z"/>', '<rect class="liq" x="30" y="106" width="60" height="26"/>', array( 60, 88 ), '<rect x="48" y="10" width="24" height="26" rx="2"/><path d="M44 10h32"/><rect x="42" y="30" width="36" height="56" rx="3"/><path d="M42 44h8M42 56h8M42 68h8"/><path d="M38 86h44"/><path d="M36 100h48v20c0 6-4 9-10 9H46c-6 0-10-3-10-9z"/><path d="M84 106c10 0 10 14 0 14"/>' ),
		'chemex'      => array( '<path d="M54 70h12l18 58H36z"/>', '<rect class="liq" x="30" y="96" width="60" height="34"/>', array( 60, 66 ), '<path d="M36 12h48L66 66l18 62H36l18-62z"/><path d="M52 58h16M50 66h20"/><path d="M57 66l3 8 3-8"/>' ),
		'frenchpress' => array( '<rect x="36" y="34" width="44" height="90" rx="3"/>', '<rect class="liq" x="30" y="66" width="60" height="62"/>', array( 58, 40 ), '<circle cx="58" cy="10" r="4"/><path d="M58 14v50"/><path d="M48 64h20"/><path d="M32 30h52"/><rect x="34" y="30" width="48" height="96" rx="4"/><path d="M82 46h12v58H82"/><path d="M30 130h56"/>' ),
		'espresso'    => array( '<path d="M38 88h44v18c0 9-7 14-16 14H54c-9 0-16-5-16-14z"/>', '<rect class="liq" x="30" y="96" width="60" height="28"/>', array( 60, 58 ), '<path d="M26 28h50v12c0 6-4 9-10 9H36c-6 0-10-3-10-9z"/><path d="M76 36l24-6"/><path d="M54 49v6M66 49v6"/><path d="M38 88h44v18c0 9-7 14-16 14H54c-9 0-16-5-16-14z"/><path d="M82 94c10 0 10 14 0 14"/><path d="M28 126h64"/>' ),
	);
	$x    = $b[ $name ] ?? $b['pourover'];
	$id   = 'pmb-cv-' . sanitize_html_class( $uid );
	$drip = '';
	for ( $i = 0; $i < 3; $i++ ) {
		$drip .= sprintf( '<circle class="drip" cx="%d" cy="%d" r="2"/>', $x[2][0], $x[2][1] );
	}
	return '<svg viewBox="0 0 120 140" aria-hidden="true" focusable="false"><defs><clipPath id="' . esc_attr( $id ) . '">' . $x[0] . '</clipPath></defs><g clip-path="url(#' . esc_attr( $id ) . ')">' . $x[1] . '</g>' . $drip . $x[3] . '</svg>';
}
