<?php

/**
 * @package YoastCommentHacks
 */

/**
 * Class YoastCommentFormHacks
 *
 * @since 1.3
 */
class YoastCommentFormHacks {

	/**
	 * @var array Holds the plugins options
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
	 * @param array $defaults
	 *
	 * @return array
	 */
	public function filter_defaults( $defaults ) {
		$defaults['comment_notes_before'] = 'You have to agree to the comment policy.';

		return $defaults;
	}
}
