<?php
/**
 * Pomelo Coffee Roasters theme functions.
 *
 * Kept short on purpose. Design lives in theme.json and style.css, markup in templates and
 * patterns. The hero tags, ticker, coffee cards, subscription loop, brew tiles and newsletter field
 * are blocks in the "Pomelo Coffee Roasters Blocks" plugin, so they survive a change of theme.
 * The shop itself (products, cart, checkout, orders) is WooCommerce.
 *
 * @package Pomelo
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_enqueue_scripts',
	function () {
		$version = wp_get_theme()->get( 'Version' );
		// Loaded after WordPress's global styles, so the theme's own rules win a tie.
		wp_enqueue_style( 'pomelo', get_stylesheet_uri(), array( 'global-styles' ), $version );
		wp_style_add_data( 'pomelo', 'path', get_theme_file_path( 'style.css' ) );
		wp_enqueue_script( 'pomelo', get_theme_file_uri( 'assets/js/main.js' ), array(), $version, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	},
	20
);
// The home page sells through the theme's own coffee cards, so WooCommerce's classic shop stylesheets
// (about 130 KB, all render-blocking) style nothing there. The shop, product, cart and checkout pages keep them.
add_action(
	'wp_enqueue_scripts',
	function () {
		if ( is_front_page() ) {
			foreach ( array( 'woocommerce-general', 'woocommerce-layout', 'woocommerce-smallscreen', 'woocommerce-blocktheme' ) as $handle ) {
				wp_dequeue_style( $handle );
			}
		}
	},
	99
);
// Print stylesheets up to 60 KB inline instead of as separate files: one fewer round trip before
// anything shows. WordPress's default limit is 20 KB.
add_filter(
	'styles_inline_size_limit',
	static function () {
		return 60000;
	}
);

add_action(
	'after_setup_theme',
	function () {
		add_editor_style( 'style.css' );
		add_theme_support( 'woocommerce' );
	}
);

/**
 * Preload the two fonts the first screen needs, and give the browser tab an icon.
 */
add_action(
	'wp_head',
	function () {
		foreach ( array( 'fraunces', 'dm-sans' ) as $font ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( get_theme_file_uri( 'assets/fonts/' . $font . '.woff2' ) )
			);
		}
		echo '<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 32 32\'%3E%3Ccircle cx=\'16\' cy=\'16\' r=\'16\' fill=\'%23AE4D32\'/%3E%3Cellipse cx=\'16\' cy=\'16\' rx=\'7\' ry=\'10\' fill=\'none\' stroke=\'%23FBEDD7\' stroke-width=\'2.4\'/%3E%3Cpath d=\'M14 7c5 5-4 12 1 18\' fill=\'none\' stroke=\'%23FBEDD7\' stroke-width=\'2\'/%3E%3C/svg%3E">' . "\n";
	},
	1
);

/**
 * Mark the page as having JavaScript before anything is drawn, so the animated blocks start in
 * their first state. Without JavaScript they show their finished state.
 */
add_action(
	'wp_head',
	function () {
		echo "<script>document.documentElement.classList.add('js')</script>\n";
	},
	0
);

/**
 * Remove WordPress's emoji script and styles. Browsers draw emoji on their own.
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

add_action(
	'init',
	function () {
		register_block_pattern_category( 'pomelo-pages', array( 'label' => __( 'Pomelo Coffee Roasters page layouts', 'pomelo' ) ) );
	}
);

/**
 * Basic meta description and share tags. Switches itself off when an SEO plugin is active.
 *
 * @return bool True when this theme is the one handling search engine tags.
 */
function pm_handles_seo() {
	return ! ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' ) || defined( 'AIOSEO_VERSION' ) );
}

add_action(
	'wp_head',
	function () {
		if ( ! pm_handles_seo() ) {
			return;
		}
		$description = '';
		if ( is_singular() ) {
			$description = get_the_excerpt();
		} elseif ( is_front_page() || is_home() || ( function_exists( 'is_shop' ) && is_shop() ) ) {
			$description = get_bloginfo( 'description' );
		}
		$description = trim( wp_strip_all_tags( (string) $description ) );
		$title       = is_front_page() ? get_bloginfo( 'name' ) : ( is_singular() ? get_the_title() : get_bloginfo( 'name' ) );
		$url         = is_singular() ? get_permalink() : home_url( '/' );

		if ( $description ) {
			printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
		}
		printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
		if ( $description ) {
			printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
		}
		printf( '<meta property="og:type" content="website">' . "\n" );
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( get_theme_file_uri( 'screenshot.jpg' ) ) );
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	},
	2
);
