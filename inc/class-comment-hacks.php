<?php
/**
 * Comment Hacks main class.
 *
 * @package YoastCommentHacks
 */

/**
 * Class YoastCommentHacks
 *
 * @since 1.0
 */
class YoastCommentHacks {
	/**
	 * Holds the plugins option name.
	 *
	 * @var string
	 */
	public static $option_name = 'yoast_comment_hacks';

	/**
	 * Holds the plugins options.
	 *
	 * @var array
	 */
	private $options = array();

	/**
	 * The absolute minimum comment length when this plugin is enabled.
	 *
	 * @var int
	 */
	private $absolute_min = 0;

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->options = self::get_options();
		$this->set_defaults();
		$this->upgrade();

		// Hook into init for registration of the option.
		add_action( 'admin_init', array( $this, 'init' ) );

		// Filter the redirect URL.
		add_filter( 'comment_post_redirect', array( $this, 'comment_redirect' ), 10, 2 );


		if ( $this->options['clean_emails'] ) {
			new YoastCleanEmails();
		}

		if ( is_admin() ) {
			new Yoast_Comment_Hacks_Admin();
		}

		new YoastCommentNotifications();
		new YoastCommentHacksEmailLinks();
		new YoastCommentFormHacks();
		new YoastCommentLength();
	}

	/**
	 * Register the text domain and the options array along with the validation function.
	 *
	 * @return void
	 */
	public function init() {
		// Register our option array.
		register_setting( self::$option_name, self::$option_name, array( $this, 'sanitize' ) );
	}

	/**
	 * Returns the comment hacks options.
	 *
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
		$has_approved_comment = get_comments(
			array(
				'author_email' => $comment->comment_author_email,
				'number'       => 1,
				'status'       => 'approve',
			)
		);

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
	 * @return bool|mixed Either the option, or false.
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
	 *
	 * @return void
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

		update_option( self::$option_name, $this->options );
	}

	/**
	 * Returns the default settings
	 *
	 * @return array The plugins defaults.
	 */
	public static function get_defaults() {
		return array(
			'clean_emails'         => true,
			'comment_policy'       => false,
			'comment_policy_text'  => __( 'I agree to the comment policy.', 'yoast-comment-hacks' ),
			'comment_policy_error' => __( 'You have to agree to the comment policy.', 'yoast-comment-hacks' ),
			'comment_policy_page'  => 0,
			/* translators: %s expands to the post title */
			'email_subject'        => sprintf( __( 'RE: %s', 'yoast-comment-hacks' ), '%title%' ),
			/* translators: %1$s expands to the commenters first name, %2$s to the post tittle, %3$s to the post permalink, %4$s expands to a double line break. */
			'email_body'           => sprintf( __( 'Hi %1$s,%4$sI\'m emailing you because you commented on my post "%2$s" - %3$s', 'yoast-comment-hacks' ), '%firstname%', '%title%', '%permalink%', "\r\n\r\n" ) . "\r\n",
			/* translators: %1$s expands to the the post tittle, %2$s to the post permalink, %3$s expands to a double line break. */
			'mass_email_body'      => sprintf( __( 'Hi,%3$sI\'m sending you all this email because you commented on my post "%1$s" - %2$s', 'yoast-comment-hacks' ), '%title%', '%permalink%', "\r\n\r\n" ) . "\r\n",
			'mincomlength'         => 15,
			'mincomlengtherror'    => __( 'Error: Your comment is too short. Please try to say something useful.', 'yoast-comment-hacks' ),
			'maxcomlength'         => 1500,
			'maxcomlengtherror'    => __( 'Error: Your comment is too long. Please try to be more concise.', 'yoast-comment-hacks' ),
			'redirect_page'        => 0,
		);
	}

	/**
	 * Set default values for the plugin. If old, as in pre 1.0, settings are there, use them and then delete them.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function set_defaults() {
		$this->options = wp_parse_args( $this->options, self::get_defaults() );

		update_option( self::$option_name, $this->options );
	}

	/**
	 * Validate the input, make sure comment length is an integer and above the minimum value.
	 *
	 * @since 1.0
	 *
	 * @param mixed $input The sanitized option value.
	 *
	 * @return array $input with validated options.
	 */
	public function sanitize( $input ) {
		$defaults = YoastCommentHacks::get_defaults();

		if ( ! is_array( $input ) ) {
			return $defaults;
		}

		foreach ( $input as $key => $value ) {
			switch ( $key ) {
				case 'mincomlength':
				case 'maxcomlength':
				case 'redirect_page':
					$input[ $key ] = (int) $value;
					break;
				case 'version':
					$input[ $key ] = YOAST_COMMENT_HACKS_VERSION;
					break;
				case 'comment_policy':
				case 'clean_emails':
					$input[ $key ] = $this->sanitize_bool( $value );
					break;
				case 'email_subject':
				case 'email_body':
				case 'mass_email_body':
					$input[ $key ] = $this->sanitize_string( $value, $defaults[ $key ] );
					break;
			}
		}

		if ( ( $this->absolute_min + 1 ) > $input['mincomlength'] || empty( $input['mincomlength'] ) ) {
			/* translators: %d is replaced with the minimum number of characters */
			add_settings_error( self::$option_name, 'min_length_invalid', sprintf( __( 'The minimum length you entered is invalid, please enter a minimum length above %d.', 'yoast-comment-hacks' ), $this->absolute_min ) );
			$input['mincomlength'] = 15;
		}

		return $input;
	}

	/**
	 * Turns checkbox values into booleans.
	 *
	 * @param mixed $value The input value to cast to boolean.
	 *
	 * @return bool $value The boolean output value.
	 */
	private function sanitize_bool( $value ) {
		if ( $value ) {
			$value = true;
		}
		if ( empty( $value ) ) {
			$value = false;
		}

		return $value;
	}

	/**
	 * Turns empty string into defaults.
	 *
	 * @param mixed  $value   The input value.
	 * @param string $default The default value of the string.
	 *
	 * @return array $input The array with sanitized input values.
	 */
	private function sanitize_string( $value, $default ) {
		if ( '' === $value ) {
			$value = $default;
		}

		return $value;
	}
}
