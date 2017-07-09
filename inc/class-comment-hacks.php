<?php
/**
 * @package YoastCommentHacks
 */

/**
 * Class YoastCommentHacks
 *
 * @since 1.0
 */
class YoastCommentHacks {

	/**
	 * @var string Holds the plugins option name
	 */
	public static $option_name = 'yoast_comment_hacks';

	/**
	 * @var array Holds the plugins options
	 */
	private $options = array();

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->options = self::get_options();
		$this->set_defaults();
		$this->upgrade();

		// Filter the redirect URL.
		add_filter( 'comment_post_redirect', array( $this, 'comment_redirect' ), 10, 2 );

		if ( $this->options['clean_emails'] ) {
			new YoastCleanEmails();
		}

		if ( is_admin() ) {
			new YoastCommentHacksAdmin();
		}

		new YoastCommentNotifications();
		new YoastCommentHacksEmailLinks();
		new YoastCommentFormHacks();
		new YoastCommentLength();
	}

	/**
	 * Returns the comment hacks options
	 * @return array
	 */
	public static function get_options() {
		return get_option( self::$option_name );
	}

	/**
	 * Check whether the current commenter is a first time commenter, if so, redirect them to the specified settings.
	 *
	 * @since 1.0
	 *
	 * @param string $url     The original redirect URL.
	 * @param object $comment The comment object.
	 *
	 * @return string $url the URL to be redirected to, altered if this was a first time comment.
	 */
	public function comment_redirect( $url, $comment ) {
		$has_approved_comment = get_comments( array( 'author_email' => $comment->comment_author_email, 'number' => 1, 'status' => 'approve' ) );

		// If no approved comments have been found, show the thank-you page.
		if ( empty( $has_approved_comment ) ) {
			// Only change $url when the page option is actually set and not zero.
			if ( isset( $this->options['redirect_page'] ) && 0 != $this->options['redirect_page'] ) {
				$url = get_permalink( $this->options['redirect_page'] );

				// Allow other plugins to hook when the user is being redirected, for analytics calls or even to change the target URL.
				$url = apply_filters( 'yoast_comment_redirect', $url, $comment );
			}
		}

		return $url;
	}

	/**
	 * See if the option has been cached, if it is, return it, otherwise return false.
	 *
	 * @param string $option The option to check for.
	 *
	 * @since 1.3
	 *
	 * @return bool|mixed
	 */
	private function get_option_from_cache( $option ) {
		$options = wp_load_alloptions();
		if ( isset( $options[ $option ] ) ) {
			return $option;
		}
		return false;
	}

	/**
	 * Check whether any old options are in there and if so upgrade them
	 *
	 * @since 1.0
	 */
	private function upgrade() {
		foreach ( array( 'MinComLengthOptions', 'min_comment_length_option', 'CommentRedirect' ) as $old_option ) {
			$old_option_values = $this->get_option_from_cache( $old_option );
			if ( is_array( $old_option_values ) ) {
				if ( isset( $old_option_values['page'] ) ) {
					$old_option_values['redirect_page'] = $old_option_values['page'];
					unset( $old_option_values['page'] );
				}
				$this->options = wp_parse_args( $this->options, $old_option_values );
				delete_option( $old_option );
			}
		}

		if ( ! isset( $this->options['version'] ) ) {
			$this->options['clean_emails'] = true;
			$this->options['version']      = YOAST_COMMENT_HACKS_VERSION;
		}

		update_option( YoastCommentHacks::$option_name, $this->options );
	}

	/**
	 * Returns the default settings
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return array(
			'clean_emails'      => true,
			/* translators: %s expands to the post title */
			'email_subject'     => sprintf( __( 'RE: %s', 'yoast-comment-hacks' ), '%title%' ),
			/* translators: %1$s expands to the commenters first name, %2$s to the post tittle, %3$s to the post permalink, %4$s expands to a double line break. */
			'email_body'        => sprintf( __( 'Hi %1$s,%4$sI\'m emailing you because you commented on my post "%2$s" - %3$s', 'yoast-comment-hacks' ), '%firstname%', '%title%', '%permalink%', "\r\n\r\n" ) . "\r\n",
			/* translators: %1$s expands to the the post tittle, %2$s to the post permalink, %3$s expands to a double line break. */
			'mass_email_body'   => sprintf( __( 'Hi,%3$sI\'m sending you all this email because you commented on my post "%1$s" - %2$s', 'yoast-comment-hacks' ), '%title%', '%permalink%', "\r\n\r\n" ) . "\r\n",
			'mincomlength'      => 15,
			'mincomlengtherror' => __( 'Error: Your comment is too short. Please try to say something useful.', 'yoast-comment-hacks' ),
			'maxcomlength'      => 1500,
			'maxcomlengtherror' => __( 'Error: Your comment is too long. Please try to be more concise.', 'yoast-comment-hacks' ),
			'redirect_page'     => 0,
		);
	}

	/**
	 * Set default values for the plugin. If old, as in pre 1.0, settings are there, use them and then delete them.
	 *
	 * @since 1.0
	 */
	public function set_defaults() {
		$this->options = wp_parse_args( $this->options, self::get_defaults() );

		update_option( YoastCommentHacks::$option_name, $this->options );
	}
}
