<?php
/**
 * Demo newsletter field. No action, no request: blocks.js swaps it for the thank-you line.
 *
 * @var array $attributes Block attributes.
 * @package PomeloBlocks
 */

defined( 'ABSPATH' ) || exit;

$a = $attributes;
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'pm-signup' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<form class="sform" novalidate>
		<label class="field"><span class="flabel"><?php echo esc_html( $a['label'] ?? '' ); ?></span><input type="email" name="email" required autocomplete="email" aria-describedby="pmb-signup-err"></label>
		<button class="pmbtn" type="submit"><?php echo esc_html( $a['button'] ?? '' ); ?></button>
	</form>
	<p class="ferr" id="pmb-signup-err" hidden><?php echo esc_html( $a['error'] ?? '' ); ?></p>
	<p class="thanks" role="status" hidden><svg viewBox="0 0 34 34" aria-hidden="true" focusable="false"><circle pathLength="1" cx="17" cy="17" r="15"/><path pathLength="1" d="M10 17.5l5 5 9-10"/></svg><?php echo esc_html( $a['thanks'] ?? '' ); ?></p>
</div>
