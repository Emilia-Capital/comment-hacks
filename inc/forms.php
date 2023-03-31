<?php

namespace JoostBlog\WP\Comment\Inc;

/**
 * Add comment note.
 */
class Forms {

	/**
	 * Holds our options.
	 */
	private array $options;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->options = Hacks::get_options();
		Hacks::get_defaults();

		if ( $this->options['comment_policy'] ) {
			\add_action( 'comment_form_after_fields', [ $this, 'comment_form_fields' ] );
			\add_filter( 'preprocess_comment', [ $this, 'check_comment_policy' ] );
		}
	}

	/**
	 * Adds the comment policy checkbox to the comment form.
	 *
	 * @return void
	 */
	public function comment_form_fields() {
		echo '<label class="agree-comment-policy">';
		echo '<input type="checkbox" name="comment_policy">';
		echo ' <a href="' . \esc_url( \get_permalink( $this->options['comment_policy_page'] ) ) . '" target="_blank">';
		echo esc_html( $this->options['comment_policy_text'] );
		echo '</a>';
		echo '</label>';
	}

	/**
	 * Checks whether the comment policy box was checked or not.
	 *
	 * @param array $comment_data Array with comment data. Unused.
	 *
	 * @return array
	 */
	public function check_comment_policy( $comment_data ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Comment forms (unfortunately) are always without nonces.
		if ( ! isset( $_POST['comment_policy'] ) || ! ( $_POST['comment_policy'] === 'on' || $_POST['comment_policy'] === true ) ) {
			\wp_die( \esc_html( $this->options['comment_policy_error'] ) . '<br /><br /><a href="javascript:history.go(-1);">' . \esc_html__( 'Go back and try again.', 'yoast-comment-hacks' ) . '</a>' );
		}

		return $comment_data;
	}
}
