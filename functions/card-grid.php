<?php
/**
 * Shared server-rendered fellow card grid — used by archive-fellow.php (all
 * fellows, no-JS/SEO baseline) and taxonomy.php (single-facet fallback).
 * Matches the live site's plain image+name+year card exactly. Ported
 * unchanged from the dlf theme for Sprint K2.
 *
 * @param WP_Query|WP_Post[] $query_or_posts A WP_Query, or an array of posts.
 */
/**
 * Best-effort "last name" for sorting: the final word of the title, after
 * dropping any trailing parenthetical (e.g. "(Mentorship Fellow '25)") and
 * quotes around a nickname (John "Alex" Pritz -> Pritz). Kept identical to
 * lastName() in assets/js/fellows-archive.js so the no-JS baseline and the
 * JS-enhanced view sort the same way.
 *
 * @param WP_Post $post
 * @return string
 */
function dlf_fellow_last_name( $post ) {
	$name = get_the_title( $post );
	$name = preg_replace( '/\s*\([^)]*\)/u', '', $name );      // drop "(...)"
	$name = preg_replace( '/["\'\x{2018}\x{2019}\x{201C}\x{201D}]/u', '', $name ); // drop quotes
	$parts = preg_split( '/\s+/', trim( $name ) );
	return $parts ? end( $parts ) : $name;
}

/**
 * Fellowship year (cohort) as an int, 0 if unset.
 *
 * @param WP_Post $post
 * @return int
 */
function dlf_fellow_year( $post ) {
	$years = wp_get_post_terms( $post->ID, 'fellowship_year', array( 'fields' => 'names' ) );
	return isset( $years[0] ) ? (int) $years[0] : 0;
}

/**
 * usort comparator: year newest-first, then last name A->Z. The default order
 * for the fellows archive (both render paths).
 */
function dlf_compare_fellows_year_then_name( $a, $b ) {
	$ya = dlf_fellow_year( $a );
	$yb = dlf_fellow_year( $b );
	if ( $ya !== $yb ) {
		return $yb <=> $ya; // newest first
	}
	return strcasecmp( dlf_fellow_last_name( $a ), dlf_fellow_last_name( $b ) );
}

function dlf_render_fellow_card_grid( $query_or_posts ) {
	$posts = $query_or_posts instanceof WP_Query ? $query_or_posts->posts : $query_or_posts;

	if ( empty( $posts ) ) {
		echo '<p class="dlf-no-fellows">No fellows found.</p>';
		return;
	}
	?>
	<div class="dlf-fellow-grid">
		<?php foreach ( $posts as $post ) : ?>
			<?php
			$years     = wp_get_post_terms( $post->ID, 'fellowship_year', array( 'fields' => 'names' ) );
			$countries = wp_get_post_terms( $post->ID, 'country', array( 'fields' => 'names' ) );
			$country_label = is_array( $countries ) ? implode( ', ', $countries ) : '';
			?>
			<a class="dlf-fellow-card" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
				<span class="dlf-fellow-card__image">
					<?php echo get_the_post_thumbnail( $post, 'fellow-card' ); ?>
				</span>
				<?php // get_the_title() is already HTML (wptexturize turns straight
				      // quotes into curly-quote entities); wrapping it in esc_html()
				      // would double-escape the & and print a literal "&#8220;". ?>
				<span class="dlf-fellow-card__name"><?php echo get_the_title( $post ); ?></span>
				<span class="dlf-fellow-card__year"><?php echo esc_html( $years[0] ?? '' ); ?></span>
				<?php if ( $country_label ) : ?>
					<span class="dlf-fellow-card__country"><?php echo esc_html( $country_label ); ?></span>
				<?php endif; ?>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
}
