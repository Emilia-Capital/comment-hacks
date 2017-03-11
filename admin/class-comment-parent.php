<?php

/**
 * @package YoastCommentHacks\Admin
 */

/**
 * Class YoastCommentParent
 *
 * @since 1.3
 */
class YoastCommentParent {

	/**
	 * Class constructor
	 */
	public function __construct() {
		// The hooks for editing and saving the comment parent.
		add_action( 'admin_menu', array( $this, 'load_comment_parent_box' ) );
		add_action( 'edit_comment', array( $this, 'update_comment_parent' ) );
	}

	/**
	 * Shows the comment parent box where you can change the comment parent
	 *
	 * @param object $comment The comment object.
	 */
	public function comment_parent_box( $comment ) {
		require_once 'views/comment-parent-box.php';
	}

	/**
	 * Adds the comment parent box to the meta box
	 */
	public function load_comment_parent_box() {
		if ( function_exists( 'add_meta_box' ) ) {
			add_meta_box( 'comment_parent', 'Comment Parent', array(
				$this,
				'comment_parent_box',
			), 'comment', 'normal' );
		}
	}

	/**
	 * Updates the comment parent field
	 */
	public function update_comment_parent() {
		$comment_parent = filter_input( INPUT_POST, 'yst_comment_parent', FILTER_VALIDATE_INT );
		$comment_id     = filter_input( INPUT_POST, 'comment_ID', FILTER_VALIDATE_INT );

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX === true ) {
			check_ajax_referer( 'replyto-comment', '_ajax_nonce-replyto-comment' );
		}

		if ( ! defined( 'DOING_AJAX' ) || DOING_AJAX !== true ) {
			check_admin_referer( 'update-comment_' . $comment_id );
		}

		if ( ! isset( $comment_parent ) ) {
			$comment_parent = 0;
		}

		if ( $comment_id ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->comments SET comment_parent = %d WHERE comment_ID = %d", $comment_parent, $comment_id ) );
		}
	}
}
