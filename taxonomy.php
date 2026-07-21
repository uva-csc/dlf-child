<?php
/**
 * Single-facet fallback — /year|region|country/<term>/. Server-rendered,
 * no JS required: the real, crawlable destination for every single-facet
 * link (nav, single-fellow meta chips), keeping the JS-only combined-facet
 * experience confined to /fellows/ itself.
 *
 * Ported from the dlf theme for Sprint K2 -- get_header()/get_footer() now
 * render Kadence's Header/Footer Builder output instead of our own
 * parts/header.php, so the extra header markup inside the hero is gone.
 */
get_header();

$term = get_queried_object();
$taxonomy_obj = get_taxonomy( $term->taxonomy );
$label = $taxonomy_obj ? $taxonomy_obj->labels->singular_name : '';
?>

<div class="dlf-hero dlf-hero--page">
	<img class="dlf-hero__img"
		src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/fellows-banner.jpg' ); ?>"
		alt="" aria-hidden="true">
	<h1 class="dlf-hero__title"><?php echo esc_html( $label . ': ' . $term->name ); ?></h1>
</div>

<main class="dlf-archive-main">
	<p><a href="<?php echo esc_url( get_post_type_archive_link( 'fellow' ) ); ?>">&larr; All Fellows</a></p>

	<?php
	$fellow_query = new WP_Query( array(
		'post_type'      => 'fellow',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
		'no_found_rows'  => true,
		'tax_query'      => array( array(
			'taxonomy' => $term->taxonomy,
			'field'    => 'term_id',
			'terms'    => $term->term_id,
		) ),
	) );
	dlf_render_fellow_card_grid( $fellow_query );
	wp_reset_postdata();
	?>
</main>

<?php get_footer(); ?>
