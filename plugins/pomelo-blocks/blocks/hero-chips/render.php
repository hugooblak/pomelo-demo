<?php
/**
 * Hero photo with tasting-note tags. Each line of "chips": note | origin | tint | left % | top %.
 *
 * @var array $attributes Block attributes.
 * @package PomeloBlocks
 */

defined( 'ABSPATH' ) || exit;

$a = $attributes;
?>
<figure <?php echo get_block_wrapper_attributes( array( 'class' => 'pm-hero-fig' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="shot">
		<img src="<?php echo esc_url( PMB_URL . 'assets/hero.webp' ); ?>" srcset="<?php echo esc_url( PMB_URL . 'assets/hero-900.webp' ); ?> 900w, <?php echo esc_url( PMB_URL . 'assets/hero.webp' ); ?> 1800w" sizes="(max-width: 900px) 100vw, 66vw" width="1800" height="934" alt="<?php echo esc_attr( $a['alt'] ?? '' ); ?>" fetchpriority="high">
		<?php
		foreach ( pmb_lines( $a['chips'] ?? '' ) as $i => $line ) {
			list( $note, $origin, $tint, $x, $y ) = pmb_cells( $line, 5 );
			printf(
				'<span class="chip c%1$d" aria-hidden="true" style="left:%2$s%%;top:%3$s%%;background:%4$s">%5$s<i>%6$s</i></span>',
				(int) $i + 1,
				esc_attr( (string) (float) $x ),
				esc_attr( (string) (float) $y ),
				esc_attr( pmb_tint( $tint ) ),
				esc_html( $note ),
				esc_html( $origin )
			);
		}
		?>
	</div>
</figure>
