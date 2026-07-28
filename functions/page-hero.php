<?php
/**
 * Per-page "Page Hero" style control.
 *
 * Lets an editor choose, per page, between the full-height sticky/parallax hero
 * (the default) and the short static banner used on Donate/Contact — with no
 * code edit and no central slug list. The choice is stored in the page's own
 * post meta `_dlf_hero_style` ('tall' | 'short').
 *
 *   - page.php reads it (via dlf_page_hero_is_short()) to pick the hero markup.
 *   - functions.php's kadence_post_layout filter reads it too, so a short-banner
 *     page gets Kadence's transparent header (white nav over the photo).
 *
 * A plain classic metabox (no build step) is used deliberately: it renders in
 * the sidebar of both the block and classic editors, and keeps this a
 * self-contained theme feature with no extra plugin dependency.
 */

const DLF_HERO_META = '_dlf_hero_style';

/**
 * True when the given page is set to the short banner hero. Used by page.php
 * and the transparent-header filter. Unset pages default to 'tall' (false).
 */
function dlf_page_hero_is_short( $post_id ) {
	return 'short' === get_post_meta( (int) $post_id, DLF_HERO_META, true );
}

// Register the meta so it's sanitized and carries a default. Kept out of REST
// (show_in_rest => false): the classic metabox below is the sole editor, so
// there's no block-editor control that would need it, and nothing else can
// race it on save.
add_action( 'init', function () {
	register_post_meta( 'page', DLF_HERO_META, array(
		'type'              => 'string',
		'single'            => true,
		'default'           => 'tall',
		'show_in_rest'      => false,
		'sanitize_callback' => function ( $value ) {
			return in_array( $value, array( 'tall', 'short' ), true ) ? $value : 'tall';
		},
		'auth_callback'     => function () {
			return current_user_can( 'edit_pages' );
		},
	) );
} );

add_action( 'add_meta_boxes', function () {
	add_meta_box(
		'dlf_page_hero',
		'Page Hero',
		'dlf_render_page_hero_metabox',
		'page',
		'side',
		'default'
	);
} );

function dlf_render_page_hero_metabox( $post ) {
	wp_nonce_field( 'dlf_save_page_hero', 'dlf_page_hero_nonce' );
	$value = get_post_meta( $post->ID, DLF_HERO_META, true );
	if ( '' === $value ) {
		$value = 'tall';
	}
	?>
	<p><label for="dlf-hero-style"><strong>Hero style</strong></label></p>
	<select name="dlf_hero_style" id="dlf-hero-style" style="width:100%">
		<option value="tall" <?php selected( $value, 'tall' ); ?>>Tall parallax hero (default)</option>
		<option value="short" <?php selected( $value, 'short' ); ?>>Short banner (like Donate)</option>
	</select>
	<p class="description">
		The banner photo comes from this page's Featured Image either way.
	</p>
	<?php
}

add_action( 'save_post_page', function ( $post_id ) {
	if (
		! isset( $_POST['dlf_page_hero_nonce'] )
		|| ! wp_verify_nonce( sanitize_key( $_POST['dlf_page_hero_nonce'] ), 'dlf_save_page_hero' )
	) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}
	$value = ( isset( $_POST['dlf_hero_style'] ) && 'short' === $_POST['dlf_hero_style'] ) ? 'short' : 'tall';
	update_post_meta( $post_id, DLF_HERO_META, $value );
} );
