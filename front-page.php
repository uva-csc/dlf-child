<?php
/**
 * Homepage. Ported from the dlf theme for Sprint K1 -- renders the
 * "Reimagine Leadership" Page (Settings > Reading > homepage displays), so
 * hero title/image and the mission/quote/photo sections below are editable
 * in wp-admin like any other page. get_header()/get_footer() now render
 * Kadence's Header/Footer Builder output; the transparent-nav-over-hero
 * look is a Kadence "Transparent Header" Customizer setting, not markup
 * here. Hero mechanics: accessible sticky + scroll-driven-animation drift
 * (see docs/full-width-parallax-hero.md).
 */
get_header();

$front      = get_post( (int) get_option( 'page_on_front' ) );
$hero_image = get_the_post_thumbnail_url( $front, 'full' );
if ( ! $hero_image ) {
	$hero_image = get_stylesheet_directory_uri() . '/assets/images/fellows-banner.jpg';
}
?>

<div class="dlf-hero-scroll">
	<div class="dlf-hero dlf-hero--home">
		<img class="dlf-hero__img" src="<?php echo esc_url( $hero_image ); ?>" alt="" aria-hidden="true">
		<h1 class="dlf-hero__title"><?php echo get_the_title( $front ); ?></h1>
		<?php // Scroll cue: the chevron is drawn by CSS (.dlf-hero__cue::before) so
		      // that hovering can swap it for the label, the way the live site's
		      // .scroll-arrow does. Decorative -- the panel below is reachable by
		      // scrolling, so this is aria-hidden and not a control. ?>
		<span class="dlf-hero__cue" aria-hidden="true"><span class="dlf-hero__cue-label">Scroll Down</span></span>
	</div>

	<main class="dlf-front-panel">
		<?php echo apply_filters( 'the_content', $front->post_content ); ?>
	</main>
</div>

<?php get_footer(); ?>
