<?php

namespace EmiliaProjects\WP\Comment\Inc;

use EmiliaProjects\WP\Comment\Admin\Admin;
use WP_Comment;

/**
 * Main comment hacks functionality.
 */
class Hacks {

	/**
	 * Holds the plugins option name.
	 */
	public static string $option_name = 'comment_hacks';

	/**
	 * Holds the plugins options.
	 *
	 * @var string[]
	 */
	private array $options = [];

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->options = self::get_options();
		if ( ! isset( $this->options['version'] ) || \EMILIA_COMMENT_HACKS_VERSION > $this->options['version'] ) {
			$this->set_defaults();
			$this->upgrade();
		}

		\add_action( 'init', [ $this, 'load_text_domain' ] );

		// Filter the redirect URL.
		\add_filter( 'comment_post_redirect', [ $this, 'comment_redirect' ], 10, 2 );

		\add_filter( 'edit_comment_link', [ $this, 'edit_comment_link' ], 10, 2 );

		// Hook into the block rendering process to modify the output of the comment-edit-link block.
		\add_filter( 'render_block', [ $this, 'modify_comment_edit_link_block' ], 90, 2 );

		// AJAX action to remove the comment URL.
		\add_action( 'wp_ajax_ch_remove_comment_url', [ $this, 'remove_comment_url' ] );

		\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_comment_block_scripts' ] );

		if ( $this->options['clean_emails'] ) {
			new Clean_Emails();
		}

		if ( \is_admin() || \wp_doing_ajax() ) {
			new Admin();
		}

		new Notifications();
		new Email_Links();
		new Forms();
		new Length();
	}

	/**
	 * AJAX handler to remove the URL from a comment.
	 *
	 * @return void
	 */
	public function remove_comment_url() {
		\check_ajax_referer( 'ch_remove_comment_url_nonce', 'nonce' );

		if ( ! isset( $_POST['commentId'] ) ) {
			\wp_send_json_error( \esc_html__( 'Comment ID not set.', 'comment-hacks' ) );
		}

		$comment_id = \intval( $_POST['commentId'] );

		if ( ! \current_user_can( 'edit_comment', $comment_id ) ) {
				\wp_send_json_error( \esc_html__( 'You do not have permission to edit this comment.', 'comment-hacks' ) );
		}

		$comment = \get_comment( $comment_id );

		if ( ! $comment ) {
				\wp_send_json_error( \esc_html__( 'Comment not found.', 'comment-hacks' ) );
		}

		// Remove the URL.
		$comment->comment_author_url = '';

		// Update the comment.
		\wp_update_comment(
			[
				'comment_ID'         => $comment_id,
				'comment_author_url' => $comment->comment_author_url,
			]
		);

		\wp_send_json_success( \esc_html__( 'URL removed successfully.', 'comment-hacks' ) );
	}

	/**
	 * Enqueue the necessary scripts.
	 *
	 * @return void
	 */
	public function enqueue_comment_block_scripts() {
		\wp_enqueue_script( 'ch-comment-block-edit', \plugin_dir_url( \EMILIA_COMMENT_HACKS_FILE ) . 'admin/assets/js/remove-url.js', [ 'jquery' ], '1.0.0', true );
		\wp_localize_script(
			'ch-comment-block-edit',
			'chCommentBlockEdit',
			[
				'ajax_url' => \admin_url( 'admin-ajax.php' ),
				'nonce'    => \wp_create_nonce( 'ch_remove_comment_url_nonce' ),
			]
		);
	}

	/**
	 * Modifies the output of the comment-edit-link block.
	 *
	 * @param string   $block_content The block content.
	 * @param string[] $block         The block data array.
	 *
	 * @return string Modified block content with the "Remove URL" option.
	 */
	public function modify_comment_edit_link_block( $block_content, $block ) {
		if ( isset( $block['blockName'] ) && $block['blockName'] === 'core/comment-edit-link' ) {
				\preg_match( '/c=(\d+)/', $block_content, $matches );

				$remove_url_link = $this->get_remove_comment_url_link( (int) $matches[1] );

			if ( ! empty( $remove_url_link ) ) {
				$block_content = \str_replace( '</div>', ' &nbsp; &middot; &nbsp; ' . $remove_url_link . '</div>', $block_content );
			}
		}

		return $block_content;
	}

	/**
	 * Returns the "Remove URL" link for a comment.
	 *
	 * @param int $comment_id The ID of the comment.
	 *
	 * @return string The HTML for the "Remove URL" link.
	 */
	public function get_remove_comment_url_link( $comment_id ) {
		$comment = \get_comment( $comment_id );

		if ( isset( $comment ) && $comment instanceof WP_Comment && ! empty( $comment->comment_author_url ) ) {
			return \sprintf(
				'<a href="#" class="comment-remove-url" data-comment-id="%d" aria-label="%s">%s</a>',
				\esc_attr( (string) $comment_id ),
				\esc_attr__( 'Remove URL from this comment', 'comment-hacks' ),
				\esc_html__( 'Remove URL', 'comment-hacks' ) . ' <code><small>' . htmlentities( $comment->comment_author_url ) . '</small></code>'
			);
		}

		return '';
	}

	/**
	 * Adds a link to remove the author's URL from the comment.
	 *
	 * @param string $link       The original edit comment link.
	 * @param int    $comment_id The ID of the comment.
	 *
	 * @return string The modified link HTML.
	 */
	public function edit_comment_link( $link, $comment_id ) {
		return $link . ' &nbsp; &middot; &nbsp; ' . $this->get_remove_comment_url_link( $comment_id );
	}

	/**
	 * Returns the comment hacks options.
	 *
	 * @return string[]
	 */
	public static function get_options(): array {
		$options = \get_option( self::$option_name );
		return \is_array( $options ) ? $options : [];
	}

	/**
	 * Use the same default WordPress core uses for a "from" email address.
	 *
	 * @return string
	 */
	private static function get_from_email_default(): string {
		// Code below taken from WP core's pluggable.php file.
		// Get the site domain and get rid of www.
		$sitename = \wp_parse_url( \network_home_url(), \PHP_URL_HOST );
		if ( $sitename === null ) {
			return '';
		}

		if ( \substr( $sitename, 0, 4 ) === 'www.' ) {
			$sitename = \substr( $sitename, 4 );
		}

		return 'wordpress@' . $sitename;
	}

	/**
	 * Check whether the current commenter is a first time commenter, if so, redirect them to the specified settings.
	 *
	 * @param string     $url     The original redirect URL.
	 * @param WP_Comment $comment The comment object.
	 *
	 * @return string The URL to be redirected to, altered if this was a first time comment.
	 */
	public function comment_redirect( string $url, WP_Comment $comment ): string {
		$has_approved_comment = \get_comments(
			[
				'author_email' => $comment->comment_author_email,
				'number'       => 1,
				'status'       => 'approve',
			]
		);

		// If no approved comments have been found, show the thank-you page.
		if ( empty( $has_approved_comment ) ) {
			// Only change $url when the page option is actually set and not zero.
			if ( isset( $this->options['redirect_page'] ) && $this->options['redirect_page'] !== 0 ) {
				$url = \get_permalink( (int) $this->options['redirect_page'] );

				/**
				 * Allow other plugins to hook in when the user is being redirected,
				 * for analytics calls or even to change the target URL.
				 *
				 * @param string $url     URL to which the first-time commenter will be redirected.
				 * @param object $comment The comment object.
				 *
				 * @since 1.6.0
				 */
				$url = \apply_filters( 'EmiliaProjects\WP\Comment\redirect', $url, $comment );
			}
		}

		return $url;
	}

	/**
	 * See if the option has been cached, if it is, return it, otherwise return false.
	 *
	 * @param string $option The option to check for.
	 *
	 * @return bool|string
	 *
	 * @since 1.3
	 */
	private function get_option_from_cache( string $option ) {
		$options = \wp_load_alloptions();
		return isset( $options[ $option ] ) ? $option : false;
	}

	/**
	 * Check whether any old options are in there and if so upgrade them.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	private function upgrade(): void {
		foreach ( [ 'MinComLengthOptions', 'min_comment_length_option', 'CommentRedirect' ] as $old_option ) {
			$old_option_values = $this->get_option_from_cache( $old_option );
			if ( \is_array( $old_option_values ) ) {
				if ( isset( $old_option_values['page'] ) ) {
					$old_option_values['redirect_page'] = $old_option_values['page'];
					unset( $old_option_values['page'] );
				}
				$this->options = \wp_parse_args( $this->options, $old_option_values );
				\delete_option( $old_option );
			}
		}

		if ( ! isset( $this->options['version'] ) ) {
			$this->options['clean_emails'] = true;
			$this->options['version']      = \EMILIA_COMMENT_HACKS_VERSION;
		}

		if ( ! isset( $this->options['disable_email_all_commenters'] ) ) {
			$this->options['disable_email_all_commenters'] = false;
		}

		\update_option( self::$option_name, $this->options );
	}

	/**
	 * Returns the default settings.
	 *
	 * @return string[]
	 */
	public static function get_defaults(): array {
		return [
			'clean_emails'                 => true,
			'comment_policy'               => false,
			'comment_policy_text'          => \__( 'I agree to the comment policy.', 'comment-hacks' ),
			'comment_policy_error'         => \__( 'You have to agree to the comment policy.', 'comment-hacks' ),
			'comment_policy_page'          => 0,
			'disable_email_all_commenters' => false,
			/* translators: %s expands to the post title */
			'email_subject'                => \sprintf( \__( 'RE: %s', 'comment-hacks' ), '%title%' ),
			/* translators: %1$s expands to the commenters first name, %2$s to the post tittle, %3$s to the post permalink, %4$s expands to a double line break. */
			'email_body'                   => \sprintf( \__( 'Hi %1$s,%4$sI\'m emailing you because you commented on my post "%2$s" - %3$s', 'comment-hacks' ), '%firstname%', '%title%', '%permalink%', "\r\n\r\n" ) . "\r\n",
			/* translators: %1$s expands to the post tittle, %2$s to the post permalink, %3$s expands to a double line break. */
			'mass_email_body'              => \sprintf( \__( 'Hi,%3$sI\'m sending you all this email because you commented on my post "%1$s" - %2$s', 'comment-hacks' ), '%title%', '%permalink%', "\r\n\r\n" ) . "\r\n",
			'mincomlength'                 => 15,
			'mincomlengtherror'            => \__( 'Error: Your comment is too short. Please try to say something useful.', 'comment-hacks' ),
			'maxcomlength'                 => 1500,
			'maxcomlengtherror'            => \__( 'Error: Your comment is too long. Please try to be more concise.', 'comment-hacks' ),
			'redirect_page'                => 0,
			'forward_email'                => '',
			'forward_name'                 => \__( 'Support', 'comment-hacks' ),
			/* translators: %1$s is replaced by the blog's name. */
			'forward_subject'              => \sprintf( \__( 'Comment forwarded from %1$s', 'comment-hacks' ), \get_bloginfo( 'name' ) ),
			'forward_from_email'           => self::get_from_email_default(),
		];
	}

	/**
	 * Set default values for the plugin. If old, as in pre 1.0, settings are there, use them and then delete them.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function set_defaults(): void {
		$this->options = \wp_parse_args( $this->options, self::get_defaults() );

		\update_option( self::$option_name, $this->options );
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @return void
	 */
	public function load_text_domain(): void {
		\load_plugin_textdomain( 'comment-hacks', false, \dirname( \plugin_basename( \EMILIA_COMMENT_HACKS_FILE ) ) . '/languages' );
	}
}
