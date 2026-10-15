<?php
/**
 * Title: Part: header
 * Slug: pomelo/part-header
 * Inserter: no
 *
 * Template part markup lives in a pattern so links use this site's own address.
 * The cart and account icons are WooCommerce's own blocks: the cart opens WooCommerce's real cart drawer.
 */

// Point the menu at the site's "Main menu" (created by the demo setup, editable in Appearance > Editor > Navigation).
$pm_menu = get_posts( array( 'post_type' => 'wp_navigation', 'title' => 'Main menu', 'numberposts' => 1, 'fields' => 'ids', 'post_status' => 'publish' ) );
$pm_ref  = $pm_menu ? '"ref":' . (int) $pm_menu[0] . ',' : '';
?>
<!-- wp:group {"className":"site-header","layout":{"type":"default"}} -->
<div class="wp-block-group site-header"><!-- wp:group {"className":"wide hbar","layout":{"type":"default"}} -->
<div class="wp-block-group wide hbar"><!-- wp:paragraph {"className":"logo"} -->
<p class="logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><strong>POMELO</strong><br>Coffee Roasters</a></p>
<!-- /wp:paragraph -->

<!-- wp:navigation {<?php echo $pm_ref; // phpcs:ignore ?>"overlayMenu":"mobile","className":"nav","layout":{"type":"flex","justifyContent":"center"}} /-->

<!-- wp:group {"className":"hr","layout":{"type":"default"}} -->
<div class="wp-block-group hr"><!-- wp:woocommerce/customer-account {"displayStyle":"icon_only","iconStyle":"line","iconClass":"wc-block-customer-account__account-icon"} /-->

<!-- wp:woocommerce/mini-cart {"addToCartBehaviour":"open_drawer","hasHiddenPrice":true} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
