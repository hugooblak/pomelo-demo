<?php
/**
 * Title: Page: Home
 * Slug: pomelo/page-home
 * Categories: pomelo-pages
 * Block Types: core/post-content
 * Post Types: page
 * Description: The shop's home page: hero with the three bags, tasting-notes ticker, the coffees, the subscription band and the brew-method tiles.
 */

$pm_url = static function ( $path = '' ) {
	return esc_url( home_url( '/' . $path ) );
};
?>
<!-- wp:group {"tagName":"section","className":"pm-hero","layout":{"type":"default"}} -->
<section class="wp-block-group pm-hero"><!-- wp:group {"className":"wide hgrid","layout":{"type":"default"}} -->
<div class="wp-block-group wide hgrid"><!-- wp:group {"className":"hero-copy","layout":{"type":"default"}} -->
<div class="wp-block-group hero-copy"><!-- wp:heading {"level":1,"fontSize":"xx-large"} -->
<h1 class="wp-block-heading has-xx-large-font-size">Different origins. Distinct flavors.</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"hero-sub","fontSize":"large"} -->
<p class="hero-sub has-large-font-size">Every origin has its own character. Every color tells its story.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"pill-cream"} -->
<div class="wp-block-button pill-cream"><a class="wp-block-button__link wp-element-button" href="<?php echo $pm_url( 'shop/' ); // phpcs:ignore ?>">Shop coffee</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:pomelo/hero-chips /--></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:pomelo/notes-ticker {"align":"full"} /-->

<!-- wp:group {"tagName":"section","anchor":"shop","className":"pm-shop","layout":{"type":"default"}} -->
<section id="shop" class="wp-block-group pm-shop"><!-- wp:group {"className":"wide","layout":{"type":"default"}} -->
<div class="wp-block-group wide"><!-- wp:group {"className":"shop-head","layout":{"type":"default"}} -->
<div class="wp-block-group shop-head"><!-- wp:heading {"className":"eyebrow"} -->
<h2 class="wp-block-heading eyebrow">Roasting this week</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size">Whole bean, 250 g bags. Ground to order on request.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:pomelo/origin-cards /--></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","anchor":"subscribe","className":"pm-sub","layout":{"type":"default"}} -->
<section id="subscribe" class="wp-block-group pm-sub"><!-- wp:group {"className":"wide subgrid","layout":{"type":"default"}} -->
<div class="wp-block-group wide subgrid"><!-- wp:pomelo/sub-loop /-->

<!-- wp:group {"className":"sub-copy","layout":{"type":"default"}} -->
<div class="wp-block-group sub-copy"><!-- wp:heading {"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size">Better coffee, on repeat.</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">Pick a coffee, pick a rhythm: a fresh bag every two or four weeks.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"sub-act","layout":{"type":"default"}} -->
<div class="wp-block-group sub-act"><!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"pill-cream"} -->
<div class="wp-block-button pill-cream"><a class="wp-block-button__link wp-element-button" href="<?php echo $pm_url( 'shop/' ); // phpcs:ignore ?>">Start a subscription</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:paragraph {"className":"arrow-link"} -->
<p class="arrow-link"><a href="<?php echo $pm_url( '#brew' ); // phpcs:ignore ?>">Learn more</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","anchor":"brew","className":"pm-brew","layout":{"type":"default"}} -->
<section id="brew" class="wp-block-group pm-brew"><!-- wp:group {"className":"wide brewgrid","layout":{"type":"default"}} -->
<div class="wp-block-group wide brewgrid"><!-- wp:group {"className":"brew-intro","layout":{"type":"default"}} -->
<div class="wp-block-group brew-intro"><!-- wp:heading -->
<h2 class="wp-block-heading">Brew it your way</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size">Grind settings and recipes for every method, printed on the back of each bag.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:pomelo/brew-tiles /--></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
