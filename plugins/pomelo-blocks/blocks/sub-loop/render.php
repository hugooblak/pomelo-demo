<?php
/**
 * The bean on its loop, plus the rhythm buttons. Each line of "options": label | days.
 * Without JavaScript the bean stands still at the front and the first rhythm is shown.
 *
 * @var array $attributes Block attributes.
 * @package PomeloBlocks
 */

defined( 'ABSPATH' ) || exit;

$a    = $attributes;
$opts = array_map(
	static function ( $l ) {
		return pmb_cells( $l, 2 );
	},
	pmb_lines( $a['options'] ?? '' )
);
$first = $opts ? (int) $opts[0][1] : 14;
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'pm-loop' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<svg class="loopsvg" viewBox="0 0 440 300" aria-hidden="true" focusable="false">
		<path class="track" d="M220 210m-180 0a180 56 0 1 0 360 0a180 56 0 1 0 -360 0"/>
		<g class="dropg" transform="translate(206 196)"><g class="dropbag"><path d="M3 4h22l-1 30H4z"/><path class="nf" d="M3 4l3-4h16l3 4"/><path class="nf" d="M9 14h10"/></g></g>
		<g class="beang" transform="translate(220 266) translate(-78 -170)"><g class="bean">
			<path class="legL" d="M64 136l-6 32h-13"/>
			<path class="legR" d="M92 136l6 32h13"/>
			<ellipse class="body" cx="78" cy="84" rx="40" ry="56" transform="rotate(-10 78 84)"/>
			<path d="M66 32c20 22-16 48 6 102"/>
			<path d="M40 92c-14 2-20 10-22 20"/>
			<path d="M8 108h20l-2 16H10z"/><path d="M28 112c6 0 6 8 0 8"/>
			<path class="steam" d="M14 102c-3-4 3-6 0-10M21 102c-3-4 3-6 0-10"/>
			<path d="M116 74c10-6 14-14 14-24"/><path d="M126 52l-4-12M131 51l4-12"/>
		</g></g>
	</svg>
	<p class="cap"><span><?php echo esc_html( $a['caption'] ?? '' ); ?></span> <b class="nextbag"><?php echo esc_html( $first . ' ' . ( $a['days'] ?? '' ) ); ?></b></p>
	<?php if ( $opts ) : ?>
		<div class="seg" role="group" aria-label="<?php echo esc_attr( $a['groupLabel'] ?? '' ); ?>" data-days-word="<?php echo esc_attr( $a['days'] ?? '' ); ?>">
			<?php foreach ( $opts as $i => $o ) : ?>
				<button type="button" data-days="<?php echo (int) $o[1]; ?>" aria-pressed="<?php echo 0 === $i ? 'true' : 'false'; ?>"><?php echo esc_html( $o[0] ); ?></button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
