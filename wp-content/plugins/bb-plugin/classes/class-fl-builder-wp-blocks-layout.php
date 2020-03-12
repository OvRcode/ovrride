<?php

/**
 * Beaver Builder layout block for the new editor.
 *
 * @since 2.1
 */
final class FLBuilderWPBlocksLayout {

	/**
	 * @since 2.1
	 * @return void
	 */
	static public function init() {
		// Actions
		add_action( 'current_screen', __CLASS__ . '::init_template' );
		add_action( 'pre_post_update', __CLASS__ . '::disable_builder_on_post_update', 10, 2 );

		// Filters
		add_action( 'block_editor_preload_paths', __CLASS__ . '::update_legacy_post', 10, 2 );
		add_filter( 'fl_builder_editor_content', __CLASS__ . '::filter_editor_content' );
		add_filter( 'fl_builder_migrated_post_content', __CLASS__ . '::filter_migrated_post_content' );
	}

	/**
	 * Initialize a template for empty posts that have
	 * the builder enabled for them.
	 *
	 * @since 2.1
	 * @return void
	 */
	static public function init_template() {
		global $pagenow;

		if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			$post_id      = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : null;
			$render_ui    = apply_filters( 'fl_builder_render_admin_edit_ui', true );
			$post_types   = FLBuilderModel::get_post_types();
			$screen       = get_current_screen();
			$enabled      = ! $post_id ? false : FLBuilderModel::is_builder_enabled( $post_id );
			$user_access  = FLBuilderUserAccess::current_user_can( 'builder_access' );
			$unrestricted = FLBuilderUserAccess::current_user_can( 'unrestricted_editing' );

			if ( $render_ui && in_array( $screen->post_type, $post_types ) ) {
				$post_type = get_post_type_object( $screen->post_type );

				if ( $post_type && ( $enabled || ( $user_access && $unrestricted ) ) ) {
					$post_type->template = array(
						array( 'fl-builder/layout' ),
					);

					if ( ! $user_access || ! $unrestricted ) {
						$post_type->template_lock = 'all';
					}
				}
			}
		}
	}

	/**
	 * Updates posts being edited in the admin that we're built
	 * using Beaver Builder before WordPress blocks existed.
	 *
	 * We do this on the `block_editor_preload_paths` filter because
	 * that is the earliest we can hook into updating the post before
	 * it is preloaded by the REST API.
	 *
	 * @since 2.1
	 * @param array $paths
	 * @param object $post
	 * @return array
	 */
	static public function update_legacy_post( $paths, $post ) {
		if ( is_object( $post ) ) {
			$enabled = FLBuilderModel::is_builder_enabled( $post->ID );
			$blocks  = preg_match( '/<!-- wp:(.*) \/?-->/', $post->post_content );

			if ( $enabled && ! $blocks ) {
				$block  = '<!-- wp:fl-builder/layout -->';
				$block .= self::remove_broken_p_tags( $post->post_content );
				$block .= '<!-- /wp:fl-builder/layout -->';

				$post->post_content = $block;

				wp_update_post( array(
					'ID'           => $post->ID,
					'post_content' => $block,
				) );
			}
		}

		return $paths;
	}

	/**
	 * Disable the builder if old post content has a l
	 * ayout block but the new post content doesn't.
	 *
	 * @since 2.1
	 * @return void
	 */
	static public function disable_builder_on_post_update( $post_id, $new_post ) {
		$new_post   = (object) $new_post;
		$old_post   = get_post( $post_id );
		$post_types = FLBuilderModel::get_post_types();

		if ( ! $old_post || ! in_array( $old_post->post_type, $post_types ) ) {
			return;
		}

		$old_layout = preg_match( '/<!-- wp:fl-builder\/layout \/?-->/', $old_post->post_content );
		$new_layout = preg_match( '/<!-- wp:fl-builder\/layout \/?-->/', $new_post->post_content );

		if ( $old_layout && ! $new_layout ) {
			update_post_meta( $post_id, '_fl_builder_enabled', false );
		}
	}

	/**
	 * Filters the content saved back to the post editor when a builder
	 * layout is published and wraps it in our layout block. If our block
	 * exists in the post content then it will be replaced with this block.
	 * Otherwise, the entire post content will be replaced.
	 *
	 * @since 2.1
	 * @param string $content
	 * @return string
	 */
	static public function filter_editor_content( $content ) {
		$post_id = FLBuilderModel::get_post_id();
		$post    = get_post( $post_id );

		$block  = '<!-- wp:fl-builder/layout -->';
		$block .= self::remove_broken_p_tags( $content );
		$block .= '<!-- /wp:fl-builder/layout -->';

		return $block;
	}

	/**
	 * Removes the builder layout block from migrated post content.
	 *
	 * @since 2.1
	 * @param string $content
	 * @return string
	 */
	static public function filter_migrated_post_content( $content ) {
		$content = preg_replace( '/<!--\s\/?wp(.|\s)*?-->/', '', $content );
		return $content;
	}

	/**
	 * Removes unclosed or unopened paragraph tags caused by a bug
	 * in wpautop. If we don't remove those here, Gutenberg will
	 * think our layout block has an error.
	 *
	 * See: https://core.trac.wordpress.org/ticket/43100
	 *
	 * @since 2.1
	 * @param string $content
	 * @return string
	 */
	static public function remove_broken_p_tags( $content ) {
		$content = preg_replace( '/<p>(.*)<\/p>/i', '<fl-p-placeholder>$1</fl-p-placeholder>', $content );
		$content = preg_replace( '/<\/?p[^>]*\>/i', '', $content );
		$content = preg_replace( '/fl-p-placeholder/i', 'p', $content );
		return $content;
	}
}

FLBuilderWPBlocksLayout::init();
