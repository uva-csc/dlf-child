/**
 * Fixed-background ("silhouette") reveal for editor-managed photos.
 *
 * Two sections use CSS `background-attachment: fixed` to hold a photo anchored
 * to the viewport while its section scrolls over it like a window:
 *   - the homepage silhouette band          (.dlf-content-photo--silhouette)
 *   - the About page "Our Origin" mid-hero   (.dlf-section-hero)
 *
 * Both need the photo as a CSS *background*, but each is an editor-managed
 * <img> (a Kadence Image block, or the raw section-hero block). Rather than
 * hardcode a URL that would silently go stale if an editor swaps the photo,
 * this copies each <img>'s own resolved src into a CSS custom property on the
 * "window" element and flips on the `.is-fixed-bg` class that activates the
 * CSS. Progressive enhancement: until (or unless) this runs, the plain <img>
 * renders as an ordinary band/hero -- and .dlf-section-hero additionally keeps
 * its pure-CSS view() drift as the no-JS fallback. See the matching rules in
 * assets/css/site.css (.dlf-content-photo--silhouette and .dlf-section-hero).
 */
( function () {
	// [ rowSelector, windowSelector | null (use the row itself), cssVar ]
	var targets = [
		[ '.dlf-content-photo--silhouette', '.kt-inside-inner-col', '--dlf-silhouette-bg' ],
		[ '.dlf-section-hero', null, '--dlf-section-hero-bg' ]
	];

	function activate( row, windowSelector, cssVar ) {
		var img = row.querySelector( 'img' );
		if ( ! img ) {
			return;
		}
		var src = img.currentSrc || img.src;
		if ( ! src ) {
			return;
		}
		// The visible "window" the CSS styles; fall back to the row itself if
		// the expected wrapper isn't present.
		var win = ( windowSelector && row.querySelector( windowSelector ) ) || row;
		win.style.setProperty( cssVar, 'url("' + src + '")' );
		row.classList.add( 'is-fixed-bg' );
	}

	function init() {
		targets.forEach( function ( target ) {
			var rows = document.querySelectorAll( target[ 0 ] );
			Array.prototype.forEach.call( rows, function ( row ) {
				var img = row.querySelector( 'img' );
				if ( img && ! img.complete ) {
					// currentSrc isn't reliable until the image has loaded.
					img.addEventListener( 'load', function () {
						activate( row, target[ 1 ], target[ 2 ] );
					}, { once: true } );
				} else {
					activate( row, target[ 1 ], target[ 2 ] );
				}
			} );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
