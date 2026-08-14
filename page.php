<?php
/**
 * Generic page template (about, apply, the-fellowship, donate, contact-us).
 * Ported from the dlf theme for Sprint K1. get_header()/get_footer() render
 * Kadence's Header/Footer Builder output; the transparent-nav-over-hero look
 * is Kadence's "Transparent Header" (enabled per-view -- see functions.php's
 * kadence_post_layout filter for donate/contact-us, Customizer for the rest).
 *
 * Two hero styles:
 *  - Most pages get a full-height sticky hero with scroll-drift parallax
 *    (.dlf-hero--home), hero photo from the page's featured image.
 *  - Donate + Contact match the live site's smaller SquareSpace page-banner:
 *    a short, static banner (.dlf-hero--page, no sticky, no parallax), the
 *    same variant the fellows archive/single pages use.
 */
get_header();

while ( have_posts() ) :
	the_post();
	$hero_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	if ( ! $hero_image ) {
		$hero_image = get_stylesheet_directory_uri() . '/assets/images/fellows-banner.jpg';
	}

	// Hero style is a per-page editor setting ("Page Hero" metabox, stored in
	// post meta -- see functions/page-hero.php). "short" renders the static
	// banner used on Donate/Contact (matching the live site); the default
	// "tall" renders the full-height sticky parallax hero below.
	$slug       = get_post_field( 'post_name' );
	$short_hero = dlf_page_hero_is_short( get_the_ID() );

	if ( $short_hero ) :
		// Per-page class (e.g. dlf-hero--banner-contact-us) lets site.css tune
		// the crop position per banner image without touching the others.
		?>

		<div class="dlf-hero dlf-hero--page dlf-hero--banner dlf-hero--banner-<?php echo esc_attr( $slug ); ?>">
			<img class="dlf-hero__img" src="<?php echo esc_url( $hero_image ); ?>" alt="" aria-hidden="true">
			<h1 class="dlf-hero__title"><?php the_title(); ?></h1>
		</div>

		<main class="dlf-front-panel dlf-front-panel--flat">
			<div class="dlf-plain-page__inner">
				<?php the_content(); ?>
			</div>
		</main>

	<?php else : ?>

		<div class="dlf-hero-scroll">
			<?php // Per-page class (e.g. dlf-hero--home-about), same idea as the
			      // banner branch above: lets site.css tune one page's crop or
			      // drift without touching the other tall heroes. ?>
			<div class="dlf-hero dlf-hero--home dlf-hero--home-<?php echo esc_attr( $slug ); ?>">
				<img class="dlf-hero__img" src="<?php echo esc_url( $hero_image ); ?>" alt="" aria-hidden="true">
				<h1 class="dlf-hero__title"><?php the_title(); ?></h1>
			</div>

			<main class="dlf-front-panel">
				<div class="dlf-plain-page__inner">
					<?php the_content(); ?>
				</div>
			</main>
		</div>

	<?php endif; ?>

<?php endwhile; ?>

<?php get_footer(); ?>
