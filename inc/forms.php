<?php

namespace JoostBlog\WP\Comment\Inc;

/**
 * Add comment note.
 */
class Forms {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		\add_filter( 'comment_form_defaults', [ $this, 'filter_defaults' ] );
	}

	/**
	 * Filters the comment defaults.
	 *
	 * @param array $defaults The current defaults.
	 *
	 * @return array The filtered defaults.
	 */
	public function filter_defaults( $defaults ): array {
		$defaults['comment_notes_before'] = '<span class="agree-comment-policy">You have to agree to the comment policy.</span>';

		return $defaults;
	}
}
