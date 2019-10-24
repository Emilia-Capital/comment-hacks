<?php

namespace Yoast\WP\Comment\Inc;

/**
 * Add comment note.
 *
 * @since 1.3
 * @since 1.6.0 Class renamed from `YoastCommentFormHacks` to `Yoast\WP\Comment\Inc\Forms`.
 */
class Forms {

	/**
	 * Holds the plugins options.
	 *
	 * @var array
	 */
	private $options = array();

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->options = YoastCommentHacks::get_options();

		add_filter( 'comment_form_defaults', array( $this, 'filter_defaults' ) );
	}

	/**
	 * Filters the comment defaults.
	 *
	 * @param array $defaults The current defaults.
	 *
	 * @return array The filtered defaults.
	 */
	public function filter_defaults( $defaults ) {
		$defaults['comment_notes_before'] = '<span class="agree-comment-policy">You have to agree to the comment policy.</span>';

		return $defaults;
	}
}
