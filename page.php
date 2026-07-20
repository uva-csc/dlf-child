<?php
/**
 * Generic page template (about, apply, the-fellowship, donate, contact-us).
 * Ported from the dlf theme for Sprint K1 -- every one of these pages has
 * its own full-height hero photo (sticky + scroll-drift), hero photo from
 * each page's featured image. get_header()/get_footer() now render
 * Kadence's Header/Footer Builder output instead of our own parts/header.php
 * -- the transparent-nav-over-hero look is a Kadence "Transparent Header"
 * Customizer setting (configured per-page), not markup here.
 */
get_header();

while ( have_posts() ) :
	the_post();
	$hero_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	if ( ! $hero_image ) {
		$hero_image = get_template_directory_uri() . '/assets/images/fellows-banner.jpg';
	}
	?>

	<div class="dlf-hero-scroll">
		<div class="dlf-hero dlf-hero--home">
			<img class="dlf-hero__img" src="<?php echo esc_url( $hero_image ); ?>" alt="" aria-hidden="true">
			<h1 class="dlf-hero__title"><?php the_title(); ?></h1>
		</div>

		<main class="dlf-front-panel">
			<div class="dlf-plain-page__inner">
				<?php the_content(); ?>
			</div>
		</main>
	</div>

<?php endwhile; ?>

<?php get_footer(); ?>
