<?php
/**
 * Add comment note.
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
