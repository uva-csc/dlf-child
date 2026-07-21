<?php
/**
 * Single fellow profile page. Matches the live site's blog-post-style
 * single-fellow page as closely as possible: full-bleed photo-hero banner
 * using the fellow's own headshot (darkened), bold uppercase Futura PT name
 * centered, then plain content sections below on white, in the same order
 * the live modal/page already uses (Leadership Vision if present →
 * Project Description always → Learn More links if present).
 *
 * Approved minor additions (Than, 2026-07-17): a small breadcrumb and
 * year/region/country meta chips linking to the taxonomy.php archives --
 * styled unobtrusively, not a page redesign.
 *
 * Ported from the dlf theme for Sprint K2 -- get_header()/get_footer() now
 * render Kadence's Header/Footer Builder output instead of our own
 * parts/header.php, so the extra header markup inside the hero is gone.
 */
get_header();

while ( have_posts() ) :
	the_post();

	$years     = wp_get_post_terms( get_the_ID(), 'fellowship_year' );
	$regions   = wp_get_post_terms( get_the_ID(), 'region' );
	$countries = wp_get_post_terms( get_the_ID(), 'country' );

	$leadership_vision   = get_post_meta( get_the_ID(), 'leadership_vision', true );
	$project_description = get_post_meta( get_the_ID(), 'project_description', true );
	$learn_more           = get_post_meta( get_the_ID(), 'learn_more', true );
	if ( ! is_array( $learn_more ) ) {
		$learn_more = array();
	}

	$hero_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	if ( ! $hero_image ) {
		$hero_image = get_stylesheet_directory_uri() . '/assets/images/fellows-banner.jpg';
	}
	?>

	<div class="dlf-hero dlf-hero--page">
		<img class="dlf-hero__img" src="<?php echo esc_url( $hero_image ); ?>" alt="" aria-hidden="true">
		<h1 class="dlf-hero__title"><?php the_title(); ?></h1>
	</div>

	<main class="dlf-fellow-single">
		<p class="dlf-fellow-single__breadcrumb">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'fellow' ) ); ?>">&larr; All Fellows</a>
		</p>

		<div class="dlf-fellow-single__meta">
			<?php foreach ( $years as $term ) : ?>
				<a class="dlf-fellow-meta-chip" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
			<?php endforeach; ?>
			<?php foreach ( $regions as $term ) : ?>
				<a class="dlf-fellow-meta-chip" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
			<?php endforeach; ?>
			<?php foreach ( $countries as $term ) : ?>
				<a class="dlf-fellow-meta-chip" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
			<?php endforeach; ?>
		</div>

		<?php if ( $leadership_vision ) : ?>
			<section class="dlf-fellow-single__section">
				<h2 class="dlf-fellow-single__section-label">Leadership Vision</h2>
				<?php echo $leadership_vision; // phpcs:ignore -- trusted admin-authored HTML, same as post content. ?>
			</section>
		<?php endif; ?>

		<section class="dlf-fellow-single__section">
			<h2 class="dlf-fellow-single__section-label">Project Description</h2>
			<?php echo $project_description; // phpcs:ignore ?>
		</section>

		<?php if ( ! empty( $learn_more ) ) : ?>
			<section class="dlf-fellow-single__section">
				<h2 class="dlf-fellow-single__section-label">Learn More</h2>
				<div class="dlf-fellow-single__learn-more">
					<?php foreach ( $learn_more as $link ) : ?>
						<a class="dlf-btn-pill" href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $link['text'] ); ?></a>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>
	</main>

<?php endwhile; ?>

<?php get_footer(); ?>
