/**
 * Editor side of the Pomelo Coffee Roasters blocks. No build step: plain JavaScript using WordPress's globals.
 * Text is edited in the sidebar; the preview is drawn by the same PHP as the live site.
 * Each block lists its fields: [ attribute, label, kind, help ] where kind is "line" (one line)
 * or "area" (one entry per line) or "number".
 */
( function ( wp ) {
	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var Disabled = wp.components.Disabled;
	var ServerSideRender = wp.serverSideRender;
	var t = 'pomelo-blocks';

	function field( props, f ) {
		var key = f[ 0 ];
		var Control = 'area' === f[ 2 ] ? TextareaControl : TextControl;
		return el( Control, {
			key: key,
			label: f[ 1 ],
			help: f[ 3 ],
			type: 'number' === f[ 2 ] ? 'number' : undefined,
			rows: 'area' === f[ 2 ] ? 6 : undefined,
			value: props.attributes[ key ],
			onChange: function ( v ) {
				var o = {};
				o[ key ] = 'number' === f[ 2 ] ? ( parseFloat( v ) || 0 ) : v;
				props.setAttributes( o );
			},
		} );
	}

	function block( name, panels ) {
		wp.blocks.registerBlockType( name, {
			edit: function ( props ) {
				var sidebar = panels.map( function ( p, i ) {
					return el( PanelBody, { key: i, title: p[ 0 ], initialOpen: 0 === i }, p[ 1 ].map( function ( f ) { return field( props, f ); } ) );
				} );
				return el(
					'div',
					useBlockProps(),
					el( InspectorControls, null, sidebar ),
					el( Disabled, null, el( ServerSideRender, { block: name, attributes: props.attributes } ) )
				);
			},
			save: function () { return null; },
		} );
	}

	var tintHelp = __( 'Colours: sage, butter, sky, sand, peach, lilac.', t );

	block( 'pomelo/hero-chips', [
		[ __( 'Photo and tags', t ), [
			[ 'alt', __( 'Photo description (alt text)', t ), 'line' ],
			[ 'chips', __( 'Tasting-note tags', t ), 'area', __( 'One per line: note | origin | colour | left % | top %. ', t ) + tintHelp ],
		] ],
	] );

	block( 'pomelo/notes-ticker', [
		[ __( 'Tasting notes', t ), [
			[ 'label', __( 'Label', t ), 'line' ],
			[ 'notes', __( 'Notes', t ), 'area', __( 'One per line: note | short description | icon. Icons: pistachio, jasmine, lemon, apple, chocolate, cane.', t ) ],
		] ],
	] );

	block( 'pomelo/origin-cards', [
		[ __( 'Coffee cards', t ), [
			[ 'count', __( 'How many coffees', t ), 'number', __( 'Products come from Products in the dashboard. Each product\'s card colour is set in its "Card colour" box.', t ) ],
			[ 'button', __( 'Button', t ), 'line' ],
			[ 'empty', __( 'Text when there are no products', t ), 'line' ],
		] ],
	] );

	block( 'pomelo/sub-loop', [
		[ __( 'Subscription loop', t ), [
			[ 'caption', __( 'Caption', t ), 'line' ],
			[ 'days', __( 'Word after the number', t ), 'line' ],
			[ 'options', __( 'Rhythm buttons', t ), 'area', __( 'One per line: label | days.', t ) ],
			[ 'groupLabel', __( 'Buttons label (screen readers)', t ), 'line' ],
		] ],
	] );

	block( 'pomelo/brew-tiles', [
		[ __( 'Brew tiles', t ), [
			[ 'tiles', __( 'Tiles', t ), 'area', __( 'One per line: name | colour | brewer. Brewers: pourover, aeropress, chemex, frenchpress, espresso. ', t ) + tintHelp ],
			[ 'link', __( 'Where the tiles link (a path on this site)', t ), 'line' ],
		] ],
	] );

	block( 'pomelo/signup', [
		[ __( 'Newsletter field', t ), [
			[ 'label', __( 'Email label', t ), 'line' ],
			[ 'button', __( 'Button', t ), 'line' ],
			[ 'error', __( 'Message when the address is missing or wrong', t ), 'line' ],
			[ 'thanks', __( 'Thank-you line', t ), 'area' ],
		] ],
	] );
} )( window.wp );
