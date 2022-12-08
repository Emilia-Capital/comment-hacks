<?php

namespace JoostBlog\WP\Comment\Inc;

/**
 * Checks the comments for allowed length.
 */
class Length {

	/**
	 * Holds the plugins options.
	 */
	private array $options = [];

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->options = Hacks::get_options();

		// Process the comment and check it for length.
		\add_filter( 'preprocess_comment', [ $this, 'check_comment_length' ] );
	}

	/**
	 * Check the length of the comment and if it's too short: die.
	 *
	 * @since 1.0
	 *
	 * @param array $comment_data All the data for the comment.
	 *
	 * @return array All the data for the comment (only returned when the comment is long enough).
	 */
	public function check_comment_length( $comment_data ): array {
		// Bail early for editors and admins, they can leave short or long comments if they want.
		if ( \current_user_can( 'edit_posts' ) ) {
			return $comment_data;
		}

		$length = $this->get_comment_length( $comment_data['comment_content'] );

		// Check for comment length and die if too short or too long.
		$error = false;
		if ( $length < $this->options['mincomlength'] ) {
			$error = $this->options['mincomlengtherror'];
		}
		if ( $length > $this->options['maxcomlength'] ) {
			$error = $this->options['maxcomlengtherror'];
		}

		if ( $error ) {
			\wp_die( \esc_html( $error ) . '<br /><a href="javascript:history.go(-1);">' . \esc_html__( 'Go back and try again.', 'yoast-comment-hacks' ) . '</a>' );
		}
		return $comment_data;
	}

	/**
	 * Returns the comment length for a comment.
	 *
	 * @since 1.3
	 *
	 * @param string $comment The comment to determine length.
	 *
	 * @return int The length of the comment.
	 */
	private function get_comment_length( string $comment ): int {
		$comment = \trim( $comment );

		if ( \function_exists( 'mb_strlen' ) ) {
			return \mb_strlen( $comment, \get_bloginfo( 'charset' ) );
		}
		return \strlen( $comment );
	}
}
