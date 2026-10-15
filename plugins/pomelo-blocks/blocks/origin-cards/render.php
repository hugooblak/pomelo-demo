<?php
/**
 * Coffee cards from the WooCommerce products. The add-to-cart link works without JavaScript
 * (?add-to-cart=ID); with JavaScript blocks.js adds it through WooCommerce's cart store, the same store
 * the header cart reads, so the badge and the cart drawer update at once.
 * Each product's colour is the tint saved on the product (meta _pm_tint).
 *
 * @var array $attributes Block attributes.
 * @package PomeloBlocks
 */

defined( 'ABSPATH' ) || exit;

$a = $attributes;

if ( ! function_exists( 'wc_get_products' ) ) {
	printf( '<div %s><p class="pm-empty">%s</p></div>', get_block_wrapper_attributes( array( 'class' => 'pm-cards' ) ), esc_html( $a['empty'] ?? '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	return;
}

$products = wc_get_products(
	array(
		'status'  => 'publish',
		'limit'   => max( 1, (int) ( $a['count'] ?? 4 ) ),
		'orderby' => 'menu_order',
		'order'   => 'ASC',
	)
);

if ( ! $products ) {
	printf( '<div %s><p class="pm-empty">%s</p></div>', get_block_wrapper_attributes( array( 'class' => 'pm-cards' ) ), esc_html( $a['empty'] ?? '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	return;
}

?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'pm-cards' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<?php
	foreach ( $products as $p ) {
		$tint  = pmb_tint( (string) $p->get_meta( '_pm_tint' ) );
		$name  = $p->get_name();
		$image = $p->get_image_id() ? wp_get_attachment_image( $p->get_image_id(), 'large', false, array( 'loading' => 'lazy', 'alt' => $name . ' coffee bag' ) ) : '';
		?>
		<article class="pm-card" style="--t:<?php echo esc_attr( $tint ); ?>">
			<div class="tile">
				<svg class="wave" viewBox="0 0 240 22" preserveAspectRatio="none" aria-hidden="true" focusable="false"><path d="M0 22V10c20-8 40 8 60 0s40-8 60 0 40 8 60 0 40-8 60 0v12z"/></svg>
				<div class="pour"><?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			</div>
			<div class="body">
				<h3><a href="<?php echo esc_url( $p->get_permalink() ); ?>"><?php echo esc_html( $name ); ?></a></h3>
				<p class="notes"><?php echo esc_html( wp_strip_all_tags( $p->get_short_description() ) ); ?></p>
				<div class="foot">
					<span class="price"><?php echo wp_kses_post( $p->get_price_html() ); ?></span>
					<a href="<?php echo esc_url( $p->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo (int) $p->get_id(); ?>" class="pm-add" rel="nofollow" aria-label="<?php echo esc_attr( ( $a['button'] ?? '' ) . ': ' . $name ); ?>"><?php echo esc_html( $a['button'] ?? '' ); ?></a>
				</div>
			</div>
		</article>
		<?php
	}
	?>
</div>
