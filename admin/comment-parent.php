<?php

namespace JoostBlog\WP\Comment\Admin;

/**
 * Comment parent handling class.
 */
class Comment_Parent {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		// The hooks for editing and saving the comment parent.
		\add_action( 'admin_menu', [ $this, 'load_comment_parent_box' ] );
		\add_action( 'edit_comment', [ $this, 'update_comment_parent' ] );
	}

	/**
	 * Shows the comment parent box where you can change the comment parent.
	 *
	 * @param object $comment The comment object.
	 */
	public function comment_parent_box( $comment ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Param used in included file.
		require_once \JOOST_COMMENT_HACKS_PATH . 'admin/views/comment-parent-box.php';
	}

	/**
	 * Adds the comment parent box to the meta box.
	 */
	public function load_comment_parent_box() {
		if ( \function_exists( 'add_meta_box' ) ) {
			\add_meta_box(
				'comment_parent',
				'Comment Parent',
				[
					$this,
					'comment_parent_box',
				],
				'comment',
				'normal'
			);
		}
	}

	/**
	 * Updates the comment parent field.
	 */
	public function update_comment_parent() {
		$comment_parent = \filter_input( \INPUT_POST, 'yst_comment_parent', \FILTER_VALIDATE_INT );
		$comment_id     = \filter_input( \INPUT_POST, 'comment_ID', \FILTER_VALIDATE_INT );
		$action         = \filter_input( \INPUT_POST, 'action' );

		if ( $action === 'edit-comment' ) {
			return; // We're on the quick edit screen. As the comment parent isn't sent along here, we might lose it if we do anything.
		}

		if ( empty( $comment_id ) && empty( $comment_parent ) ) {
			return; // There might be another reason for a comment to be updated.
		}

		if ( \defined( 'DOING_AJAX' ) && \DOING_AJAX === true ) {
			\check_ajax_referer( 'replyto-comment', '_ajax_nonce-replyto-comment' );
		}

		if ( ! \defined( 'DOING_AJAX' ) || \DOING_AJAX !== true ) {
			\check_admin_referer( 'update-comment_' . $comment_id );
		}

		if ( ! isset( $comment_parent ) ) {
			$comment_parent = 0;
		}

		if ( $comment_id ) {
			$comment                 = \get_comment( $comment_id );
			$comment->comment_parent = $comment_parent;

			// Remove our filter, or we'll keep looping.
			\remove_action( 'edit_comment', [ $this, 'update_comment_parent' ] );
			\wp_update_comment( (array) $comment );

			// Add our filter back.
			\add_action( 'edit_comment', [ $this, 'update_comment_parent' ] );
		}
	}
}
