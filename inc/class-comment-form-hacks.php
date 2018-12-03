<?php
/**
 * Comment Hacks file. Deals with changes to the comment form.
 *
 * @package YoastCommentHacks
 */

/**
 * Class YoastCommentFormHacks
 *
 * @since 1.3
 */
class YoastCommentFormHacks {
	/**
	 * Holds the plugins options.
	 *
	 * @var array
	 */
	private $options = array();

	/**
	 * YoastCommentFormHacks constructor.
	 */
	public function __construct() {
		$this->options = YoastCommentHacks::get_options();

		add_action( 'comment_form_after_fields', array( $this, 'comment_form_fields' ) );
		add_filter( 'preprocess_comment', array( $this, 'check_comment_policy' ), 10, 1 );
	}

	/**
	 * Adds the comment policy checkbox to the comment form.
	 *
	 * @return void
	 */
	public function comment_form_fields() {
		if ( $this->options['comment_policy'] ) {
			echo '<label class="agree-comment-policy">';
			echo '<input type="checkbox" name="comment_policy">';
			echo '<a href="' . esc_url( get_permalink( $this->options['comment_policy_page'] ) ) . '">';
			echo esc_html( $this->options['comment_policy_text'] );
			echo '</a>';
			echo '</label>';
		}
	}

	/**
	 * Checks whether the comment policy box was checked or not.
	 *
	 * @param array $comment_data Array with comment data.
	 *
	 * @return array
	 */
	public function check_comment_policy( $comment_data ) {
		// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- Comment forms (unfortunately) are always without nonces.
		if ( ! isset( $_POST['comment_policy'] ) && ( $_POST['comment_policy'] !== 'on' || $_POST['comment_policy'] !== true ) ) {
			wp_die( esc_html( $this->options['comment_policy_error'] ) . '<br /><a href="javascript:history.go(-1);">' . esc_html__( 'Go back and try again.', 'yoast-comment-hacks' ) . '</a>' );
		}

		return $comment_data;
	}
}
