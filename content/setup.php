<?php
/**
 * Demo site setup for Pomelo Coffee Roasters.
 *
 * On a brand-new install: sets up WooCommerce (US dollars, a fictional store address, no setup wizard,
 * no "coming soon" page, one flat shipping rate, one demo payment method that takes no money),
 * creates the four coffees with their photos and colours, the menu and the home page.
 * Safe to run again: it skips anything that already exists.
 *
 * Playground runs this automatically (see blueprint.json).
 * Local run:  wp eval-file content/setup.php
 *
 * @package Pomelo
 */

defined( 'ABSPATH' ) || exit;

wp_set_current_user( 1 ); // Admin, so block markup is saved exactly as written.

/**
 * Pattern markup with nested pattern references expanded, so saved pages hold plain editable blocks.
 *
 * @param string $slug Pattern slug.
 * @return string
 */
function pms_pattern( $slug ) {
	$pattern = WP_Block_Patterns_Registry::get_instance()->get_registered( $slug );
	if ( ! $pattern ) {
		pms_log( "Missing pattern: $slug" );
		return '';
	}
	return preg_replace_callback(
		'#<!-- wp:pattern \{"slug":"([^"]+)"\} /-->#',
		function ( $m ) {
			return pms_pattern( $m[1] );
		},
		$pattern['content']
	);
}

/**
 * Print a line when run from WP-CLI. Silent in Playground.
 *
 * @param string $msg Message.
 */
function pms_log( $msg ) {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::log( $msg );
	}
}

/**
 * Create a page unless one with this slug exists.
 *
 * @param string $slug     Page slug.
 * @param string $title    Title.
 * @param string $content  Block markup.
 * @param string $excerpt  Used as the meta description.
 * @param string $template Page template slug.
 * @return int Page ID.
 */
function pms_page( $slug, $title, $content, $excerpt, $template = '' ) {
	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		return $existing->ID;
	}
	$id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_name'    => $slug,
			'post_title'   => $title,
			'post_content' => $content,
			'post_excerpt' => $excerpt,
			'meta_input'   => $template ? array( '_wp_page_template' => $template ) : array(),
		)
	);
	pms_log( "Page: $title" );
	return $id;
}

/**
 * Copy an image from the blocks plugin into the media library. Returns the attachment ID.
 *
 * @param string $file  File name in plugins/pomelo-blocks/assets.
 * @param string $title Title and alt text.
 * @return int Attachment ID, 0 on failure.
 */
function pms_image( $file, $title ) {
	$found = get_posts( array( 'post_type' => 'attachment', 'title' => $title, 'numberposts' => 1, 'fields' => 'ids', 'post_status' => 'inherit' ) );
	if ( $found ) {
		return (int) $found[0];
	}
	// Ask the blocks plugin where it lives: Playground names the folder after the GitHub path
	// (https-github.com-...-plugins-pomelo-blocks), so a hard-coded "pomelo-blocks" finds nothing.
	$src = ( defined( 'PMB_DIR' ) ? PMB_DIR : WP_PLUGIN_DIR . '/pomelo-blocks' ) . '/assets/' . $file;
	if ( ! file_exists( $src ) ) {
		pms_log( "Missing image: $src" );
		return 0;
	}
	$up = wp_upload_bits( $file, null, file_get_contents( $src ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	if ( ! empty( $up['error'] ) ) {
		pms_log( 'Upload failed: ' . $up['error'] );
		return 0;
	}
	$id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/webp',
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$up['file']
	);
	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $up['file'] ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $title );
	return (int) $id;
}

/* --------------------------------------------------------------------------
 * Site settings
 * ----------------------------------------------------------------------- */
update_option( 'blogname', 'Pomelo Coffee Roasters' );
update_option( 'blogdescription', 'Single-origin specialty coffee, one color per origin. A concept shop for a fictional company.' );
update_option( 'timezone_string', 'America/Los_Angeles' );
update_option( 'date_format', 'F j, Y' );
update_option( 'start_of_week', 0 );

// Remove WordPress's sample content.
foreach ( array( 'hello-world' => 'post', 'sample-page' => 'page' ) as $slug => $type ) {
	$sample = get_page_by_path( $slug, OBJECT, $type );
	if ( $sample ) {
		wp_delete_post( $sample->ID, true );
	}
}
$privacy_draft = get_page_by_path( 'privacy-policy' );
if ( $privacy_draft && 'draft' === $privacy_draft->post_status ) {
	wp_delete_post( $privacy_draft->ID, true );
}

/* --------------------------------------------------------------------------
 * WooCommerce
 * ----------------------------------------------------------------------- */
if ( class_exists( 'WooCommerce' ) ) {
	$options = array(
		'woocommerce_currency'                       => 'USD',
		'woocommerce_default_country'                => 'US:OR',
		'woocommerce_store_address'                  => '100 Example Street',
		'woocommerce_store_city'                     => 'Portland',
		'woocommerce_store_postcode'                 => '97201',
		'woocommerce_calc_taxes'                     => 'no',
		'woocommerce_weight_unit'                    => 'g',
		'woocommerce_coming_soon'                    => 'no',
		'woocommerce_store_pages_only'               => 'no',
		'woocommerce_task_list_hidden'               => 'yes',
		'woocommerce_task_list_complete'             => 'yes',
		'woocommerce_extended_task_list_hidden'      => 'yes',
		'woocommerce_show_marketplace_suggestions'   => 'no',
		'woocommerce_allow_tracking'                 => 'no',
		'woocommerce_enable_guest_checkout'          => 'yes',
		'woocommerce_enable_checkout_login_reminder' => 'no',
		'woocommerce_enable_reviews'                 => 'no',
		'woocommerce_demo_store'                     => 'no',
		'woocommerce_cart_redirect_after_add'        => 'no',
		'woocommerce_enable_ajax_add_to_cart'        => 'yes',
	);
	foreach ( $options as $key => $value ) {
		update_option( $key, $value );
	}
	update_option( 'woocommerce_onboarding_profile', array( 'skipped' => true, 'completed' => true ) );

	// Shop, cart, checkout and account pages (WooCommerce's own block versions).
	if ( class_exists( 'WC_Install' ) ) {
		WC_Install::create_pages();
	}

	// The one way to pay in this demo: an offline method that takes no money.
	update_option(
		'woocommerce_cheque_settings',
		array(
			'enabled'      => 'yes',
			'title'        => 'Demo payment (no money is taken)',
			'description'  => 'This is a demo shop for a fictional company. Your order is saved so you can see it in the dashboard, but no payment is taken and nothing ships.',
			'instructions' => 'Demo order: no payment was taken and nothing will ship.',
		)
	);
	foreach ( array( 'bacs', 'cod' ) as $gateway ) {
		update_option( 'woocommerce_' . $gateway . '_settings', array( 'enabled' => 'no' ) );
	}

	// One flat shipping rate everywhere.
	$rest = new WC_Shipping_Zone( 0 );
	if ( ! $rest->get_shipping_methods() ) {
		$instance = $rest->add_shipping_method( 'flat_rate' );
		update_option(
			'woocommerce_flat_rate_' . $instance . '_settings',
			array(
				'title'      => 'Standard shipping',
				'tax_status' => 'none',
				'cost'       => '6',
			)
		);
		pms_log( 'Shipping rate added.' );
	}

	// Product category.
	$cat = term_exists( 'coffee', 'product_cat' );
	if ( ! $cat ) {
		$cat = wp_insert_term( 'Coffee', 'product_cat', array( 'slug' => 'coffee', 'description' => 'Single-origin whole bean coffee.' ) );
	}
	$cat_id = is_array( $cat ) ? (int) $cat['term_id'] : 0;
	update_option( 'default_product_cat', $cat_id );

	// The four coffees. Prices are example prices for a fictional shop.
	$coffees = array(
		array( 'brazil-mantiqueira', 'Brazil Mantiqueira', '20', 'Pistachio, milk chocolate, cane sugar', 'sage', 'card-brazil.webp', 'Grown on small family farms in the Serra da Mantiqueira hills of Minas Gerais and dried in the sun on raised beds. A round, nutty cup that works as filter coffee and as espresso.' ),
		array( 'ethiopia-gedeb', 'Ethiopia Gedeb', '24', 'Jasmine, Meyer lemon, honey', 'butter', 'card-ethiopia.webp', 'A washed coffee from the highlands of Gedeb. Light and floral, best as pour over or Chemex.' ),
		array( 'colombia-cauca', 'Colombia Cauca', '22', 'Red apple, cocoa, caramel', 'sky', 'card-colombia.webp', 'From smallholder farms in the Cauca valley. Sweet and balanced, an easy everyday coffee for any brewer.' ),
		array( 'sumatra-lintong', 'Sumatra Lintong', '21', 'Baking spice, dark chocolate, walnut', 'sand', 'card-sumatra.webp', 'Wet-hulled the traditional way near Lake Toba. Heavy and spiced, good in a French press or with milk.' ),
	);
	foreach ( $coffees as $order => $c ) {
		list( $slug, $name, $price, $notes, $tint, $file, $about ) = $c;
		if ( get_page_by_path( $slug, OBJECT, 'product' ) ) {
			continue;
		}
		$p = new WC_Product_Simple();
		$p->set_name( $name );
		$p->set_slug( $slug );
		$p->set_status( 'publish' );
		$p->set_catalog_visibility( 'visible' );
		$p->set_regular_price( $price );
		$p->set_short_description( $notes );
		$p->set_description( $about . "\n\n250 g bag, whole bean. Roasted to order and shipped within a few days of roasting." );
		$p->set_sku( 'PM-' . strtoupper( substr( $slug, 0, 3 ) ) );
		$p->set_weight( '250' );
		$p->set_menu_order( $order );
		if ( $cat_id ) {
			$p->set_category_ids( array( $cat_id ) );
		}
		$img = pms_image( $file, $name . ' coffee bag' );
		if ( $img ) {
			$p->set_image_id( $img );
		}
		$p->update_meta_data( '_pm_tint', $tint );
		$p->save();
		pms_log( "Product: $name" );
	}
}

/* --------------------------------------------------------------------------
 * Main menu (Appearance > Editor > Navigation). Used by the header.
 * Created before the home page, so the header pattern can point at it.
 * ----------------------------------------------------------------------- */
if ( ! get_posts( array( 'post_type' => 'wp_navigation', 'title' => 'Main menu', 'numberposts' => 1, 'post_status' => 'publish' ) ) ) {
	$nav   = '';
	$items = array(
		array( 'Shop', 'shop/' ),
		array( 'Subscriptions', '#subscribe' ),
		array( 'Brew guides', '#brew' ),
		array( 'Origins', '#shop' ),
	);
	foreach ( $items as $item ) {
		$nav .= '<!-- wp:navigation-link ' . wp_json_encode(
			array(
				'label' => $item[0],
				'url'   => home_url( '/' . $item[1] ),
				'kind'  => 'custom',
			),
			JSON_UNESCAPED_SLASHES
		) . ' /-->';
	}
	wp_insert_post(
		array(
			'post_type'    => 'wp_navigation',
			'post_status'  => 'publish',
			'post_title'   => 'Main menu',
			'post_content' => $nav,
		)
	);
	pms_log( 'Menu created.' );
}

/* --------------------------------------------------------------------------
 * Home page
 * ----------------------------------------------------------------------- */
$home = pms_page(
	'home',
	'Pomelo Coffee Roasters',
	get_page_by_path( 'home' ) ? '' : pms_pattern( 'pomelo/page-home' ),
	'Single-origin specialty coffee from Pomelo Coffee Roasters, one color per origin. Shop whole bean coffee or set up a subscription. A concept shop for a fictional company.',
	'page-landing'
);

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home );

// Pretty links. set_permalink_structure() also updates the rules WordPress already loaded for
// this request; only saving the option would rebuild the old rules.
global $wp_rewrite;
$wp_rewrite->set_permalink_structure( '/%postname%/' );
update_option( 'woocommerce_permalinks', array( 'product_base' => 'product', 'category_base' => 'product-category', 'tag_base' => 'product-tag', 'attribute_base' => '' ) );
// Do not build the address rules here: in this setup step WooCommerce has not added its checkout
// endpoints yet, so /checkout/order-received/ would be missing and the thank-you page would redirect home.
// An empty option makes WordPress rebuild the rules on the first normal page view, with everything loaded.
delete_option( 'rewrite_rules' );

pms_log( 'Pomelo Coffee Roasters demo ready.' );
