/**
 * Pomelo Coffee Roasters theme script: the header turns cream once the page scrolls, and the phone
 * menu closes after a link is used. Everything that moves on the page lives in the Pomelo Blocks plugin.
 */
( () => {
	document.documentElement.classList.add( 'js' );
	const header = document.querySelector( 'header.wp-block-template-part' );
	if ( ! header ) {
		return;
	}
	const place = () => header.classList.toggle( 'stuck', scrollY > 60 );
	addEventListener( 'scroll', place, { passive: true } );
	place();
	header.addEventListener( 'click', ( e ) => {
		if ( e.target.closest( '.is-menu-open .wp-block-navigation-item__content' ) ) {
			header.querySelector( '.wp-block-navigation__responsive-container-close' )?.click();
		}
	} );
} )();
