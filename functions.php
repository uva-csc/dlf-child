<?php
/**
 * DLF (Kadence Child) theme setup.
 *
 * Chrome (header/footer/nav/colors/typography/buttons) is configured via
 * Kadence Global Styles + Header/Footer Builder in the Customizer, not code
 * -- see docs/decision-kadence-child-theme.md. This file only wires up
 * what Kadence doesn't provide: the Adobe Fonts kit, the carried-over
 * component stylesheet, the fellow-card thumbnail size, the shared card-grid
 * helper, and the fellows-archive facet JS (all ported in Sprint K2).
 */

require get_stylesheet_directory() . '/functions/card-grid.php';

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'dlf-kadence-parent',
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme( get_template() )->get( 'Version' )
	);

	// Real Adobe Fonts kit (Than's Creative Cloud account) — same
	// Proxima Nova / Futura PT the live site uses, not a free substitute.
	wp_enqueue_style(
		'dlf-fonts',
		'https://use.typekit.net/dkh7eln.css',
		array(),
		null
	);

	wp_enqueue_style(
		'dlf-site',
		get_stylesheet_directory_uri() . '/assets/css/site.css',
		array( 'dlf-kadence-parent', 'dlf-fonts' ),
		filemtime( get_stylesheet_directory() . '/assets/css/site.css' )
	);

	if ( is_post_type_archive( 'fellow' ) ) {
		wp_enqueue_script(
			'dlf-fellows-archive',
			get_stylesheet_directory_uri() . '/assets/js/fellows-archive.js',
			array(),
			filemtime( get_stylesheet_directory() . '/assets/js/fellows-archive.js' ),
			true
		);
		wp_localize_script( 'dlf-fellows-archive', 'dlfFellows', array(
			'restUrl' => esc_url_raw( rest_url( 'dlf/v1/fellows' ) ),
		) );
	}

	// Fixed-background reveal: syncs an editor-managed <img> src into the CSS
	// custom property that drives its background-attachment:fixed effect. Used
	// by the homepage silhouette (.dlf-content-photo--silhouette) and the
	// About page "Our Origin" mid-hero (.dlf-section-hero) -- so it loads on
	// the front page and on any static Page. See assets/js/homepage-parallax.js.
	if ( is_front_page() || is_page() ) {
		wp_enqueue_script(
			'dlf-homepage-parallax',
			get_stylesheet_directory_uri() . '/assets/js/homepage-parallax.js',
			array(),
			filemtime( get_stylesheet_directory() . '/assets/js/homepage-parallax.js' ),
			true
		);
	}
}, 20 );

add_action( 'after_setup_theme', function () {
	// Card grid thumbnail — keeps the archive from shipping full-res headshots.
	add_image_size( 'fellow-card', 320, 320, true );
} );

/**
 * Force Kadence's Transparent Header on the fellow views. Their .dlf-hero--page
 * banner (archive-fellow.php / single-fellow.php / taxonomy.php) is meant to
 * sit UNDER the header like the homepage and static pages -- so the banner
 * image runs full-bleed to the top and the white logo/nav overlay it -- not
 * below a solid white header bar (on which the white logo is invisible).
 * Kadence assembles the per-view layout (including the 'transparent' key) and
 * exposes it via the `kadence_post_layout` filter; the transparent-header
 * styling itself is already configured in the Customizer for the rest of the
 * site, so we only need to flip it on for these views. Done in code (not a
 * Customizer toggle) so it's version-controlled and can't silently drift.
 */
add_filter( 'kadence_post_layout', function ( $layout ) {
	if (
		is_post_type_archive( 'fellow' )
		|| is_singular( 'fellow' )
		|| is_tax( array( 'fellowship_year', 'region', 'country' ) )
	) {
		$layout['transparent'] = 'enable';
	}
	return $layout;
} );
