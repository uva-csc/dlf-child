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
}, 20 );

add_action( 'after_setup_theme', function () {
	// Card grid thumbnail — keeps the archive from shipping full-res headshots.
	add_image_size( 'fellow-card', 320, 320, true );
} );
