<?php
/**
 * Plugin Name: Pomelo Coffee Roasters Demo Notes
 * Description: Portfolio helper. Shows a "this is a demo" bar and, when switched on, a note above each section explaining the design decision behind it. Delete this plugin on a real site.
 * Version: 1.0.0
 * Requires at least: 6.6
 * Author: Hugo Oblak
 * License: GPL-2.0-or-later
 *
 * The notes are not stored in page content, so editors never see them and they vanish with the plugin.
 *
 * @package PomeloDemoNotes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Notes on or off. ?notes=1 turns them on (and remembers it in a cookie while you browse), ?notes=0 turns them off.
 */
function pmd_notes_on() {
	static $on = null;
	if ( null === $on ) {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['notes'] ) ) {
			$on = '1' === $_GET['notes'];
		} else {
			$on = isset( $_COOKIE['pmd_notes'] ) && '1' === $_COOKIE['pmd_notes'];
		}
		// phpcs:enable
	}
	return $on;
}

add_action(
	'send_headers',
	function () {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['notes'] ) && ! headers_sent() ) {
			setcookie( 'pmd_notes', pmd_notes_on() ? '1' : '0', 0, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
		}
	}
);

/**
 * The notes, keyed by the class of the section they sit above.
 *
 * @return array
 */
function pmd_notes() {
	return array(
		'pm-hero' => array(
			'One colour per origin',
			'The product leads: three bags on the terracotta field, each in its own colour, and a tasting-note tag drops onto each one. The tags, photo text and colours are edited in the block sidebar.',
		),
		'pm-shop' => array(
			'A real shop, not a picture of one',
			'These cards read the WooCommerce products. Add to cart really adds to the cart, a small bag arcs into the header cart and WooCommerce\'s own cart drawer opens. Each coffee\'s colour is set on the product ("Card colour").',
		),
		'pm-sub'  => array(
			'Subscriptions, shown honestly',
			'The bean walks a loop and drops a bag each lap: the idea of a subscription in one picture. In this demo the band is marketing only. A real store adds a subscriptions plugin for WooCommerce, which is not installed here, so the button goes to the shop.',
		),
		'pm-brew' => array(
			'Shop by how you brew',
			'Each brewer fills with coffee as it comes into view. The tiles link to the shop; with more coffees they would link to a filtered list per brew method.',
		),
	);
}

/**
 * Put a note in front of each matching section when notes are on.
 */
add_filter(
	'render_block',
	function ( $html, $block ) {
		if ( is_admin() || ! pmd_notes_on() ) {
			return $html;
		}
		$classes = preg_split( '/\s+/', (string) ( $block['attrs']['className'] ?? '' ) );
		foreach ( pmd_notes() as $class => $note ) {
			if ( in_array( $class, $classes, true ) && 'core/group' === $block['blockName'] ) {
				return sprintf(
					'<aside class="pmd-note" aria-label="%s"><p class="pmd-note-title"><span>Design note</span> %s</p><p>%s</p></aside>',
					esc_attr( 'Design note: ' . $note[0] ),
					esc_html( $note[0] ),
					esc_html( $note[1] )
				) . $html;
			}
		}
		return $html;
	},
	10,
	2
);

/**
 * Demo bar at the very top of every page.
 *
 * It sits above the header, which would otherwise put its link ahead of WordPress's skip link in the
 * tab order. So we print our own skip link first and hide core's.
 */
add_action(
	'wp_body_open',
	function () {
		printf(
			'<a class="pmd-skip skip-link screen-reader-text" href="#wp--skip-link--target">%s</a>',
			esc_html__( 'Skip to content', 'pomelo-demo-notes' )
		);
	},
	0
);

add_action(
	'wp_body_open',
	function () {
		$toggle = add_query_arg( 'notes', pmd_notes_on() ? '0' : '1' );
		?>
		<div class="pmd-bar" role="region" aria-label="Demo notice">
			<p><strong>Portfolio demo.</strong> Pomelo Coffee Roasters is a fictional shop. Orders are saved, but no payment is taken and nothing ships. Subscriptions are shown as a design only.
				<a href="<?php echo esc_url( $toggle ); ?>"><?php echo pmd_notes_on() ? 'Hide the design notes' : 'Show the design notes behind each section'; ?></a>
			</p>
		</div>
		<?php
	},
	1
);

add_action(
	'wp_head',
	function () {
		?>
		<style id="pmd-css">
			.pmd-bar ~ a.skip-link:not(.pmd-skip){display:none !important}
			.pmd-bar{background:#000;color:#EEE9E2;font-size:.8125rem;line-height:1.5;padding:.55rem 1rem;text-align:center;position:relative;z-index:60}
			.pmd-bar p{margin:0}.pmd-bar a{color:#E9C7A0;font-weight:500;margin-left:.35rem}
			.pmd-note{box-sizing:border-box;margin:0;padding:.9rem clamp(1rem,7.9%,10rem);background:#FFF8DB;border-top:2px dashed #C99A06;border-bottom:2px dashed #C99A06;color:#3D2E00;font-size:.9375rem;line-height:1.55}
			.pmd-note p{margin:0;max-width:90ch}.pmd-note-title{font-weight:700;margin-bottom:.25rem !important}
			.pmd-note-title span{display:inline-block;background:#3D2E00;color:#FFF8DB;font-size:.6875rem;letter-spacing:.06em;text-transform:uppercase;padding:.1rem .4rem;margin-right:.35rem;vertical-align:.1em}
		</style>
		<?php
	}
);

/**
 * A short guide on the Dashboard for anyone reviewing the demo in WordPress Playground.
 */
add_action(
	'wp_dashboard_setup',
	function () {
		wp_add_dashboard_widget(
			'pmd_guide',
			'Pomelo Coffee Roasters demo: where to look',
			function () {
				$items = array(
					array( home_url( '/?notes=1' ), 'Home page with the design notes switched on' ),
					array( home_url( '/shop/' ), 'The shop: add a coffee to the cart and check out (no payment is taken)' ),
					array( admin_url( 'edit.php?post_type=product' ), 'Products: prices, tasting notes, photos and each card\'s colour' ),
					array( admin_url( 'admin.php?page=wc-orders' ), 'Orders: every demo order you place lands here' ),
					array( admin_url( 'post.php?post=' . get_option( 'page_on_front' ) . '&action=edit' ), 'Edit the home page: normal blocks; tags, notes and tiles are edited in the sidebar of each block' ),
					array( admin_url( 'site-editor.php' ), 'Site editor: styles, header, footer, templates' ),
				);
				echo '<ol>';
				foreach ( $items as $item ) {
					printf( '<li><a href="%s">%s</a></li>', esc_url( $item[0] ), esc_html( $item[1] ) );
				}
				echo '</ol>';
			}
		);
	}
);
