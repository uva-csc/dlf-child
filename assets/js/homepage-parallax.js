/**
 * Homepage silhouette photo: fixed-background reveal.
 *
 * The silhouette band uses CSS `background-attachment: fixed` (see the
 * `.dlf-content-photo--silhouette` rules in assets/css/site.css) to keep the
 * photo anchored to the viewport while its section scrolls over it. That
 * needs the image as a CSS background, but the photo is an editor-managed
 * Kadence Image block. Rather than hardcode a URL that would go stale if an
 * editor swaps the photo, this copies the <img>'s own resolved src into the
 * `--dlf-silhouette-bg` custom property and flips on the `.is-fixed-bg` class
 * that activates the CSS. Progressive enhancement: until (or unless) this
 * runs, the plain <img> just renders as an ordinary full-bleed band.
 */
( function () {
	function activate( row ) {
		var img = row.querySelector( 'img' );
		if ( ! img ) {
			return;
		}
		var src = img.currentSrc || img.src;
		if ( ! src ) {
			return;
		}
		// The Kadence column wrapper is the visible window the CSS styles;
		// fall back to the row itself if the markup ever changes.
		var win = row.querySelector( '.kt-inside-inner-col' ) || row;
		win.style.setProperty( '--dlf-silhouette-bg', 'url("' + src + '")' );
		row.classList.add( 'is-fixed-bg' );
	}

	function init() {
		var rows = document.querySelectorAll( '.dlf-content-photo--silhouette' );
		Array.prototype.forEach.call( rows, function ( row ) {
			var img = row.querySelector( 'img' );
			if ( img && ! img.complete ) {
				// currentSrc isn't reliable until the image has loaded.
				img.addEventListener( 'load', function () {
					activate( row );
				}, { once: true } );
			} else {
				activate( row );
			}
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
