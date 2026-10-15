<?php
/**
 * Brew-method tiles. Each line of "tiles": name | tint | brewer (pourover, aeropress, chemex, frenchpress, espresso).
 * "link" is a path on this site, so it keeps working when the site moves.
 *
 * @var array $attributes Block attributes.
 * @package PomeloBlocks
 */

defined( 'ABSPATH' ) || exit;

$a    = $attributes;
$link = home_url( '/' . ltrim( (string) ( $a['link'] ?? '/shop/' ), '/' ) );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'pm-tiles' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<?php
	foreach ( pmb_lines( $a['tiles'] ?? '' ) as $i => $line ) {
		list( $name, $tint, $brewer ) = pmb_cells( $line, 3 );
		printf(
			'<a class="btile" href="%1$s" style="--t:%2$s">%3$s<span class="go" aria-hidden="true"><svg viewBox="0 0 14 12" focusable="false"><path d="M0 6h12M8 2l4 4-4 4"/></svg></span><span class="bname">%4$s</span></a>',
			esc_url( $link ),
			esc_attr( pmb_tint( $tint ) ),
			pmb_brewer( $brewer, $brewer . $i ), // phpcs:ignore WordPress.Security.EscapeOutput
			esc_html( $name )
		);
	}
	?>
</div>
