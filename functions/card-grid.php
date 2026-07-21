<?php
/**
 * Shared server-rendered fellow card grid — used by archive-fellow.php (all
 * fellows, no-JS/SEO baseline) and taxonomy.php (single-facet fallback).
 * Matches the live site's plain image+name+year card exactly. Ported
 * unchanged from the dlf theme for Sprint K2.
 *
 * @param WP_Query|WP_Post[] $query_or_posts A WP_Query, or an array of posts.
 */
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
			$years = wp_get_post_terms( $post->ID, 'fellowship_year', array( 'fields' => 'names' ) );
			?>
			<a class="dlf-fellow-card" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
				<span class="dlf-fellow-card__image">
					<?php echo get_the_post_thumbnail( $post, 'fellow-card' ); ?>
				</span>
				<span class="dlf-fellow-card__name"><?php echo esc_html( get_the_title( $post ) ); ?></span>
				<span class="dlf-fellow-card__year"><?php echo esc_html( $years[0] ?? '' ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
}
