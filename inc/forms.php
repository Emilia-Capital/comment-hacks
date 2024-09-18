<?php

namespace EmiliaProjects\WP\Comment\Inc;

/**
 * Add comment note.
 */
class Forms {

	/**
	 * Holds our options.
	 *
	 * @var string[]
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
		?>
		<label class="agree-comment-policy">
			<input type="checkbox" name="comment_policy">
			<a href="<?php echo \esc_url( \get_permalink( $this->options['comment_policy_page'] ) ); ?>" target="_blank">
				<?php echo \esc_html( $this->options['comment_policy_text'] ); ?>
			</a>
		</label>
		<?php
	}

	/**
	 * Checks whether the comment policy box was checked or not.
	 *
	 * @param string[] $comment_data Array with comment data. Unused.
	 *
	 * @return string[]
	 */
	public function check_comment_policy( $comment_data ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Comment forms (unfortunately) are always without nonces.
		if ( ! isset( $_POST['comment_policy'] ) || ! ( $_POST['comment_policy'] === 'on' || $_POST['comment_policy'] === true ) ) {
			\wp_die( \esc_html( $this->options['comment_policy_error'] ) . '<br /><br /><a href="javascript:history.go(-1);">' . \esc_html__( 'Go back and try again.', 'comment-hacks' ) . '</a>' );
		}

		return $comment_data;
	}
}
