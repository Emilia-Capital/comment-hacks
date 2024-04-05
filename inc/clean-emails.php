<?php

namespace EmiliaProjects\WP\Comment\Inc;

/**
 * Clean the emails.
 */
class Clean_Emails {

	/**
	 * Holds the current comment's ID.
	 */
	private int $comment_id = 0;

	/**
	 * Holds the current comment.
	 */
	private \WP_Comment $comment;

	/**
	 * Holds the comment's post.
	 */
	private \WP_Post $post;

	/**
	 * Holds the email message.
	 */
	private string $message;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		\add_filter( 'comment_notification_text', [ $this, 'comment_notification_text' ], 10, 2 );
		\add_filter( 'comment_moderation_text', [ $this, 'comment_moderation_text' ], 10, 2 );

		\add_filter( 'comment_notification_headers', [ $this, 'comment_email_headers' ] );
		\add_filter( 'comment_moderation_headers', [ $this, 'comment_email_headers' ] );
	}

	/**
	 * Set the comment email headers to HTML.
	 *
	 * @param string $message_headers The message headers for the comment email.
	 *
	 * @return string Not hard typed because core may throw something else in.
	 */
	public function comment_email_headers( $message_headers ) {
		if ( $message_headers !== null && \is_scalar( $message_headers ) === false ) {
			// Some other plugin must be doing it wrong, bow out.
			return $message_headers;
		}

		if ( $message_headers === null
			|| \is_string( $message_headers ) === false
			|| $message_headers === ''
		) {
			return 'Content-Type: text/html; charset="' . \get_option( 'blog_charset' ) . "\"\n";
		}

		if ( \strpos( $message_headers, 'Content-Type: ' ) === false ) {
			$message_headers  = \rtrim( $message_headers, "\r\n" ) . "\n";
			$message_headers .= 'Content-Type: text/html; charset="' . \get_option( 'blog_charset' ) . "\"\n";
			return $message_headers;
		}

		return \str_replace( 'Content-Type: text/plain', 'Content-Type: text/html', $message_headers );
	}

	/**
	 * Clean up the comment notification message.
	 *
	 * @param string $message    The comment notification message.
	 * @param int    $comment_id The ID of the comment the notification is for.
	 */
	public function comment_notification_text( $message, $comment_id ): string {
		$this->setup_data( $comment_id );

		$comment_url  = \get_permalink( (int) $this->comment->comment_post_ID ) . '#comment-' . $comment_id;
		$comment_link = '<a href="' . \esc_url( $comment_url ) . '">' . \esc_html( $this->post->post_title ) . '</a>';

		switch ( $this->comment->comment_type ) {
			case 'pingback':
				/* translators: %s is replaced with the post title */
				$this->message = \sprintf( \esc_html__( 'New pingback on "%s"', 'comment-hacks' ), $comment_link ) . '<br /><br />';
				break;
			case 'trackback':
				/* translators: %s is replaced with the post title */
				$this->message = \sprintf( \esc_html__( 'New trackback on "%s"', 'comment-hacks' ), $comment_link ) . '<br /><br />';
				break;
			default:
				/* translators: %s is replaced with the post title */
				$this->message = \sprintf( \esc_html__( 'New comment on "%s"', 'comment-hacks' ), $comment_link ) . '<br /><br />';
				break;
		}
		$this->add_comment_basics();

		if ( \user_can( (int) $this->post->post_author, 'edit_comment', $comment_id ) ) {
			$this->comment_notification_actions();
		}

		return $this->wrap_message();
	}

	/**
	 * Clean up the comment moderation message.
	 *
	 * @param string $message    The comment moderation message.
	 * @param int    $comment_id The ID of the comment the moderation notification is for.
	 */
	public function comment_moderation_text( $message, $comment_id ): string {
		$this->setup_data( $comment_id );

		$comment_link = '<a href="' . \esc_url( \get_permalink( (int) $this->comment->comment_post_ID ) ) . '">' . \esc_html( $this->post->post_title ) . '</a>';

		switch ( $this->comment->comment_type ) {
			case 'pingback':
				$this->message = \sprintf(
					/* translators: %1$s is replaced with the post title */
					\esc_html__( 'A new pingback on the post "%1$s" is waiting for your approval:', 'comment-hacks' ),
					$comment_link
				) . '<br /><br />';
				break;
			case 'trackback':
				$this->message = \sprintf(
					/* translators: %1$s is replaced with the post title */
					\esc_html__( 'A new trackback on the post "%1$s" is waiting for your approval:', 'comment-hacks' ),
					$comment_link
				) . '<br /><br />';
				break;
			default:
				$this->message = \sprintf(
					/* translators: %1$s is replaced with the post title */
					\esc_html__( 'A new comment on the post "%1$s" is waiting for your approval:', 'comment-hacks' ),
					$comment_link
				) . '<br /><br />';
				break;
		}
		$this->add_comment_basics();

		$this->comment_moderation_actions();
		$this->message .= ' | ' . \sprintf(
			'<a href="http://ip-lookup.net/index.php?ip=%1$s">%2$s</a>',
			$this->comment->comment_author_IP,
			\esc_html__( 'Whois', 'comment-hacks' )
		);
		$this->message .= '<br/><br/>';

		$this->get_moderation_msg();

		return $this->wrap_message();
	}

	/**
	 * Adds the basics of the email.
	 */
	private function add_comment_basics(): void {
		$this->add_author_line();
		$this->add_url_line();
		$this->message .= '<br/>';
		$this->add_content_line();
	}

	/**
	 * Adds the author line to the message.
	 */
	private function add_author_line(): void {
		if ( $this->comment->comment_type === 'comment' ) {
			/* translators: %1$s is replaced with the comment author's name, %2$s is replaced with the comment author's email */
			$this->message .= \sprintf( \esc_html__( 'Author: %1$s (%2$s)', 'comment-hacks' ), \esc_html( $this->comment->comment_author ), '<a href="' . \esc_url( 'mailto:' . $this->comment->comment_author_email ) . '">' . \esc_html( $this->comment->comment_author_email ) . '</a>' ) . '<br />';
		}
		else {
			/* translators: %1$s is replaced with the website doing the ping or trackback */
			$this->message .= \sprintf( \esc_html__( 'Website: %1$s', 'comment-hacks' ), \esc_html( $this->comment->comment_author ) ) . '<br>';
		}
	}

	/**
	 * Adds the content line to the message.
	 */
	private function add_content_line(): void {
		if ( $this->comment->comment_type === 'comment' ) {
			$this->message .= \esc_html__( 'Comment:', 'comment-hacks' );
		}
		else {
			$this->message .= \esc_html__( 'Excerpt:', 'comment-hacks' );
		}

		$this->message .= '<br />' . \wpautop( $this->comment->comment_content ) . '<br />';
	}

	/**
	 * Adds the URL line to the message.
	 */
	private function add_url_line(): void {
		if ( isset( $this->comment->comment_author_url ) && $this->comment->comment_author_url !== '' ) {
			/* translators: %s is replaced with the URL */
			$this->message .= \sprintf( \esc_html__( 'URL: %s', 'comment-hacks' ), '<a href="' . \esc_url( $this->comment->comment_author_url ) . '">' . \esc_html( $this->comment->comment_author_url ) . '</a>' ) . '<br/>';
		}
	}

	/**
	 * Wraps the message in some styling.
	 */
	private function wrap_message(): string {
		return '<div style="font-family:Helvetica,Arial,sans-serif; font-size:14px;">' . $this->message . '</div>';
	}

	/**
	 * Sets up class variables used with all emails.
	 *
	 * @param int $comment_id The comment we're setting up variables for.
	 */
	private function setup_data( int $comment_id ): void {
		$this->comment_id = $comment_id;
		$this->comment    = \get_comment( $this->comment_id );
		$this->post       = \get_post( (int) $this->comment->comment_post_ID );
	}

	/**
	 * Adds a sentence about the number of comments awaiting moderation.
	 */
	private function get_moderation_msg(): void {
		$comments_waiting = \get_comment_count()['awaiting_moderation'];

		if ( $comments_waiting > 1 ) {
			--$comments_waiting;
			$this->message .= \sprintf(
				/* translators: %s is replaced with the number of comments waiting for approval */
				\esc_html__( 'Currently this and %s other comments are waiting for approval.', 'comment-hacks' ),
				\number_format_i18n( $comments_waiting )
			);
			$this->message .= ' ';
			$this->message .= \sprintf(
				/* translators: %s is replaced with the HTML for a link to the moderation panel, with text "moderation panel". */
				\esc_html__( 'Please visit the %s.', 'comment-hacks' ),
				'<a href="' . \admin_url( 'edit-comments.php?comment_status=moderated' ) . '">' . \esc_html__( 'moderation panel', 'comment-hacks' ) . '</a>'
			) . '<br>';
		}
	}

	/**
	 * Returns a string containing comment moderation links.
	 */
	private function comment_moderation_actions(): void {
		$actions = [
			'approve'     => \esc_html__( 'Approve', 'comment-hacks' ),
			'spam'        => \esc_html__( 'Spam', 'comment-hacks' ),
			'trash'       => \esc_html__( 'Trash', 'comment-hacks' ),
			'editcomment' => \esc_html__( 'Edit', 'comment-hacks' ),
		];

		$this->comment_action_links( $actions );
	}

	/**
	 * Returns a string containing comment action links.
	 */
	private function comment_notification_actions(): void {
		$actions = [
			'spam'        => \esc_html__( 'Spam', 'comment-hacks' ),
			'trash'       => \esc_html__( 'Trash', 'comment-hacks' ),
			'editcomment' => \esc_html__( 'Edit', 'comment-hacks' ),
		];

		$this->comment_action_links( $actions );
	}

	/**
	 * Add action links to the message
	 *
	 * @param array $actions The array of actions we're adding our action for.
	 */
	private function comment_action_links( array $actions ): void {
		$links = '';
		foreach ( $actions as $action => $label ) {
			$links .= $this->comment_action_link( $label, $action ) . ' | ';
		}

		$links = \rtrim( $links, '| ' );

		$this->message .= $links;
	}

	/**
	 * Creates a comment action link.
	 *
	 * @param string $label  The label for the comment action link.
	 * @param string $action The action we're going to add.
	 */
	private function comment_action_link( string $label, string $action ): string {
		$url = \admin_url( \sprintf( 'comment.php?action=%s&c=%d', $action, $this->comment_id ) );

		return '<a href="' . \esc_url( $url ) . '">' . \esc_html( $label ) . '</a>';
	}
}
