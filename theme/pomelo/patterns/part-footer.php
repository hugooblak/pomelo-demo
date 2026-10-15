<?php
/**
 * Title: Part: footer
 * Slug: pomelo/part-footer
 * Inserter: no
 *
 * Template part markup lives in a pattern so links use this site's own address.
 */

$pm_url = static function ( $path = '' ) {
	return esc_url( home_url( '/' . $path ) );
};
?>
<!-- wp:group {"className":"footer-in","layout":{"type":"default"}} -->
<div class="wp-block-group footer-in"><!-- wp:group {"className":"wide ftop","layout":{"type":"default"}} -->
<div class="wp-block-group wide ftop"><!-- wp:group {"className":"fsign","layout":{"type":"default"}} -->
<div class="wp-block-group fsign"><!-- wp:heading {"className":"fhead"} -->
<h2 class="wp-block-heading fhead">New origins and roast dates, once a month.</h2>
<!-- /wp:heading -->

<!-- wp:pomelo/signup /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"fcol","layout":{"type":"default"}} -->
<div class="wp-block-group fcol"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Shop</h3>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li><a href="<?php echo $pm_url( 'shop/' ); // phpcs:ignore ?>">All coffee</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo $pm_url( '#subscribe' ); // phpcs:ignore ?>">Subscriptions</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo $pm_url( '#brew' ); // phpcs:ignore ?>">By brew method</a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"fcol","layout":{"type":"default"}} -->
<div class="wp-block-group fcol"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Learn</h3>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li><a href="<?php echo $pm_url( '#brew' ); // phpcs:ignore ?>">Brew guides</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo $pm_url( '#shop' ); // phpcs:ignore ?>">Our origins</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo $pm_url( '#subscribe' ); // phpcs:ignore ?>">How subscriptions work</a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"fcol","layout":{"type":"default"}} -->
<div class="wp-block-group fcol"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Your order</h3>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li><a href="<?php echo $pm_url( 'cart/' ); // phpcs:ignore ?>">Cart</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo $pm_url( 'checkout/' ); // phpcs:ignore ?>">Checkout</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo $pm_url( 'my-account/' ); // phpcs:ignore ?>">Your account</a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"wide fbase","layout":{"type":"default"}} -->
<div class="wp-block-group wide fbase"><!-- wp:paragraph -->
<p>© 2026 Pomelo Coffee Roasters</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Pomelo Coffee Roasters is a fictional company. Concept design by Sunside.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
