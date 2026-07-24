<?php
/**
 * Fellows archive (/fellows/). Server-rendered card grid (all published
 * fellows, no filtering) is the no-JS/SEO/accessibility baseline;
 * assets/js/fellows-archive.js progressively enhances it with client-side
 * facet filtering (fetches /wp-json/dlf/v1/fellows once) and a card-click
 * modal. Matches the live site's plain "Class"/"Region" native-select
 * filters + image/name/year cards; Country is the one approved addition
 * (Spike D taxonomy decision), same plain style.
 *
 * Ported from the dlf theme for Sprint K2 -- get_header()/get_footer() now
 * render Kadence's Header/Footer Builder output instead of our own
 * parts/header.php, so the extra header markup inside the hero (that the
 * old theme needed to overlay-transparent it) is gone.
 */
get_header();
?>

<div class="dlf-hero dlf-hero--page">
	<img class="dlf-hero__img"
		src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/fellows-banner.jpg' ); ?>"
		alt="" aria-hidden="true">
	<h1 class="dlf-hero__title">Dalai Lama Fellows</h1>
</div>

<main class="dlf-archive-main">
	<div class="dlf-facet-bar">
		<div class="dlf-facet">
			<label for="dlf-facet-year">Class</label>
			<select id="dlf-facet-year" data-facet="year">
				<option value="">All</option>
				<?php foreach ( get_terms( array( 'taxonomy' => 'fellowship_year', 'hide_empty' => true, 'orderby' => 'name', 'order' => 'DESC' ) ) as $term ) : ?>
					<option value="<?php echo esc_attr( $term->name ); ?>"><?php echo esc_html( $term->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="dlf-facet">
			<label for="dlf-facet-region">Region</label>
			<select id="dlf-facet-region" data-facet="region">
				<option value="">All</option>
				<?php foreach ( get_terms( array( 'taxonomy' => 'region', 'hide_empty' => true, 'orderby' => 'name' ) ) as $term ) : ?>
					<option value="<?php echo esc_attr( $term->name ); ?>"><?php echo esc_html( $term->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="dlf-facet">
			<label for="dlf-facet-country">Country</label>
			<select id="dlf-facet-country" data-facet="country">
				<option value="">All</option>
				<?php foreach ( get_terms( array( 'taxonomy' => 'country', 'hide_empty' => true, 'orderby' => 'name' ) ) as $term ) : ?>
					<option value="<?php echo esc_attr( $term->name ); ?>"><?php echo esc_html( $term->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="dlf-facet">
			<label for="dlf-facet-sort">Sort by</label>
			<select id="dlf-facet-sort" data-facet="sort">
				<option value="year">Class</option>
				<option value="lastname">Last name</option>
			</select>
		</div>
	</div>

	<div id="dlf-fellow-grid-container">
		<?php
		$fellow_query = new WP_Query( array(
			'post_type'      => 'fellow',
			'posts_per_page' => -1,
			'no_found_rows'  => true,
		) );
		// No-JS baseline sort: year (newest first) then last name -- matches
		// the default of the JS-enhanced view (fellows-archive.js). WP_Query
		// can't order by a derived "last name", so sort the loaded posts.
		$sorted = $fellow_query->posts;
		usort( $sorted, 'dlf_compare_fellows_year_then_name' );
		dlf_render_fellow_card_grid( $sorted );
		wp_reset_postdata();
		?>
	</div>
</main>

<dialog class="dlf-fellow-modal" id="dlf-fellow-modal">
	<button type="button" class="dlf-fellow-modal__close" id="dlf-fellow-modal-close" aria-label="Close">&times;</button>
	<div id="dlf-fellow-modal-content"></div>
</dialog>

<?php get_footer(); ?>
