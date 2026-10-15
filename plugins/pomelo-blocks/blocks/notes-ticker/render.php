<?php
/**
 * Tasting-notes ticker. Each line of "notes": name | description | icon.
 * The list is printed once; blocks.js copies it for the endless loop and marks the copy hidden from screen readers.
 *
 * @var array $attributes Block attributes.
 * @package PomeloBlocks
 */

defined( 'ABSPATH' ) || exit;

$a = $attributes;
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'pm-ticker' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> aria-label="<?php echo esc_attr( $a['label'] ?? '' ); ?>">
	<p class="tlabel"><?php echo esc_html( $a['label'] ?? '' ); ?></p>
	<div class="ttrack">
		<ul class="tset draw">
			<?php
			foreach ( pmb_lines( $a['notes'] ?? '' ) as $line ) {
				list( $name, $desc, $icon ) = pmb_cells( $line, 3 );
				printf(
					'<li class="tnote">%s<span><b>%s</b> %s</span></li>',
					pmb_note_icon( $icon ), // phpcs:ignore WordPress.Security.EscapeOutput
					esc_html( $name ),
					esc_html( $desc )
				);
			}
			?>
		</ul>
	</div>
</section>
